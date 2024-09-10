import { settings } from "@/database/index";
import type { APIRoute } from "astro";

export const GET: APIRoute = async ({ cookies, redirect, request, rewrite }) => {
  const storeUrl = new URL(
    (await settings.findOneBy({ key: "store_url" }).then((storeUrl) => storeUrl?.value)) ?? "",
  );
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
