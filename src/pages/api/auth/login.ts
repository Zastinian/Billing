import { settings, clients } from "@/database/index";
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
  const client = await clients.findOneBy({ email: data.email }).then((client) => client);
  if (!client) {
    return redirect("/?type=danger&msg=auth.invalid");
  }
  if (!(await client.verifyPassword(data.password))) {
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
  if (data.remember === "on") {
    cookies.set("_SECURE_SESSION_TOKEN_", token, {
      path: "/",
      maxAge: maxAge,
      sameSite: "strict",
      secure: true,
    });
    return redirect("/");
  }
  cookies.set("_SECURE_SESSION_TOKEN_", token, {
    path: "/",
    sameSite: "strict",
    secure: true,
  });
  return redirect("/");
};
