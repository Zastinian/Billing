import { Like } from "typeorm";
import { Credits } from "@/database/entities/Credits";
import { clients, credits, invoices, jobs, planCycles, plans, servers } from "@/database/index";
import parseEntities from "@/utils/parseEntities";
import { createPterodactylServer, getPterodactylSettings } from "@/utils/pterodactylServer";
import { invoiceStatus, serverStatus } from "@/utils/status";

interface ServerCreationResult {
  success: boolean;
  serverId?: number;
  pterodactylId?: number;
  error?: string;
}

export const createServerFromPayPalInvoice = async (
  invoiceId: number,
): Promise<ServerCreationResult> => {
  try {
    const invoice = await invoices.findOneBy({ id: invoiceId });
    if (!invoice) {
      return { success: false, error: "Invoice not found" };
    }

    if (!invoice.serverId) {
      return { success: false, error: "Invoice has no associated server" };
    }

    const server = await servers.findOneBy({ id: invoice.serverId });
    if (!server) {
      return { success: false, error: "Server not found" };
    }

    if (server.serverId && server.status === serverStatus.active) {
      console.log(`Server ${server.id} already exists (Pterodactyl ID: ${server.serverId})`);
      return { success: true, serverId: server.id, pterodactylId: server.serverId };
    }

    const client = await clients.findOneBy({ id: server.clientId });
    const plan = await plans.findOneBy({ id: server.planId });
    const planCycle = await planCycles.findOneBy({ id: server.planCycle });

    if (!client || !plan || !planCycle) {
      return { success: false, error: "Missing client, plan, or plan cycle data" };
    }

    const jobPayload = await jobs.findOne({
      where: {
        queue: "create_server",
        payload: Like(`%${server.id}%`),
      },
    });

    if (!jobPayload) {
      return { success: false, error: "Server creation job not found" };
    }

    const payload = JSON.parse(jobPayload.payload);
    const egg = parseEntities(payload.egg);
    const node = parseEntities(payload.node);

    if (egg.length !== 1 || node.length !== 1) {
      return { success: false, error: "Invalid egg or node configuration" };
    }

    const pterodactylSettings = await getPterodactylSettings();
    if (!pterodactylSettings) {
      return { success: false, error: "Pterodactyl settings not configured" };
    }

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
      return { success: false, error: "Failed to fetch egg data from Pterodactyl" };
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
      return { success: false, error: "Failed to fetch node data from Pterodactyl" };
    }

    const allocations = nodeData.attributes.relationships.allocations.data.filter(
      (allocation: any) => allocation.attributes.assigned === false,
    );

    if (allocations.length <= 0) {
      return { success: false, error: "No available allocations on node" };
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

    if (!createServerResponse) {
      return { success: false, error: "Failed to create server in Pterodactyl" };
    }

    server.serverId = createServerResponse.id;
    server.identifier = createServerResponse.identifier;
    server.status = serverStatus.active;
    server.updatedAt = new Date();
    await servers.save(server);

    invoice.paid = invoiceStatus.paid;
    invoice.updatedAt = new Date();
    await invoices.save(invoice);

    await jobs.delete({ id: jobPayload.id });

    console.log(
      `Server ${server.id} created successfully from PayPal payment (Pterodactyl ID: ${createServerResponse.id})`,
    );

    return {
      success: true,
      serverId: server.id,
      pterodactylId: createServerResponse.id,
    };
  } catch (error) {
    console.error("Error creating server from PayPal invoice:", error);
    return { success: false, error: String(error) };
  }
};

export const refundInvoiceToClient = async (invoiceId: number): Promise<boolean> => {
  try {
    const invoice = await invoices.findOneBy({ id: invoiceId });
    if (!invoice) {
      console.error(`Invoice ${invoiceId} not found for refund`);
      return false;
    }

    const client = await clients.findOneBy({ id: invoice.clientId });
    if (!client) {
      console.error(`Client ${invoice.clientId} not found for refund`);
      return false;
    }

    const refundAmount = invoice.total;
    const credit = new Credits();
    credit.clientId = client.id;
    credit.details = `Refund for failed server creation (Invoice #${invoice.id})`;
    credit.change = refundAmount;
    credit.balance = client.credit + refundAmount;
    credit.createdAt = new Date();
    await credits.save(credit);

    client.credit += refundAmount;
    client.updatedAt = new Date();
    await clients.save(client);

    console.log(
      `Refunded ${refundAmount} credits to client ${client.id} for invoice ${invoice.id}`,
    );
    return true;
  } catch (error) {
    console.error("Error refunding invoice to client:", error);
    return false;
  }
};
