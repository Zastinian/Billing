import { clients } from "@/database/index";
import { categories } from "@/database/index";
import { Categories } from "@/database/entities/Categories";
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
    if (
      !data.name ||
      !data.order ||
      typeof Number(data.order) !== "number" ||
      Number.isNaN(Number(data.order)) ||
      Number(data.order) < 0 ||
      Number(data.order) > 1000
    ) {
      return redirect("/admin/categories?type=danger&msg=admin.category.create.error");
    }
    const category = new Categories();
    category.name = data.name;
    category.description = data.description ?? null;
    category.order = Number(data.order);
    category.createdAt = new Date();
    category.updatedAt = new Date();
    await categories.save(category);
    return redirect(`/admin/categories/${category.id}`);
  }
  return redirect("/");
};
