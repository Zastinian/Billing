import { settings } from "@/database/index";

export const getCaptchaProvider = async (): Promise<string> => {
  const provider = await settings.findOneBy({ key: "captcha_provider" });
  return provider?.value || "disabled";
};

export const verifyHCaptcha = async (token: string, secretKey: string): Promise<boolean> => {
  try {
    const response = await fetch("https://hcaptcha.com/siteverify", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `response=${token}&secret=${secretKey}`,
    });

    if (!response.ok) {
      console.error("hCaptcha verification failed:", response.statusText);
      return false;
    }

    const data = await response.json();
    return data.success === true;
  } catch (error) {
    console.error("Error verifying hCaptcha:", error);
    return false;
  }
};

export const verifyTurnstile = async (token: string, secretKey: string): Promise<boolean> => {
  try {
    const response = await fetch("https://challenges.cloudflare.com/turnstile/v0/siteverify", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `response=${token}&secret=${secretKey}`,
    });

    if (!response.ok) {
      console.error("Turnstile verification failed:", response.statusText);
      return false;
    }

    const data = await response.json();
    return data.success === true;
  } catch (error) {
    console.error("Error verifying Turnstile:", error);
    return false;
  }
};

export const verifyCaptcha = async (token: string | null): Promise<boolean> => {
  const provider = await getCaptchaProvider();

  if (provider === "disabled") {
    return true;
  }

  if (!token) {
    console.error("CAPTCHA token required but not provided");
    return false;
  }

  if (provider === "hcaptcha") {
    const secretKey = await settings
      .findOneBy({ key: "hcaptcha_secret_key" })
      .then((s) => s?.getValue());

    if (!secretKey) {
      console.error("hCaptcha secret key not configured");
      return false;
    }

    return await verifyHCaptcha(token, secretKey);
  }
  if (provider === "turnstile") {
    const secretKey = await settings
      .findOneBy({ key: "turnstile_secret_key" })
      .then((s) => s?.getValue());

    if (!secretKey) {
      console.error("Turnstile secret key not configured");
      return false;
    }

    return await verifyTurnstile(token, secretKey);
  }

  console.error("Unknown CAPTCHA provider:", provider);
  return false;
};
