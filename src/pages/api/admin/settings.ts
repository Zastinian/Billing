import { clients } from "@/database/index";
import { settings } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";
import { Settings } from "@/database/entities/Settings";

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
      !data.company_name ||
      !data.logo_path ||
      !data.favicon_path ||
      !data.open_registration ||
      !data.panel_url ||
      !data.panel_client_api_key ||
      !data.panel_app_api_key ||
      typeof Number(data.open_registration) !== "number" ||
      Number.isNaN(Number(data.open_registration)) ||
      Number(data.open_registration) < 0 ||
      Number(data.open_registration) > 1
    ) {
      return redirect("/admin/settings?type=danger&msg=admin.settings.error");
    }
    const setting_model = await settings
      .findOneBy({ key: "company_name" });
    if (!setting_model) {
      const newSetting = new Settings();
      newSetting.key = "company_name";
      newSetting.value = data.company_name;
      await settings.save(newSetting);
    } else {
      setting_model.value = data.company_name;
      await settings.save(setting_model);
    }
    const logo_path = await settings.findOneBy({ key: "logo_path" });
    if (!logo_path) {
      const newSetting = new Settings();
      newSetting.key = "logo_path";
      newSetting.value = data.logo_path;
      await settings.save(newSetting);
    } else {
      logo_path.value = data.logo_path;
      await settings.save(logo_path);
    }
    const favicon_path = await settings
      .findOneBy({ key: "favicon_path" });
    if (!favicon_path) {
      const newSetting = new Settings();
      newSetting.key = "favicon_path";
      newSetting.value = data.favicon_path;
      await settings.save(newSetting);
    } else {
      favicon_path.value = data.favicon_path;
      await settings.save(favicon_path);
    }
    const openRegistrationBooleanCheck: { [key: number]: string } = {
      0: "false",
      1: "true",
    };
    const open_registration = await settings
      .findOneBy({ key: "open_registration" });
    if (!open_registration) {
      const newSetting = new Settings();
      newSetting.key = "open_registration";
      newSetting.value = openRegistrationBooleanCheck[Number(data.open_registration)] ?? "true";
      await settings.save(newSetting);
    } else {
      open_registration.value =
        openRegistrationBooleanCheck[Number(data.open_registration)] ?? "true";
      await settings.save(open_registration);
    }
    const panel_url = await settings.findOneBy({ key: "panel_url" });
    if (!panel_url) {
      const newSetting = new Settings();
      newSetting.key = "panel_url";
      newSetting.value = data.panel_url;
      await settings.save(newSetting);
    } else {
      panel_url.value = data.panel_url;
      await settings.save(panel_url);
    }
    const panel_client_api_key = await settings
      .findOneBy({ key: "panel_client_api_key" });
    if (!panel_client_api_key) {
      const newSetting = new Settings();
      newSetting.key = "panel_client_api_key";
      newSetting.value = data.panel_client_api_key;
      await settings.save(newSetting);
    } else {
      panel_client_api_key.value = data.panel_client_api_key;
      await settings.save(panel_client_api_key);
    }
    const panel_app_api_key = await settings
      .findOneBy({ key: "panel_app_api_key" });
    if (!panel_app_api_key) {
      const newSetting = new Settings();
      newSetting.key = "panel_app_api_key";
      newSetting.value = data.panel_app_api_key;
      await settings.save(newSetting);
    } else {
      panel_app_api_key.value = data.panel_app_api_key;
      await settings.save(panel_app_api_key);
    }
    const discord_url = await settings.findOneBy({ key: "discord_url" });
    if (!discord_url) {
      const newSetting = new Settings();
      newSetting.key = "discord_url";
      newSetting.value = data.discord_url ?? null;
      await settings.save(newSetting);
    } else {
      discord_url.value = data.discord_url ?? null;
      await settings.save(discord_url);
    }
    const phpmyadmin_url = await settings
      .findOneBy({ key: "phpmyadmin_url" });
    if (!phpmyadmin_url) {
      const newSetting = new Settings();
      newSetting.key = "phpmyadmin_url";
      newSetting.value = data.phpmyadmin_url ?? null;
      await settings.save(newSetting);
    } else {
      phpmyadmin_url.value = data.phpmyadmin_url ?? null;
      await settings.save(phpmyadmin_url);
    }
    const hcaptcha_site_key = await settings
      .findOneBy({ key: "hcaptcha_site_key" });
    if (!hcaptcha_site_key) {
      const newSetting = new Settings();
      newSetting.key = "hcaptcha_site_key";
      newSetting.value = data.hcaptcha_site_key ?? null;
      await settings.save(newSetting);
    } else {
      hcaptcha_site_key.value = data.hcaptcha_site_key ?? null;
      await settings.save(hcaptcha_site_key);
    }
    const hcaptcha_secret_key = await settings
      .findOneBy({ key: "hcaptcha_secret_key" });
    if (!hcaptcha_secret_key) {
      const newSetting = new Settings();
      newSetting.key = "hcaptcha_secret_key";
      newSetting.value = data.hcaptcha_secret_key ?? null;
      await settings.save(newSetting);
    } else {
      hcaptcha_secret_key.value = data.hcaptcha_secret_key ?? null;
      await settings.save(hcaptcha_secret_key);
    }
    const google_analytics_id = await settings
      .findOneBy({ key: "google_analytics_id" });
    if (!google_analytics_id) {
      const newSetting = new Settings();
      newSetting.key = "google_analytics_id";
      newSetting.value = data.google_analytics_id ?? null;
      await settings.save(newSetting);
    } else {
      google_analytics_id.value = data.google_analytics_id ?? null;
      await settings.save(google_analytics_id);
    }
    return redirect("/admin/settings?type=success&msg=admin.settings.success");
  }
  return redirect("/");
};
