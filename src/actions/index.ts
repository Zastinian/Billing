import { ActionError, defineAction } from "astro:actions";
import { z } from "astro:schema";
import { Credits } from "@/database/entities/Credits";
import { Servers } from "@/database/entities/Servers";
import {
  clients,
  coupons,
  credits,
  planCycles,
  plans,
  servers,
  settings,
  usedCoupons,
} from "@/database/index";
import parseEntities, { isValidFormat } from "@/utils/parseEntities";
import profile from "@/utils/profile";
import { cycleType, serverStatus } from "@/utils/status";
import { UsedCoupons } from "../database/entities/UsedCoupons";

const userAlreadyCreatingAServer: any[] = [];

function percentOffToDiscount(price: number, percentOff: number) {
  return price - price * (percentOff / 100);
}

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
      coupon: z.optional(
        z.string({
          message: "Coupon code is invalid.",
        }),
      ),
    }),
    handler: async (input, context) => {
      if (input.serverName.length < 3 || input.serverName.length > 20) {
        throw new ActionError({ message: "Server name is invalid.", code: "CONFLICT" });
      }
      if (!isValidFormat(input.egg)) {
        throw new ActionError({ message: "Egg is invalid.", code: "CONFLICT" });
      }
      if (!isValidFormat(input.node)) {
        throw new ActionError({ message: "Node is invalid.", code: "CONFLICT" });
      }
      const cookies = context.request.headers.get("cookie");
      if (!cookies) {
        throw new Error("Session error.");
      }
      const cookie = cookies
        .split(";")
        .find((token) => token.includes("_SECURE_SESSION_TOKEN_"))
        ?.split("=")[1]
        ?.trim();
      if (!cookie) {
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
      const c = profile(cookie);
      if (!c.clientId) {
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
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
      const plan = await plans.findOneBy({ id: Number(planCycle.planId) });
      if (!plan) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Plan not found.", code: "NOT_FOUND" });
      }
      let discount = 0;
      let couponId: number | null = null;
      let totalDiscount = percentOffToDiscount(planCycle.initPrice - planCycle.setupFee, discount);
      let totalPrice = client.credit - totalDiscount;
      if (input.coupon) {
        const coupon = await coupons.findOneBy({ code: input.coupon });

        if (coupon) {
          const allUsedCoupons = await usedCoupons.find({
            where: { couponId: coupon.id },
          });

          const clientUsedCoupons = allUsedCoupons.filter((used) => used.clientId === client.id);

          const isGlobalLimitReached =
            coupon.globalLimit && allUsedCoupons.length >= coupon.globalLimit;
          const isClientLimitReached =
            coupon.perClientLimit && clientUsedCoupons.length >= coupon.perClientLimit;

          const isPlanSpecificCoupon = coupon.isGlobal !== 1;
          const isCouponValidForPlan =
            !isPlanSpecificCoupon || plan.coupons?.split(",").includes(coupon.id.toString());

          if (!isGlobalLimitReached && !isClientLimitReached && isCouponValidForPlan) {
            discount = coupon.percentOff;
            couponId = coupon.id;
            totalDiscount = percentOffToDiscount(
              planCycle.initPrice - planCycle.setupFee,
              discount,
            );
            totalPrice = client.credit - totalDiscount;
          }
        }
      }
      if (totalPrice < 0) {
        userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
        throw new ActionError({ message: "Insufficient credits.", code: "CONFLICT" });
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
        .then((panelAppApiKey) => panelAppApiKey?.getValue());
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
        return null;
      });

      const { createPterodactylServer } = await import("@/utils/pterodactylServer");
      const createServer = await createPterodactylServer(
        {
          name: input.serverName,
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
        { panelUrl, panelAppApiKey },
      );

      if (!createServer) {
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
      if (couponId) {
        const newUsedCoupon = new UsedCoupons();
        newUsedCoupon.couponId = couponId;
        newUsedCoupon.clientId = client.id;
        newUsedCoupon.serverId = createServer.id;
        newUsedCoupon.createdAt = new Date();
        newUsedCoupon.updatedAt = new Date();

        await usedCoupons.save(newUsedCoupon);
      }
      const server = new Servers();
      server.serverId = createServer.id;
      server.identifier = createServer.identifier;
      server.planId = planCycle.planId;
      server.clientId = client.id;
      server.planCycle = planCycle.id;
      server.serverName = input.serverName;
      server.status = serverStatus.active;
      server.dueDate = dueDate(planCycleType);
      server.createdAt = new Date();
      server.updatedAt = new Date();
      await servers.save(server);
      const credit = new Credits();
      credit.clientId = client.id;
      credit.details = "Purchased a server";
      credit.change = -totalDiscount;
      credit.balance = totalPrice;
      credit.createdAt = new Date();
      await credits.save(credit);
      client.credit = totalPrice;
      client.updatedAt = new Date();
      await clients.save(client);
      userAlreadyCreatingAServer.splice(userAlreadyCreatingAServer.indexOf(c.clientId), 1);
      return { status: 201 };
    },
  }),
  checkCoupon: defineAction({
    accept: "json",
    input: z.object({
      coupon: z.string({
        message: "Coupon code is invalid.",
      }),
      planId: z.nullable(
        z.number({
          message: "Plan is invalid.",
        }),
      ),
    }),
    handler: async (input, context) => {
      const cookies = context.request.headers.get("cookie");
      if (!cookies) {
        throw new Error("Session error.");
      }
      const cookie = cookies
        .split(";")
        .find((token) => token.includes("_SECURE_SESSION_TOKEN_"))
        ?.split("=")[1]
        ?.trim();
      if (!cookie) {
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
      const c = profile(cookie);
      if (!c.clientId) {
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
      const client = await clients.findOneBy({ id: c.clientId });
      if (!client) {
        throw new ActionError({ message: "Session error.", code: "UNAUTHORIZED" });
      }
      const coupon = await coupons.findOneBy({ code: input.coupon });
      if (!coupon) {
        throw new ActionError({ message: "Coupon not found.", code: "NOT_FOUND" });
      }
      if (coupon.isGlobal !== 1) {
        if (!input.planId) {
          throw new ActionError({
            message: "Plan is required.",
            code: "NOT_FOUND",
          });
        }
        const plan = await plans.findOneBy({ id: input.planId });
        if (!plan) {
          throw new ActionError({ message: "Plan not found.", code: "NOT_FOUND" });
        }
        if (!plan.coupons) {
          throw new ActionError({
            message: "The coupon is not valid on this plan.",
            code: "NOT_FOUND",
          });
        }
        const planCoupons = plan.coupons.split(",");
        if (!planCoupons.includes(coupon.id.toString())) {
          throw new ActionError({
            message: "The coupon is not valid on this plan.",
            code: "NOT_FOUND",
          });
        }
      }
      const usedCoupon = await usedCoupons.find({
        where: {
          couponId: coupon.id,
        },
      });
      if (coupon.globalLimit && usedCoupon.length >= coupon.globalLimit) {
        throw new ActionError({
          message: "The coupon global limit has been reached.",
          code: "CONFLICT",
        });
      }
      const clientUsedCoupons = usedCoupon.filter(
        (usedCoupon) => usedCoupon.clientId === client.id,
      );
      if (coupon.perClientLimit && clientUsedCoupons.length >= coupon.perClientLimit) {
        throw new ActionError({
          message: "The coupon per client limit has been reached.",
          code: "CONFLICT",
        });
      }
      return { status: 200, percentOff: coupon.percentOff, oneTime: coupon.oneTime };
    },
  }),
};
