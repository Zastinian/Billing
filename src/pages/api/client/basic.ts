import { settings, clients } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ cookies, request, redirect, rewrite }) => {
  const storeUrl = new URL(
    (await settings.findOneBy({ key: "store_url" }).then((storeUrl) => storeUrl?.value)) ?? "",
  );
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);

  if (c.success === true && c.clientId !== null) {
    const client = await clients.findOneBy({ id: c.clientId }).then((client) => client);
    if (client?.email !== c.email) {
      return redirect("/");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (!data.currency || !data.country) {
      return redirect("/client/settings?type=error&msg=client.settings.basic.error");
    }
    client.currency = Number(data.currency);
    client.country = Number(data.country);
    await clients.save(client);
    return redirect("/client/settings?type=success&msg=client.settings.success");
  }

  return redirect("/");
};
