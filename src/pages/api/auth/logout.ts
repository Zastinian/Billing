import type { APIRoute } from "astro";

export const GET: APIRoute = async ({ cookies, redirect }) => {
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
