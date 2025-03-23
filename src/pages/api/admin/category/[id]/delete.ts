import { clients, plans } from "@/database/index";
import { categories } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";
import { STORE_URL } from "astro:env/server";

const storeUrl = new URL(STORE_URL ?? "");

export const POST: APIRoute = async ({ cookies, request, redirect, rewrite, params }) => {
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
    const categoryId = params.id;
    if (!categoryId || typeof Number(categoryId) !== "number" || Number.isNaN(Number(categoryId))) {
      return redirect("/admin/categories?type=danger&msg=admin.category.not_found");
    }
    const category = await categories.findOneBy({ id: Number(categoryId) });
    if (!category) {
      return redirect("/admin/categories?type=danger&msg=admin.category.not_found");
    }
    const plansData = await plans.find({
      where: { categoryId: category.id },
      select: ["id", "categoryId"],
    });
    if (plansData.length > 0) {
      return redirect(
        `/admin/categories/${category.id}?type=danger&msg=admin.category.delete.error`,
      );
    }
    await categories.remove(category);
    return redirect(`/admin/categories?type=success&msg=admin.category.delete.success`);
  }
  return redirect("/");
};
