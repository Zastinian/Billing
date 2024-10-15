import { clients } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ cookies, request, redirect, rewrite }) => {
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
    if (client.sessionToken !== c.sessionToken) {
      return redirect("/");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (
      !data.currency ||
      !data.auto_renew ||
      typeof Number(data.auto_renew) !== "number" ||
      Number.isNaN(Number(data.auto_renew)) ||
      Number(data.auto_renew) < 0 ||
      Number(data.auto_renew) > 1
    ) {
      return redirect("/client/settings?type=danger&msg=client.settings.basic.error");
    }
    client.currency = Number(data.currency);
    client.autoRenew = Number(data.auto_renew);
    client.updatedAt = new Date();
    await clients.save(client);
    return redirect("/client/settings?type=success&msg=client.settings.success");
  }
  return redirect("/");
};
