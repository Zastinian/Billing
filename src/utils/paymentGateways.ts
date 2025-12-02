import type { Invoices } from "@/database/entities/Invoices";
import { extensions, invoices } from "@/database/index";

export const getEnabledGateways = async (): Promise<{ name: string; displayName: string }[]> => {
  const enabledGateways: { name: string; displayName: string }[] = [];

  const paypalEnabled = await extensions.findOneBy({ extension: "PayPal", key: "enabled" });
  if (paypalEnabled?.value === "1") {
    enabledGateways.push({ name: "PayPal", displayName: "PayPal" });
  }

  // const mercadopagoEnabled = await extensions.findOneBy({ extension: "MercadoPago", key: "enabled" });
  // if (mercadopagoEnabled?.value === "1") {
  //   enabledGateways.push({ name: "MercadoPago", displayName: "Mercado Pago" });
  // }

  return enabledGateways;
};

export const generatePayPalLink = async (invoice: Invoices): Promise<string | null> => {
  try {
    const clientId = await extensions
      .findOneBy({ extension: "PayPal", key: "client_id" })
      .then((e) => e?.value);
    const clientSecret = await extensions
      .findOneBy({ extension: "PayPal", key: "client_secret" })
      .then((e) => e?.getValue());
    const mode = await extensions
      .findOneBy({ extension: "PayPal", key: "mode" })
      .then((e) => e?.value);

    if (!clientId || !clientSecret) {
      console.error("PayPal configuration not found");
      return null;
    }

    const baseUrl =
      mode === "sandbox" ? "https://api-m.sandbox.paypal.com" : "https://api-m.paypal.com";

    const authResponse = await fetch(`${baseUrl}/v1/oauth2/token`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Basic ${Buffer.from(`${clientId}:${clientSecret}`).toString("base64")}`,
      },
      body: "grant_type=client_credentials",
    });

    if (!authResponse.ok) {
      console.error("Failed to get PayPal access token");
      return null;
    }

    const authData = await authResponse.json();
    const accessToken = authData.access_token;

    const orderResponse = await fetch(`${baseUrl}/v2/checkout/orders`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${accessToken}`,
      },
      body: JSON.stringify({
        intent: "CAPTURE",
        purchase_units: [
          {
            amount: {
              currency_code: "USD",
              value: invoice.total.toFixed(2),
            },
            description: invoice.credit
              ? `Credit Purchase - Invoice #${invoice.id}`
              : `Server Purchase - Invoice #${invoice.id}`,
          },
        ],
        application_context: {
          return_url: `${process.env.STORE_URL || "http://localhost:3000"}/client/invoices/${invoice.id}`,
          cancel_url: `${process.env.STORE_URL || "http://localhost:3000"}/client/invoices/${invoice.id}`,
        },
      }),
    });

    if (!orderResponse.ok) {
      console.error("Failed to create PayPal order");
      return null;
    }

    const orderData = await orderResponse.json();

    const approvalUrl = orderData.links?.find((link: any) => link.rel === "approve")?.href;

    if (!approvalUrl) {
      console.error("No approval URL in PayPal order response");
      return null;
    }

    await invoices.update(invoice.id, {
      paymentLink: orderData.id,
      paymentMethod: "PayPal",
    });

    return approvalUrl;
  } catch (error) {
    console.error("Error generating PayPal link:", error);
    return null;
  }
};

export const regeneratePaymentLink = async (invoice: Invoices): Promise<string | null> => {
  if (!invoice.paymentMethod) {
    console.error("No payment method set for invoice");
    return null;
  }

  switch (invoice.paymentMethod) {
    case "PayPal":
      return await generatePayPalLink(invoice);
    default:
      console.error(`Unknown payment method: ${invoice.paymentMethod}`);
      return null;
  }
};
