import { ActionError, defineAction } from "astro:actions";
import { z } from "astro:schema";
import { clients, credits, plans, planCycles, servers, settings } from "@/database/index";
import { Servers } from "@/database/entities/Servers";
import { Credits } from "@/database/entities/Credits";
import profile from "@/utils/profile";
import parseEntities, { isValidFormat } from "@/utils/parseEntities";
import { cycleType, serverStatus } from "@/utils/status";

let userAlreadyCreatingAServer: any[] = [];

export const server = {
  purchaseServer: defineAction({
    accept: "form",
    input: z.object({
      serverName: z.string({
        message: "Server name is required.",
      }),
      cycle: z.number({
        message: "Cycle is required.",
      }),
      egg: z.string({
        message: "Egg is required.",
      }),
      node: z.string({
        message: "Node is required.",
      }),
    }),
    handler: async (input, context) => {
      if (input.serverName.length < 3 || input.serverName.length > 20)
        throw new ActionError({ message: "Server name is invalid.", code: "CONFLICT" });
      if (!isValidFormat(input.egg))
        throw new ActionError({ message: "Egg is invalid.", code: "CONFLICT" });
      if (!isValidFormat(input.node))
        throw new ActionError({ message: "Node is invalid.", code: "CONFLICT" });
      const cookies = context.request.headers.get("cookie");
      if (!cookies) throw new Error("Session error.");
      const cookie = cookies
        .split(";")
        .find((token) => token.includes("_SECURE_SESSION_TOKEN_"))
        ?.split("=")[1]
        ?.trim();
      if (!cookie) throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      const c = profile(cookie);
      if (!c.clientId) throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      if (userAlreadyCreatingAServer.includes(c.clientId)) {
        throw new ActionError({ message: "Server is being created.", code: "CONFLICT" });
      }
      userAlreadyCreatingAServer.push(c.clientId);
      const client = await clients.findOneBy({ id: c.clientId });
      if (!client) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
      const planCycle = await planCycles.findOneBy({ id: Number(input.cycle) });
      if (!planCycle) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Plan not found.", code: "NOT_FOUND" });
      }
      if (client.credit < planCycle.initPrice + planCycle.setupFee) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Insufficient credits.", code: "CONFLICT" });
      }
      const plan = await plans.findOneBy({ id: Number(planCycle.planId) });
      if (!plan) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Plan not found.", code: "NOT_FOUND" });
      }
      if (typeof plan.globalLimit === "number") {
        const serversWithPlan = await servers.count({
          where: {
            planId: plan.id,
            status: serverStatus.active || serverStatus.pending || serverStatus.suspended,
          },
        });
        const serverLimit = plan.globalLimit - serversWithPlan;
        if (serverLimit <= 0) {
          userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
          throw new ActionError({ message: "Plan limit reached.", code: "CONFLICT" });
        }
      }
      if (typeof plan.perClientLimit === "number") {
        const clientServers = await servers.count({
          where: {
            planId: plan.id,
            clientId: c.clientId,
            status: serverStatus.active || serverStatus.pending || serverStatus.suspended,
          },
        });
        const clientServerLimit = plan.perClientLimit - clientServers;
        if (clientServerLimit <= 0) {
          userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
          throw new ActionError({ message: "Plan limit reached.", code: "CONFLICT" });
        }
      }
      const panelUrl = await settings
        .findOneBy({ key: "panel_url" })
        .then((panelUrl) => panelUrl?.value);
      if (!panelUrl) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Panel URL is missing.", code: "NOT_FOUND" });
      }
      const panelAppApiKey = await settings
        .findOneBy({ key: "panel_app_api_key" })
        .then((panelAppApiKey) => panelAppApiKey?.value);
      if (!panelAppApiKey) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Panel API key is missing.", code: "NOT_FOUND" });
      }
      const egg = parseEntities(input.egg);
      const node = parseEntities(input.node);
      if (egg.length !== 1 || node.length !== 1) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Egg or node is invalid.", code: "CONFLICT" });
      }
      const eggData = await fetch(
        new URL(
          `/api/application/nests/${egg[0].id}/eggs/${egg[0].values}?include=variables`,
          panelUrl,
        ).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      )
        .then((res) => (res.status === 200 ? res.json() : null))
        .catch(() => null);
      if (!eggData) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Egg not found.", code: "NOT_FOUND" });
      }
      const locationData = await fetch(
        new URL(`/api/application/locations/${node[0].id}?include=nodes`, panelUrl).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      )
        .then((res) => (res.status === 200 ? res.json() : null))
        .catch(() => null);
      if (!locationData) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node not found.", code: "NOT_FOUND" });
      }
      const filterNode = locationData.attributes.relationships.nodes.data.find(
        (nodeData: any) => nodeData.attributes.id === node[0].values[0],
      );
      if (!filterNode) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node not found.", code: "NOT_FOUND" });
      }
      const nodeData = await fetch(
        new URL(
          `/api/application/nodes/${node[0].values[0]}?include=allocations`,
          panelUrl,
        ).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      )
        .then((res) => (res.status === 200 ? res.json() : null))
        .catch(() => null);
      if (!nodeData) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node not found.", code: "NOT_FOUND" });
      }
      if (nodeData.attributes.maintenance_mode === true) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node is under maintenance.", code: "CONFLICT" });
      }
      const allocations = nodeData.attributes.relationships.allocations.data.filter(
        (allocation: any) => allocation.attributes.assigned === false,
      );
      if (allocations.length <= 0) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node is full.", code: "CONFLICT" });
      }
      const checkNodeSettings = await fetch(
        new URL(`/api/application/nodes/${node[0].values[0]}/configuration`, panelUrl).toString(),
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${panelAppApiKey}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      )
        .then((res) => (res.status === 200 ? res.json() : null))
        .catch(() => null);
      if (!checkNodeSettings) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node not found.", code: "NOT_FOUND" });
      }
      const checkStatus = await fetch(
        `${nodeData.attributes.scheme}://${nodeData.attributes.fqdn}:${nodeData.attributes.daemon_listen}/api/system`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${checkNodeSettings.token}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      ).catch(() => null);
      if (!checkStatus || checkStatus.ok === false) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Node is not ready.", code: "CONFLICT" });
      }
      const nextAllocation = allocations[0].attributes.id;
      const environment: any = {};
      eggData.attributes.relationships.variables.data.map((variable: any) => {
        environment[variable.attributes.env_variable] = variable.attributes.default_value;
      });
      const createServer = await fetch(new URL(`/api/application/servers`, panelUrl).toString(), {
        method: "POST",
        headers: {
          Authorization: `Bearer ${panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: input.serverName,
          description: plan.serverDescription,
          user: client.userId,
          egg: eggData.attributes.id,
          docker_image: eggData.attributes.docker_image,
          startup: eggData.attributes.startup,
          environment,
          limits: {
            memory: plan.ram,
            cpu: plan.cpu,
            disk: plan.disk,
            swap: plan.swap,
            io: plan.io,
          },
          feature_limits: {
            databases: plan.databases,
            backups: plan.backups,
            allocations: plan.extraPorts,
          },
          allocation: {
            default: nextAllocation,
          },
        }),
      })
        .then((res) => (res.status === 201 ? res.json() : null))
        .catch(() => null);
      if (!createServer) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Server not created.", code: "NOT_FOUND" });
      }
      if (!createServer.attributes.identifier) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Server not created.", code: "NOT_FOUND" });
      }
      const planCycleLength = planCycle.cycleLength;
      const planCycleType = planCycle.cycleType;
      const dueDate = (cycle: number): Date | null => {
        if (cycle === cycleType.oneTime) {
          return null;
        }
        const now = new Date();
        switch (cycle) {
          case cycleType.hourly:
            now.setHours(now.getHours() + planCycleLength);
            break;
          case cycleType.daily:
            now.setDate(now.getDate() + planCycleLength);
            break;
          case cycleType.monthly:
            now.setMonth(now.getMonth() + planCycleLength);
            break;
          case cycleType.yearly:
            now.setFullYear(now.getFullYear() + planCycleLength);
            break;
        }
        return now;
      };
      const server = new Servers();
      server.serverId = createServer.attributes.id;
      server.identifier = createServer.attributes.identifier;
      server.planId = planCycle.planId;
      server.clientId = client.id;
      server.planCycle = planCycle.id;
      server.serverName = input.serverName;
      server.status = serverStatus.active;
      server.dueDate = dueDate(planCycleType);
      server.createdAt = new Date();
      server.updatedAt = new Date();
      await servers.save(server);
      const clientChange = client.credit - (planCycle.initPrice - planCycle.setupFee);
      const credit = new Credits();
      credit.clientId = client.id;
      credit.details = "Purchased a server";
      credit.change = -(planCycle.initPrice + planCycle.setupFee);
      credit.balance = clientChange;
      credit.createdAt = new Date();
      await credits.save(credit);
      client.credit = clientChange;
      client.updatedAt = new Date();
      await clients.save(client);
      userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
      return { status: 201 };
    },
  }),
};
