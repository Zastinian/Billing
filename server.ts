import "reflect-metadata";
import "./dist/server/entry.mjs";
import { IsNull, LessThan, Like, Not } from "typeorm";
import { Credits } from "./src/database/entities/Credits";
import {
  clients,
  credits,
  extensions,
  failedJobs,
  invoices,
  jobs,
  planCycles,
  plans,
  servers,
} from "./src/database/index";
import parseEntities from "./src/utils/parseEntities";
import {
  createPterodactylServer,
  deletePterodactylServer,
  getPterodactylSettings,
  suspendPterodactylServer,
} from "./src/utils/pterodactylServer";
import { cycleType, invoiceStatus, serverStatus } from "./src/utils/status";

const calculateDueDate = (cycleLength: number, cycleTypeNum: number): Date | null => {
  if (cycleTypeNum === cycleType.oneTime) {
    return null;
  }
  const now = new Date();
  switch (cycleTypeNum) {
    case cycleType.hourly:
      now.setHours(now.getHours() + cycleLength);
      break;
    case cycleType.daily:
      now.setDate(now.getDate() + cycleLength);
      break;
    case cycleType.monthly:
      now.setMonth(now.getMonth() + cycleLength);
      break;
    case cycleType.yearly:
      now.setFullYear(now.getFullYear() + cycleLength);
      break;
  }
  return now;
};

const suspendExpiredServers = async () => {
  const pterodactylSettings = await getPterodactylSettings();
  if (!pterodactylSettings) {
    return;
  }

  const now = new Date();
  const expiredServers = await servers.find({
    where: {
      status: serverStatus.active,
      dueDate: LessThan(now),
    },
  });

  for (const server of expiredServers) {
    const plan = await plans.findOneBy({ id: server.planId });
    if (!plan || !plan.daysBeforeSuspend) {
      continue;
    }

    const daysSinceExpired = Math.floor(
      (now.getTime() - new Date(server.dueDate!).getTime()) / (1000 * 60 * 60 * 24),
    );

    if (daysSinceExpired >= plan.daysBeforeSuspend) {
      if (server.serverId) {
        const suspended = await suspendPterodactylServer(server.serverId, pterodactylSettings);
        if (suspended) {
          server.status = serverStatus.suspended;
          server.updatedAt = new Date();
          await servers.save(server);
          console.log(`Server ${server.id} suspended (Pterodactyl ID: ${server.serverId})`);
        }
      }
    }
  }
};

const deleteTerminatedServers = async () => {
  const pterodactylSettings = await getPterodactylSettings();
  if (!pterodactylSettings) {
    return;
  }

  const now = new Date();
  const suspendedServers = await servers.find({
    where: {
      status: serverStatus.suspended,
    },
  });

  for (const server of suspendedServers) {
    const plan = await plans.findOneBy({ id: server.planId });
    if (!plan || !plan.daysBeforeDelete) {
      continue;
    }

    const daysSinceSuspended = Math.floor(
      (now.getTime() - new Date(server.updatedAt!).getTime()) / (1000 * 60 * 60 * 24),
    );

    if (daysSinceSuspended >= plan.daysBeforeDelete) {
      if (server.serverId) {
        const deleted = await deletePterodactylServer(server.serverId, pterodactylSettings);
        if (deleted) {
          server.status = serverStatus.terminated;
          server.updatedAt = new Date();
          await servers.save(server);
          console.log(`Server ${server.id} deleted (Pterodactyl ID: ${server.serverId})`);
        }
      }
    }
  }
};

const processFailedJobs = async () => {
  const pterodactylSettings = await getPterodactylSettings();
  if (!pterodactylSettings) {
    return;
  }

  const pendingServers = await servers.find({
    where: {
      status: serverStatus.pending,
      serverId: IsNull(),
    },
  });

  for (const server of pendingServers) {
    const relatedFailedJobs = await failedJobs.find({
      where: {
        payload: Like(`%${server.id}%`),
      },
    });

    if (relatedFailedJobs.length >= 3) {
      server.status = serverStatus.canceled;
      server.updatedAt = new Date();
      await servers.save(server);

      const client = await clients.findOneBy({ id: server.clientId });
      const planCycle = await planCycles.findOneBy({ id: server.planCycle });

      if (client && planCycle) {
        const refundAmount = planCycle.initPrice + planCycle.setupFee;
        const credit = new Credits();
        credit.clientId = client.id;
        credit.details = `Refund for failed server creation (Server ID: ${server.id})`;
        credit.change = refundAmount;
        credit.balance = client.credit + refundAmount;
        credit.createdAt = new Date();
        await credits.save(credit);

        client.credit += refundAmount;
        client.updatedAt = new Date();
        await clients.save(client);

        console.log(`Server ${server.id} canceled and refunded after 3 failed attempts`);
      }
    }
  }
};

