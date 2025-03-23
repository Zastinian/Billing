import { clients } from "@/database/index";
import jwt from "jsonwebtoken";
import type { APIRoute } from "astro";
import { APP_KEY, STORE_URL } from "astro:env/server";

const storeUrl = new URL(STORE_URL ?? "");

export const POST: APIRoute = async ({ cookies, redirect, request, rewrite }) => {
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const data = Object.fromEntries(new URLSearchParams(await request.text()));
  const client = await clients.findOneBy({ email: data.email });
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
      sessionToken: client.sessionToken,
    },
    APP_KEY,
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
