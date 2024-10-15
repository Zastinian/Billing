import jwt from "jsonwebtoken";
import { clients, settings } from "@/database/index";
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
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (!data.email) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.client.email.error`);
    }
    const checkClient = await clients.findOneBy({ email: data.email });
    if (checkClient) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.client.email.already`);
    }
    const panelUrl = await settings
      .findOneBy({ key: "panel_url" })
      .then((panelUrl) => panelUrl?.value);
    if (!panelUrl) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Panel URL is missing")}`);
    }
    const panelAppApiKey = await settings
      .findOneBy({ key: "panel_app_api_key" })
      .then((panelAppApiKey) => panelAppApiKey?.value);
    if (!panelAppApiKey) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Panel API Key is missing")}`);
    }
    const checkPterodactylUser = await fetch(
      new URL(`/api/application/users?filter[email]=${data.email}`, panelUrl).toString(),
      {
        method: "GET",
        headers: {
          Authorization: `Bearer ${panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      },
    );
    if (checkPterodactylUser.status !== 200) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while checking the user in the panel")}`);
    }
    if (!checkPterodactylUser.ok) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while checking the user in the panel")}`);
    }
    const dataPterodactylUser = await checkPterodactylUser.json();
    if (dataPterodactylUser.data.length > 0) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.client.email.already`);
    }
    const getPterodactylUser = await fetch(
      new URL(`/api/application/users/${client.userId}`, panelUrl).toString(),
      {
        method: "GET",
        headers: {
          Authorization: `Bearer ${panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      },
    );
    if (getPterodactylUser.status !== 200) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while getting the user in the panel")}`);
    }
    if (!getPterodactylUser.ok) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while getting the user in the panel")}`);
    }
    const getPterodactylUserData = await getPterodactylUser.json();
    const pterodactyl = await fetch(
      new URL(`/api/application/users/${client.userId}`, panelUrl).toString(),
      {
        method: "PATCH",
        headers: {
          Authorization: `Bearer ${panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: data.email,
          username: getPterodactylUserData.attributes.username,
          first_name: getPterodactylUserData.attributes.first_name,
          last_name: getPterodactylUserData.attributes.last_name,
        }),
      },
    );
    if (pterodactyl.status !== 200) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while updating the user in the panel")}`);
    }
    if (!pterodactyl.ok) {
      return redirect(`/admin/clients/${client.id}?type=danger&msg=admin.error&error=${encodeURI("Error while updating the user in the panel")}`);
    }
    client.email = data.email;
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
    return redirect(`/admin/clients/${client.id}?type=success&msg=admin.client.email.success`);
  }
  return redirect("/");
};