const createApprovedServers = async () => {
  const pterodactylSettings = await getPterodactylSettings();
  if (!pterodactylSettings) {
    return;
  }

  const paidInvoices = await invoices.find({
    where: {
      paid: invoiceStatus.paid,
      serverId: Not(IsNull()),
    },
  });

  for (const invoice of paidInvoices) {
    const server = await servers.findOneBy({ id: invoice.serverId! });
    if (!server || server.serverId || server.status !== serverStatus.pending) {
      continue;
    }

    const plan = await plans.findOneBy({ id: server.planId });
    const planCycle = await planCycles.findOneBy({ id: server.planCycle });
    const client = await clients.findOneBy({ id: server.clientId });

    if (!plan || !planCycle || !client) {
      continue;
    }

    const jobPayload = await jobs.findOne({
      where: {
        queue: "create_server",
        payload: Like(`%${server.id}%`),
      },
    });

    if (!jobPayload) {
      continue;
    }

    const payload = JSON.parse(jobPayload.payload);
    const egg = parseEntities(payload.egg);
    const node = parseEntities(payload.node);

    if (egg.length !== 1 || node.length !== 1) {
      continue;
    }

    try {
      const eggData = await fetch(
        new URL(
          `/api/application/nests/${egg[0].id}/eggs/${egg[0].values}?include=variables`,
          pterodactylSettings.panelUrl,
        ).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${pterodactylSettings.panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      ).then((res) => (res.status === 200 ? res.json() : null));

      if (!eggData) {
        continue;
      }

      const nodeData = await fetch(
        new URL(
          `/api/application/nodes/${node[0].values[0]}?include=allocations`,
          pterodactylSettings.panelUrl,
        ).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${pterodactylSettings.panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      ).then((res) => (res.status === 200 ? res.json() : null));

      if (!nodeData) {
        continue;
      }

      const allocations = nodeData.attributes.relationships.allocations.data.filter(
        (allocation: any) => allocation.attributes.assigned === false,
      );

      if (allocations.length <= 0) {
        continue;
      }

      const nextAllocation = allocations[0].attributes.id;
      const environment: any = {};
      eggData.attributes.relationships.variables.data.forEach((variable: any) => {
        environment[variable.attributes.env_variable] = variable.attributes.default_value;
      });

      const createServerResponse = await createPterodactylServer(
        {
          name: server.serverName,
          description: plan.serverDescription || "",
          userId: client.userId || 0,
          eggId: eggData.attributes.id,
          dockerImage: eggData.attributes.docker_image,
          startup: eggData.attributes.startup,
          environment,
          limits: {
            memory: plan.ram,
            cpu: plan.cpu,
            disk: plan.disk,
            swap: plan.swap,
            io: plan.io,
          },
          featureLimits: {
            databases: plan.databases,
            backups: plan.backups,
            allocations: plan.extraPorts,
          },
          allocationId: nextAllocation,
        },
        pterodactylSettings,
      );

      if (createServerResponse) {
        server.serverId = createServerResponse.id;
        server.identifier = createServerResponse.identifier;
        server.status = serverStatus.active;
        server.dueDate = calculateDueDate(planCycle.cycleLength, planCycle.cycleType);
        server.updatedAt = new Date();
        await servers.save(server);

        await jobs.delete({ id: jobPayload.id });
        console.log(
          `Server ${server.id} created successfully (Pterodactyl ID: ${server.serverId})`,
        );
      }
    } catch (error) {
      console.error(`Error creating server ${server.id}:`, error);
    }
  }
};

const autoRenewServers = async () => {
  const now = new Date();
  const twoDaysFromNow = new Date(now.getTime() + 2 * 24 * 60 * 60 * 1000);

  const expiringServers = await servers.find({
    where: {
      status: serverStatus.active,
      dueDate: LessThan(twoDaysFromNow),
    },
  });

  for (const server of expiringServers) {
    const client = await clients.findOneBy({ id: server.clientId });
    if (!client || client.autoRenew !== 1) {
      continue;
    }

    const planCycle = await planCycles.findOneBy({ id: server.planCycle });
    if (!planCycle) {
      continue;
    }

    if (planCycle.cycleType === cycleType.oneTime) {
      continue;
    }

    if (client.credit >= planCycle.renewPrice) {
      const newDueDate = calculateDueDate(planCycle.cycleLength, planCycle.cycleType);

      const credit = new Credits();
      credit.clientId = client.id;
      credit.details = `Auto-renewal for server ${server.serverName}`;
      credit.change = -planCycle.renewPrice;
      credit.balance = client.credit - planCycle.renewPrice;
      credit.createdAt = new Date();
      await credits.save(credit);

      client.credit -= planCycle.renewPrice;
      client.updatedAt = new Date();
      await clients.save(client);

      server.dueDate = newDueDate;
      server.updatedAt = new Date();
      await servers.save(server);

      console.log(`Server ${server.id} auto-renewed for client ${client.id}`);
    }
  }
};

const verifyPayPalPayments = async () => {
  const webhookEnabled = await extensions
    .findOneBy({ extension: "PayPal", key: "webhook_enabled" })
    .then((e) => e?.value === "1");

  if (webhookEnabled) {
    return;
  }

  const { getPayPalSettings } = await import("./src/utils/paypal");
  const PayPal = (await import("./src/utils/paypal")).default;
  const { createServerFromPayPalInvoice, refundInvoiceToClient } = await import(
    "./src/utils/paypalServerCreation"
  );

  const paypalConfig = await getPayPalSettings();
  if (!paypalConfig) {
    console.log("PayPal configuration not found, skipping payment verification");
    return;
  }

  const paypal = new PayPal(paypalConfig);

  const unpaidPayPalInvoices = await invoices.find({
    where: {
      paid: invoiceStatus.pending,
      paymentMethod: "PayPal",
      paymentLink: Not(IsNull()),
    },
  });

  console.log(`Checking ${unpaidPayPalInvoices.length} unpaid PayPal invoices`);

  for (const invoice of unpaidPayPalInvoices) {
    try {
      if (!invoice.paymentLink) {
        continue;
      }

      const order = await paypal.getOrder(invoice.paymentLink);

      if (!order) {
        console.log(`PayPal order ${invoice.paymentLink} not found`);
        continue;
      }

      if (order.status === "APPROVED") {
        console.log(`PayPal order ${invoice.paymentLink} is approved, capturing payment`);

        const captureResult = await paypal.capturePayment(invoice.paymentLink);

        if (!captureResult || captureResult.status !== "COMPLETED") {
          console.error(`Failed to capture payment for invoice ${invoice.id}`);
          continue;
        }

        console.log(`Payment captured for invoice ${invoice.id}`);

        const result = await createServerFromPayPalInvoice(invoice.id);

        if (!result.success) {
          console.error(`Failed to create server for invoice ${invoice.id}:`, result.error);

          const refunded = await refundInvoiceToClient(invoice.id);

          if (refunded) {
            console.log(`Credits refunded to client for invoice ${invoice.id}`);
          } else {
            console.error(`Failed to refund credits for invoice ${invoice.id}`);
          }
        } else {
          console.log(
            `Server created from PayPal payment for invoice ${invoice.id} (Server ID: ${result.serverId}, Pterodactyl ID: ${result.pterodactylId})`,
          );
        }
      } else if (order.status === "COMPLETED") {
        console.log(`PayPal order ${invoice.paymentLink} already completed`);
        const result = await createServerFromPayPalInvoice(invoice.id);

        if (!result.success && result.error !== "Server already exists") {
          await refundInvoiceToClient(invoice.id);
        }
      }
    } catch (error) {
      console.error(`Error verifying PayPal payment for invoice ${invoice.id}:`, error);
    }
  }
};

setInterval(
  async () => {
    await suspendExpiredServers();
  },
  60 * 60 * 1000,
);

setInterval(
  async () => {
    await deleteTerminatedServers();
  },
  6 * 60 * 60 * 1000,
);

setInterval(
  async () => {
    await processFailedJobs();
  },
  30 * 60 * 1000,
);

setInterval(
  async () => {
    await createApprovedServers();
  },
  15 * 60 * 1000,
);

setInterval(
  async () => {
    await autoRenewServers();
  },
  60 * 60 * 1000,
);

setInterval(
  async () => {
    await verifyPayPalPayments();
  },
  15 * 60 * 1000,
);

console.log("Server management cron jobs initialized");
console.log("- Suspend expired servers: every 1 hour");
console.log("- Delete terminated servers: every 6 hours");
console.log("- Process failed jobs: every 30 minutes");
console.log("- Create approved servers: every 15 minutes");
console.log("- Auto-renew servers: every 1 hour");
console.log("- Verify PayPal payments: every 15 minutes");
