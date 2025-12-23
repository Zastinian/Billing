import type { APIRoute } from "astro";
import jwt from "jsonwebtoken";
import config from "@/config/index";
import { clients } from "@/database/index";
import { verifyCaptcha } from "@/utils/captcha";

const { APP_KEY } = config;

export const POST: APIRoute = async ({ cookies, redirect, request }) => {
  const data = Object.fromEntries(new URLSearchParams(await request.text()));

  const captchaValid = await verifyCaptcha(
    data["cf-turnstile-response"] || data["h-captcha-response"] || null,
  );
  if (!captchaValid) {
    return redirect("/?type=danger&msg=captcha.invalid");
  }

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
