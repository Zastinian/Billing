import type { APIRoute } from "astro";
import { categories, clients } from "@/database/index";
import profile from "@/utils/profile";

export const POST: APIRoute = async ({ cookies, request, redirect, params }) => {
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
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (
      !data.name ||
      !data.order ||
      typeof Number(data.order) !== "number" ||
      Number.isNaN(Number(data.order)) ||
      Number(data.order) < 0 ||
      Number(data.order) > 1000
    ) {
      return redirect(
        `/admin/categories/${category.id}?type=danger&msg=admin.category.update.error`,
      );
    }
    category.name = data.name;
    category.description = data.description ?? null;
    category.order = Number(data.order);
    category.updatedAt = new Date();
    await categories.save(category);
    return redirect(
      `/admin/categories/${category.id}?type=success&msg=admin.category.update.success`,
    );
  }
  return redirect("/");
};
