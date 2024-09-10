import { settings, clients } from "@/database/index";
import { Clients } from "@/database/entities/Clients";
import jwt from "jsonwebtoken";
import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ cookies, redirect, request, rewrite }) => {
  const storeUrl = new URL(
    (await settings.findOneBy({ key: "store_url" }).then((storeUrl) => storeUrl?.value)) ?? "",
  );
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const data = Object.fromEntries(new URLSearchParams(await request.text()));
  const checkClient = await clients.findOneBy({ email: data.email }).then((client) => client);
  if (checkClient) {
    return redirect("/?type=danger&msg=auth.register.already");
  }
  if (data.password !== data.password_confirmation) {
    return redirect("/?type=danger&msg=auth.register.password");
  }
  const panelUrl = await settings
    .findOneBy({ key: "panel_url" })
    .then((panelUrl) => panelUrl?.value);
  if (!panelUrl) {
    return redirect("/?type=danger&msg=error");
  }
  const panelAppApiKey = await settings
    .findOneBy({ key: "panel_app_api_key" })
    .then((panelAppApiKey) => panelAppApiKey?.value);
  if (!panelAppApiKey) {
    return redirect("/?type=danger&msg=error");
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
    return redirect("/?type=danger&msg=error");
  }
  if (!checkPterodactylUser.ok) {
    return redirect("/?type=danger&msg=error");
  }
  const dataPterodactylUser = await checkPterodactylUser.json();
  if (dataPterodactylUser.data.length > 0) {
    return redirect("/?type=danger&msg=auth.register.pterodactyl");
  }
  const pterodactyl = await fetch(new URL("/api/application/users", panelUrl).toString(), {
    method: "POST",
    headers: {
      Authorization: `Bearer ${panelAppApiKey}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      username:
        `${data.email}`.split("@")[0].replace(/[^A-Za-z0-9 ]/g, "") +
        Math.random().toString(36).substring(2, 6),
      email: data.email,
      password: data.password,
      first_name: "First",
      last_name: "Last",
    }),
  });
  if (pterodactyl.status !== 200) {
    return redirect("/?type=danger&msg=error");
  }
  if (!pterodactyl.ok) {
    return redirect("/?type=danger&msg=error");
  }
  const dataPterodactyl = await pterodactyl.json();
  const newClient = new Clients();
  newClient.email = data.email;
  newClient.userId = dataPterodactyl.attributes.id;
  newClient.setPassword(data.password);
  await clients.save(newClient);
  const client = await clients.findOneBy({ email: data.email }).then((client) => client);
  if (!client) {
    return redirect("/?type=danger&msg=auth.invalid");
  }
  const maxAge = 7 * 24 * 60 * 60 * 1000;
  const expire = Math.floor(Date.now() / 1000) + maxAge;
  const token = jwt.sign(
    {
      exp: expire,
      clientId: client.id,
      email: client.email,
    },
    import.meta.env.APP_KEY,
  );
  cookies.set("_SECURE_SESSION_TOKEN_", token, {
    path: "/",
    sameSite: "strict",
    secure: true,
  });
  return redirect("/");
};
