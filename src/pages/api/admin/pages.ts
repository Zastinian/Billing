import type { APIRoute } from "astro";
import { clients, pages } from "@/database/index";
import profile from "@/utils/profile";

export const POST: APIRoute = async ({ cookies, request, redirect }) => {
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
