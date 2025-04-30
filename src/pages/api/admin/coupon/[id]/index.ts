import { clients } from "@/database/index";
import { coupons } from "@/database/index";
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
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (
      !data.code ||
      !data.percent_off ||
      typeof Number(data.percent_off) !== "number" ||
      Number.isNaN(Number(data.percent_off)) ||
      Number(data.order) < 1 ||
      Number(data.order) > 100 ||
      !data.one_time ||
      typeof Number(data.one_time) !== "number" ||
      Number.isNaN(Number(data.one_time)) ||
      Number(data.one_time) < 0 ||
      Number(data.one_time) > 1 ||
      !data.is_global ||
      typeof Number(data.is_global) !== "number" ||
      Number.isNaN(Number(data.is_global)) ||
      Number(data.is_global) < 0 ||
      Number(data.is_global) > 1
    ) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.update.error");
    }
    const couponId = params.id;
    if (!couponId || typeof Number(couponId) !== "number" || Number.isNaN(Number(couponId))) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.not_found");
    }
    const coupon = await coupons.findOneBy({ id: Number(couponId) });
    if (!coupon) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.not_found");
    }
    const existCouponWithSameCode = await coupons.exists({
      where: {
        code: data.code,
      },
    });
    if (existCouponWithSameCode && coupon.code !== data.code) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.exists");
    }
    if (data.end_date) {
      const endDate = new Date(data.end_date);
      if (isNaN(endDate.getTime())) {
        return redirect("/admin/coupons?type=danger&msg=admin.coupon.update.error");
      }
    }
    if (data.global_limit) {
      const globalLimit = Number(data.global_limit);
      if (isNaN(globalLimit)) {
        return redirect("/admin/coupons?type=danger&msg=admin.coupon.update.error");
      }
    }
    if (data.per_client_limit) {
      const perClientLimit = Number(data.per_client_limit);
      if (isNaN(perClientLimit)) {
        return redirect("/admin/coupons?type=danger&msg=admin.coupon.update.error");
      }
    }
    coupon.code = data.code;
    coupon.percentOff = Number(data.percent_off);
    coupon.oneTime = Number(data.one_time);
    coupon.globalLimit = data.global_limit ? Number(data.global_limit) : null;
    coupon.perClientLimit = data.per_client_limit ? Number(data.per_client_limit) : null;
    coupon.isGlobal = Number(data.is_global);
    coupon.endDate = data.end_date ? new Date(data.end_date) : null;
    coupon.updatedAt = new Date();
    await coupons.save(coupon);
    return redirect(`/admin/coupons/${coupon.id}?type=success&msg=admin.coupon.update.success`);
  }
  return redirect("/");
};
