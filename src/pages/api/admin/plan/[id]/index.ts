import { clients, plans, planCycles, servers } from "@/database/index";
import { PlanCycles } from "@/database/entities/PlanCycles";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";
import { serverStatus } from "@/src/utils/status";

export const POST: APIRoute = async ({ cookies, request, redirect, rewrite, params }) => {
  const storeUrl = new URL(import.meta.env.STORE_URL ?? "");
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);

  if (c.success === true && c.clientId !== null) {
    const client = await clients.findOneBy({ id: c.clientId });
    if (client?.email !== c.email) {
      return redirect("/");
    }
    if (client?.sessionToken !== c.sessionToken) {
      return redirect("/");
    }
    if (client?.isAdmin !== 1) {
      return redirect("/");
    }
    const planId = params.id;
    if (!planId || typeof Number(planId) !== "number" || Number.isNaN(Number(planId))) {
      return redirect("/admin/plans?type=danger&msg=admin.plan.not_found");
    }
    const plan = await plans.findOneBy({ id: Number(planId) });
    if (!plan) {
      return redirect("/admin/plans?type=danger&msg=admin.plan.not_found");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (
      !data.name ||
      !data.category ||
      !data.ram ||
      !data.cpu ||
      !data.disk ||
      !data.swap ||
      !data.io ||
      !data.databases ||
      !data.backups ||
      !data.extra_ports ||
      !data.nodes ||
      !data.eggs ||
      !data.days_before_suspend ||
      !data.days_before_delete ||
      !data.order ||
      data.name.length < 0 ||
      data.category.length < 0 ||
      data.ram.length < 0 ||
      data.cpu.length < 0 ||
      data.disk.length < 0 ||
      data.swap.length < 0 ||
      data.io.length < 0 ||
      data.databases.length < 0 ||
      data.backups.length < 0 ||
      data.extra_ports.length < 0 ||
      data.nodes.length < 0 ||
      data.eggs.length < 0 ||
      data.days_before_suspend.length < 0 ||
      data.days_before_delete.length < 0 ||
      data.order.length < 0 ||
      typeof Number(data.ram) !== "number" ||
      Number.isNaN(Number(data.ram)) ||
      Number(data.ram) < 0 ||
      typeof Number(data.cpu) !== "number" ||
      Number.isNaN(Number(data.cpu)) ||
      Number(data.cpu) < 0 ||
      typeof Number(data.disk) !== "number" ||
      Number.isNaN(Number(data.disk)) ||
      Number(data.disk) < 0 ||
      typeof Number(data.swap) !== "number" ||
      Number.isNaN(Number(data.swap)) ||
      Number(data.swap) < 0 ||
      typeof Number(data.io) !== "number" ||
      Number.isNaN(Number(data.io)) ||
      Number(data.io) < 0 ||
      typeof Number(data.databases) !== "number" ||
      Number.isNaN(Number(data.databases)) ||
      Number(data.databases) < 0 ||
      typeof Number(data.backups) !== "number" ||
      Number.isNaN(Number(data.backups)) ||
      Number(data.backups) < 0 ||
      typeof Number(data.extra_ports) !== "number" ||
      Number.isNaN(Number(data.extra_ports)) ||
      Number(data.extra_ports) < 0 ||
      typeof Number(data.days_before_suspend) !== "number" ||
      Number.isNaN(Number(data.days_before_suspend)) ||
      typeof Number(data.days_before_delete) !== "number" ||
      Number.isNaN(Number(data.days_before_delete)) ||
      typeof Number(data.order) !== "number" ||
      Number.isNaN(Number(data.order)) ||
      Number(data.order) < 0 ||
      Number(data.order) > 1000
    ) {
      return redirect("/admin/plans/${plan.id}?type=danger&msg=admin.plan.create.error");
    }

    interface NewCycle {
      cycle_length: string;
      cycle_type: string;
      init_price: string;
      renew_price: string;
      setup_fee: string;
      late_fee: string;
      trial_length: string;
      trial_type: string;
    }

    const requiredFieldsNewCycle: (keyof NewCycle)[] = [
      "cycle_length",
      "cycle_type",
      "init_price",
      "renew_price",
      "setup_fee",
      "late_fee",
    ];

    const newCycles: NewCycle[] = Object.entries(data)
      .reduce((result: NewCycle[], [key, value]) => {
        const match = key.match(/^new_cycle\[(\d+)]\[(\w+)]$/);
        if (match) {
          const index = parseInt(match[1], 10);
          const field = match[2] as keyof NewCycle;
          if (!result[index]) {
            result[index] = {
              cycle_length: "",
              cycle_type: "",
              init_price: "",
              renew_price: "",
              setup_fee: "",
              late_fee: "",
              trial_length: "",
              trial_type: "",
            };
          }
          result[index][field] = value.trim();
        }
        return result;
      }, [])
      .filter((cycle) => Object.values(cycle).some((value) => value !== ""));

    for (const cycle of newCycles) {
      for (const field of requiredFieldsNewCycle) {
        if (!cycle[field] || cycle[field].trim() === "") {
          return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.update.error`);
        }
      }
    }

    interface CycleWithId extends NewCycle {
      id: number;
    }

    const cyclesWithId: CycleWithId[] = Object.entries(data)
      .reduce((result: CycleWithId[], [key, value]) => {
        const match = key.match(/^cycle\[(\d+)]\[(\w+)]$/);
        if (match) {
          const id = match[1];
          const field = match[2] as keyof NewCycle;
          let cycle = result.find((c) => c.id === Number(id));
          if (!cycle) {
            cycle = {
              id: Number(id),
              cycle_length: "",
              cycle_type: "",
              init_price: "",
              renew_price: "",
              setup_fee: "",
              late_fee: "",
              trial_length: "",
              trial_type: "",
            };
            result.push(cycle);
          }
          cycle[field] = value.trim();
        }
        return result;
      }, [])
      .filter((cycle) => Object.values(cycle).some((value) => value !== ""));

    for (const cycle of cyclesWithId) {
      for (const field of requiredFieldsNewCycle) {
        if (typeof Number(cycle["id"]) !== "number") {
          return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.update.error`);
        }
        if (!cycle[field] || cycle[field].trim() === "") {
          return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.update.error`);
        }
      }
    }

    const numericFields = [
      "min_port",
      "max_port",
      "discount",
      "global_limit",
      "per_client_limit",
      "per_client_trial_limit",
    ];

    const formatFields = {
      locationsNodesId: /^(\d+:\d+,?)+$/,
      nestsEggsId: /^(\d+:\d+,?)+$/,
      coupons: /^(\d+,?)+$/,
    };

    for (const field of numericFields) {
      if (field in data) {
        const value = data[field];
        if (value.length === 0) {
          continue;
        } else if (field in data && typeof value !== "number") {
          return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.create.error`);
        }
      }
    }

    for (const [field, regex] of Object.entries(formatFields)) {
      if (field in data && data[field].length === 0) {
        continue;
      } else if (field in data && !regex.test(data[field])) {
        return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.create.error`);
      }
    }

    const existingPlanCycles = await planCycles.findBy({ planId: plan.id });

    const cyclesToDelete = existingPlanCycles.filter(
      (existingCycle) => !cyclesWithId.some((updatedCycle) => updatedCycle.id === existingCycle.id),
    );

    for (const cycleToDelete of cyclesToDelete) {
      const serversWithCycle = (await servers.findBy({ planCycle: cycleToDelete.id })).filter(
        (server) => {
          return (
            server.status !== serverStatus.terminated && server.status !== serverStatus.canceled
          );
        },
      );
      if (serversWithCycle.length < 0) {
        return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.cycle.delete.error`);
      }
      await planCycles.remove(cycleToDelete);
    }

    for (const cycle of cyclesWithId) {
      const planCycle = await planCycles.findOneBy({ id: cycle.id });
      if (!planCycle) {
        return redirect(`/admin/plans/${plan.id}?type=danger&msg=admin.plan.update.error`);
      }
      planCycle.cycleLength = Number(cycle.cycle_length);
      planCycle.cycleType = Number(cycle.cycle_type);
      planCycle.initPrice = Number(cycle.init_price);
      planCycle.renewPrice = Number(cycle.renew_price);
      planCycle.setupFee = Number(cycle.setup_fee);
      planCycle.lateFee = Number(cycle.late_fee);
      planCycle.trialLength = cycle.trial_length ? Number(cycle.trial_length) : null;
      planCycle.trialType = cycle.trial_type ? Number(cycle.trial_type) : null;
      planCycle.updatedAt = new Date();
      await planCycles.save(planCycle);
    }

    plan.name = data.name;
    plan.description = data.description ?? null;
    plan.order = Number(data.order);
    plan.categoryId = Number(data.category);
    plan.ram = Number(data.ram);
    plan.cpu = Number(data.cpu);
    plan.disk = Number(data.disk);
    plan.swap = Number(data.swap);
    plan.io = Number(data.io);
    plan.databases = Number(data.databases);
    plan.backups = Number(data.backups);
    plan.extraPorts = Number(data.extra_ports);
    plan.minPort = data.min_port.length < 0 ? Number(data.min_port) : null;
    plan.maxPort = data.max_port.length < 0 ? Number(data.max_port) : null;
    plan.serverDescription = data.server_description ?? null;
    plan.discount = data.discount.length < 0 ? Number(data.discount) : null;
    plan.coupons = data.coupons ?? null;
    plan.daysBeforeSuspend = Number(data.days_before_suspend);
    plan.daysBeforeDelete = Number(data.days_before_delete);
    plan.globalLimit = data.global_limit.length < 0 ? Number(data.global_limit) : null;
    plan.perClientLimit = data.per_client_limit.length < 0 ? Number(data.per_client_limit) : null;
    plan.perClientTrialLimit =
      data.per_client_trial_limit.length < 0 ? Number(data.per_client_trial_limit) : null;
    plan.locationsNodesId = data.nodes;
    plan.nestsEggsId = data.eggs;
    plan.updatedAt = new Date();
    await plans.save(plan);

    for (const cycle of newCycles) {
      const planCycle = new PlanCycles();
      planCycle.planId = plan.id;
      planCycle.cycleLength = Number(cycle.cycle_length);
      planCycle.cycleType = Number(cycle.cycle_type);
      planCycle.initPrice = Number(cycle.init_price);
      planCycle.renewPrice = Number(cycle.renew_price);
      planCycle.setupFee = Number(cycle.setup_fee);
      planCycle.lateFee = Number(cycle.late_fee);
      planCycle.trialLength = cycle.trial_length ? Number(cycle.trial_length) : null;
      planCycle.trialType = cycle.trial_type ? Number(cycle.trial_type) : null;
      planCycle.createdAt = new Date();
      planCycle.updatedAt = new Date();
      await planCycles.save(planCycle);
    }

    return redirect(`/admin/plans/${plan.id}?type=success&msg=admin.plan.update.success`);
  }
  return redirect("/");
};
