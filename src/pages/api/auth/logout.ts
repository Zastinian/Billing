import type { APIRoute } from "astro";

const storeUrl = new URL(import.meta.env.STORE_URL ?? "");

export const GET: APIRoute = async ({ cookies, redirect, request, rewrite }) => {
  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return rewrite("/404");
  }
  const cook = cookies.get("_SECURE_SESSION_TOKEN_");
  if (cook) {
    cookies.set("_SECURE_SESSION_TOKEN_", cook, {
      path: "/",
      maxAge: 0,
      secure: true,
    });
    return redirect("/");
  }
  return redirect("/");
};
