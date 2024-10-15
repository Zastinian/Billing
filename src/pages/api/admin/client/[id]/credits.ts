import { clients, credits } from "@/database/index";
import profile from "@/utils/profile";
import { Credits } from "@/database/entities/Credits";
import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ cookies, request, redirect, rewrite, params }) => {
  const storeUrl = new URL(import.meta.env.STORE_URL ?? "");
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);
  if (c.success === true && c.clientId !== null) {
    const admin = await clients.findOneBy({ id: c.clientId });
    if (admin?.email !== c.email) {
      return redirect("/");
    }
    if (admin?.sessionToken !== c.sessionToken) {
      return redirect("/");
    }
    if (admin?.isAdmin !== 1) {
      return redirect("/");
    }
    const clientId = params.id;
    if (!clientId || typeof Number(clientId) !== "number" || Number.isNaN(Number(clientId))) {
      return redirect("/admin/clients?type=danger&msg=admin.client.not_found");
    }
    const client = await clients.findOneBy({ id: Number(clientId) });
    if (!client) {
      return redirect("/admin/clients?type=danger&msg=admin.client.not_found");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (
      !data.credit ||
      typeof Number(data.credit) !== "number" ||
      Number.isNaN(Number(data.credit)) ||
      Number(data.credit) < 0 ||
      Number(data.credit) > 999999.99
    ) {
      return redirect(`/admin/clients/${client.id}/credits?type=danger&msg=admin.client.credits.error`);
    }
    const credit = new Credits();
    credit.clientId = client.id;
    credit.details = "Edited by an administrator";
    credit.change = Number(data.credit) - client.credit;
    credit.balance = Number(data.credit);
    credit.createdAt = new Date();
    await credits.save(credit);
    client.credit = Number(data.credit);
    client.updatedAt = new Date();
    await clients.save(client);
    return redirect(`/admin/clients/${client.id}/credits?type=success&msg=admin.client.credits.success`);
  }
  return redirect("/");
};
