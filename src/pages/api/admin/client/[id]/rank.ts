import { clients } from "@/database/index";
import profile from "@/utils/profile";
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
    const type = requestUrl.searchParams.get("type");
    if (!type) {
      return redirect("/admin/clients?type=danger&msg=admin.client.rank.error");
    }
    if (type !== "promote" && type !== "demote") {
      return redirect("/admin/clients?type=danger&msg=admin.client.rank.error");
    }
    client.isAdmin = type === "promote" ? 1 : 0;
    client.updatedAt = new Date();
    await clients.save(client);
    return redirect(`/admin/clients/${client.id}?type=success&msg=admin.client.rank.success`);
  }
  return redirect("/");
};
