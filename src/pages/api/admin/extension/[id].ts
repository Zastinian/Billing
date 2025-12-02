import type { APIRoute } from "astro";
import { Extensions } from "@/database/entities/Extensions";
import { clients, extensions } from "@/database/index";
import profile from "@/utils/profile";

export const POST: APIRoute = async ({ cookies, request, redirect, params }) => {
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);

  if (!c.success || !c.clientId || !c.email || !c.sessionToken) {
    return redirect("/");
  }

  const admin = await clients.findOneBy({ id: c.clientId });
  if (
    !admin ||
    admin.email !== c.email ||
    admin.sessionToken !== c.sessionToken ||
    admin.isAdmin !== 1
  ) {
    return redirect("/");
  }

  const extensionName = params.id;
  if (!extensionName) {
    return redirect("/admin/payments?type=danger&msg=Extension not specified");
  }

  try {
    const formData = await request.formData();
    const updates: { key: string; value: string }[] = [];

    for (const [key, value] of formData.entries()) {
      updates.push({
        key,
        value: value.toString(),
      });
    }

    await extensions.delete({ extension: extensionName });

    for (const update of updates) {
      const ext = new Extensions();
      ext.extension = extensionName;
      ext.key = update.key;
      await ext.setValue(update.value);
      ext.createdAt = new Date();
      ext.updatedAt = new Date();
      await extensions.save(ext);
    }

    return redirect("/admin/payments?type=success&msg=Extension updated successfully");
  } catch (error) {
    console.error("Error updating extension:", error);
    return redirect("/admin/payments?type=danger&msg=Failed to update extension");
  }
};
