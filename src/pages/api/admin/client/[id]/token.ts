import jwt from "jsonwebtoken";
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
    client.setSessionToken();
    client.updatedAt = new Date();
    await clients.save(client);
    if (client.id === c.clientId) {
      const maxAge = 7 * 24 * 60 * 60 * 1000;
      const expire = Math.floor(Date.now() / 1000) + maxAge;
      const token = jwt.sign(
        {
          exp: expire,
          clientId: client.id,
          email: client.email,
          sessionToken: client.sessionToken,
        },
        import.meta.env.APP_KEY,
      );
      cookies.set("_SECURE_SESSION_TOKEN_", token, {
        path: "/",
        sameSite: "strict",
        secure: true,
      });
    }
    return redirect(`/admin/clients/${client.id}?type=success&msg=admin.client.token`);
  }
  return redirect("/");
};
