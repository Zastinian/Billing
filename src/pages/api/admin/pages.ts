import { clients } from "@/database/index";
import { pages } from "@/database/index";
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
    if (client?.sessionToken !== c.sessionToken) {
      return redirect("/");
    }
    if (client?.isAdmin !== 1) {
      return redirect("/");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (!data.name) {
      return redirect("/admin?type=danger&msg=admin.page.error");
    }
    const page = await pages.findOneBy({ name: data.name });
    if (!page) {
        return redirect("/admin?type=danger&msg=admin.page.error");
    }
    page.content = data.content;
    await pages.save(page);
    return redirect(`/admin/pages/${data.name}?type=success&msg=admin.page.success`);
  }
  return redirect("/");
};
