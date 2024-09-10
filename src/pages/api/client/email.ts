import jwt from "jsonwebtoken";
import { settings, clients, jobs } from "@/database/index";
import { Jobs } from "@/database/entities/Jobs";
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
    if (!data.email || !data.password) {
      return redirect("/client/settings?type=error&msg=client.settings.email.error");
    }
    if (!client.verifyPassword(data.password)) {
      return redirect("/client/settings?type=error&msg=client.settings.email.error");
    }
    const newJob = new Jobs();
    newJob.queue = "update_client_email";
    newJob.payload = JSON.stringify({
      old: client.email,
      new: data.email,
    });
    await jobs.save(newJob);
    client.email = data.email;
    await clients.save(client);
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
    return redirect("/client/settings?type=success&msg=client.settings.success");
  }

  return redirect("/");
};
