import { clients, coupons, plans, usedCoupons } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";
import { STORE_URL } from "astro:env/server";
import { Like } from "typeorm";

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
    const couponId = params.id;
    if (!couponId || typeof Number(couponId) !== "number" || Number.isNaN(Number(couponId))) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.not_found");
    }
    const coupon = await coupons.findOneBy({ id: Number(couponId) });
    if (!coupon) {
      return redirect("/admin/coupons?type=danger&msg=admin.coupon.not_found");
    }
    const usedCouponData = await usedCoupons.find({
      where: {
        couponId: coupon.id,
      },
    });
    for (const usedCoupon of usedCouponData) {
      await usedCoupons.remove(usedCoupon);
    }
    const plansData = await plans.find({
      where: { coupons: Like(`%${coupon.id},%`) },
      select: ["id", "coupons"],
    });
    for (const plan of plansData) {
      if (!plan.coupons) continue;
      const newCoupons = plan.coupons
        .split(",")
        .filter((couponId) => Number(couponId) !== coupon.id);
      await plans.update(plan.id, { coupons: newCoupons.join(",") });
    }
    await coupons.remove(coupon);
    return redirect(`/admin/coupons?type=success&msg=admin.coupon.delete.success`);
  }
  return redirect("/");
};
