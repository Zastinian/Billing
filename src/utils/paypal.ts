import { extensions } from "@/database/index";

interface PayPalConfig {
  clientId: string;
  clientSecret: string;
  sandboxMode: boolean;
}

interface PayPalAccessToken {
  access_token: string;
  token_type: string;
  expires_in: number;
  scope: string;
}

interface PayPalOrderResponse {
  id: string;
  status: string;
  purchase_units?: any[];
}

interface PayPalCaptureResponse {
  id: string;
  status: string;
}

export const getPayPalSettings = async (): Promise<PayPalConfig | null> => {
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
    console.error("PayPal configuration not found in extensions");
    return null;
  }

  return {
    clientId,
    clientSecret,
    sandboxMode: mode === "sandbox",
  };
};

export default class PayPal {
  public readonly baseUrl: string;

  private readonly clientId: string;
  private readonly clientSecret: string;

  private accessToken: string | null = null;
  private tokenExpiry = 0;

  constructor(config: PayPalConfig) {
    this.baseUrl = config.sandboxMode
      ? "https://api-m.sandbox.paypal.com"
      : "https://api-m.paypal.com";
    this.clientId = config.clientId;
    this.clientSecret = config.clientSecret;
  }

  public async getAccessToken(): Promise<string> {
    if (this.accessToken && Date.now() < this.tokenExpiry) {
      return this.accessToken;
    }
    const auth = Buffer.from(`${this.clientId}:${this.clientSecret}`).toString("base64");
    const response = await fetch(`${this.baseUrl}/v1/oauth2/token`, {
      method: "POST",
      headers: {
        Authorization: `Basic ${auth}`,
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "grant_type=client_credentials",
    });
    if (!response.ok) {
      throw new Error(`Failed to get access token: ${response.statusText}`);
    }
    const data: PayPalAccessToken = await response.json();
    this.accessToken = data.access_token;
    this.tokenExpiry = Date.now() + data.expires_in * 1000 - 60000;
    return this.accessToken;
  }

  public async getOrder(orderId: string): Promise<PayPalOrderResponse | null> {
    try {
      const token = await this.getAccessToken();
      const response = await fetch(`${this.baseUrl}/v2/checkout/orders/${orderId}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        console.error(`Failed to get PayPal order: ${response.statusText}`);
        return null;
      }

      return await response.json();
    } catch (error) {
      console.error("Error getting PayPal order:", error);
      return null;
    }
  }

  public async capturePayment(orderId: string): Promise<PayPalCaptureResponse | null> {
    try {
      const token = await this.getAccessToken();
      const response = await fetch(`${this.baseUrl}/v2/checkout/orders/${orderId}/capture`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        const errorData = await response.json();
        console.error("Failed to capture PayPal payment:", errorData);
        return null;
      }

      return await response.json();
    } catch (error) {
      console.error("Error capturing PayPal payment:", error);
      return null;
    }
  }

  public async refundPayment(
    captureId: string,
    amount: number,
    currency = "USD",
  ): Promise<boolean> {
    try {
      const token = await this.getAccessToken();
      const response = await fetch(`${this.baseUrl}/v2/payments/captures/${captureId}/refund`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          amount: {
            value: amount.toFixed(2),
            currency_code: currency,
          },
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        console.error("Failed to refund PayPal payment:", errorData);
        return false;
      }

      console.log(`PayPal refund processed for capture ${captureId}`);
      return true;
    } catch (error) {
      console.error("Error refunding PayPal payment:", error);
      return false;
    }
  }

  public async verifyWebhookSignature({
    transmissionId,
    transmissionTime,
    certUrl,
    authAlgo,
    transmissionSig,
    webhookId,
    webhookEvent,
  }: {
    transmissionId: string;
    transmissionTime: string;
    certUrl: string;
    authAlgo: string;
    transmissionSig: string;
    webhookId: string;
    webhookEvent: object;
  }): Promise<boolean> {
    try {
      const token = await this.getAccessToken();
      const response = await fetch(`${this.baseUrl}/v1/notifications/verify-webhook-signature`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          transmission_id: transmissionId,
          transmission_time: transmissionTime,
          cert_url: certUrl,
          auth_algo: authAlgo,
          transmission_sig: transmissionSig,
          webhook_id: webhookId,
          webhook_event: webhookEvent,
        }),
      });

      if (!response.ok) {
        console.error("Webhook signature verification failed");
        return false;
      }

      const data = await response.json();
      return data.verification_status === "SUCCESS";
    } catch (error) {
      console.error("Error verifying webhook signature:", error);
      return false;
    }
  }
}
