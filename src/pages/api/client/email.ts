import jwt from "jsonwebtoken";
import { clients, settings } from "@/database/index";
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
    if (!data.email || !data.password) {
      return redirect("/client/settings?type=danger&msg=client.settings.email.error");
    }
    if (!(await client.verifyPassword(data.password))) {
      return redirect("/client/settings?type=danger&msg=client.settings.email.error");
    }
    // const newJob = new Jobs();
    // newJob.queue = "update_client_email";
    // newJob.payload = JSON.stringify({
    //   old: client.email,
    //   new: data.email,
    // });
    // await jobs.save(newJob);
    const checkClient = await clients.findOneBy({ email: data.email });
    if (checkClient) {
      return redirect("/client/settings?type=danger&msg=client.settings.email.already");
    }
    const panelUrl = await settings
      .findOneBy({ key: "panel_url" })
      .then((panelUrl) => panelUrl?.value);
    if (!panelUrl) {
      return redirect("/client/settings?type=danger&msg=error");
    }
    const panelAppApiKey = await settings
      .findOneBy({ key: "panel_app_api_key" })
      .then((panelAppApiKey) => panelAppApiKey?.value);
    if (!panelAppApiKey) {
      return redirect("/client/settings?type=danger&msg=error");
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
      return redirect("/client/settings?type=danger&msg=error");
    }
    if (!checkPterodactylUser.ok) {
      return redirect("/client/settings?type=danger&msg=error");
    }
    const dataPterodactylUser = await checkPterodactylUser.json();
    if (dataPterodactylUser.data.length > 0) {
      return redirect("/client/settings?type=danger&msg=client.settings.email.already");
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
      return redirect("/client/settings?type=danger&msg=error");
    }
    if (!getPterodactylUser.ok) {
      return redirect("/client/settings?type=danger&msg=error");
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
      return redirect("/client/settings?type=danger&msg=error");
    }
    if (!pterodactyl.ok) {
      return redirect("/client/settings?type=danger&msg=error");
    }
    client.email = data.email;
    client.setSessionToken();
    client.updatedAt = new Date();
    await clients.save(client);
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
    return redirect("/client/settings?type=success&msg=client.settings.success");
  }
  return redirect("/");
};
