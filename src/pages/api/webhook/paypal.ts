import type { APIRoute } from "astro";
import { extensions, invoices } from "@/database/index";
import PayPal, { getPayPalSettings } from "@/utils/paypal";
import { createServerFromPayPalInvoice, refundInvoiceToClient } from "@/utils/paypalServerCreation";

export const POST: APIRoute = async ({ request }) => {
  try {
    const paypalConfig = await getPayPalSettings();
    if (!paypalConfig) {
      console.error("PayPal configuration not found");
      return new Response("PayPal not configured", { status: 500 });
    }

    const webhookEnabled = await extensions
      .findOneBy({ extension: "PayPal", key: "webhook_enabled" })
      .then((e) => e?.value === "1");

    if (!webhookEnabled) {
      console.log("PayPal webhook is disabled, use cron job instead");
      return new Response("Webhook disabled", { status: 200 });
    }

    const controller = new PayPal(paypalConfig);

    const transmissionId = request.headers.get("paypal-transmission-id");
    const transmissionTime = request.headers.get("paypal-transmission-time");
    const certUrl = request.headers.get("paypal-cert-url");
    const authAlgo = request.headers.get("paypal-auth-algo");
    const transmissionSig = request.headers.get("paypal-transmission-sig");

    const webhookId = await extensions
      .findOneBy({ extension: "PayPal", key: "webhook_id" })
      .then((e) => e?.value);

    if (
      !transmissionId ||
      !transmissionTime ||
      !certUrl ||
      !authAlgo ||
      !transmissionSig ||
      !webhookId
    ) {
      console.error("Missing webhook headers or webhook ID not configured");
      return new Response("Invalid webhook request", { status: 400 });
    }

    const webhookEvent = await request.json();

    const isValid = await controller.verifyWebhookSignature({
      transmissionId,
      transmissionTime,
      certUrl,
      authAlgo,
      transmissionSig,
      webhookId,
      webhookEvent,
    });

    if (!isValid) {
      console.error("Invalid webhook signature");
      return new Response("Invalid signature", { status: 401 });
    }

    console.log("PayPal webhook event:", webhookEvent.event_type);

    if (webhookEvent.event_type === "CHECKOUT.ORDER.APPROVED") {
      const orderId = webhookEvent.resource?.id;

      if (!orderId) {
        console.error("No order ID in webhook event");
        return new Response(undefined, { status: 204 });
      }

      const order = await controller.getOrder(orderId);
      if (!order || order.status !== "APPROVED") {
        console.error("Order not approved or not found:", order?.status);
        return new Response(undefined, { status: 204 });
      }

      const captureResult = await controller.capturePayment(orderId);
      if (!captureResult || captureResult.status !== "COMPLETED") {
        console.error("Failed to capture payment:", captureResult);
        return new Response(undefined, { status: 204 });
      }

      console.log("Payment captured successfully:", captureResult.id);

      const invoice = await invoices.findOne({
        where: {
          paymentLink: orderId,
          paymentMethod: "PayPal",
        },
      });

      if (!invoice) {
        console.error(`Invoice not found for PayPal order ${orderId}`);
        return new Response(undefined, { status: 204 });
      }

      console.log(`Processing invoice #${invoice.id} for PayPal order ${orderId}`);

      const result = await createServerFromPayPalInvoice(invoice.id);

      if (!result.success) {
        console.error(`Failed to create server for invoice #${invoice.id}:`, result.error);

        const refunded = await refundInvoiceToClient(invoice.id);

        if (refunded) {
          console.log(`Credits refunded to client for invoice #${invoice.id}`);
        } else {
          console.error(`Failed to refund credits for invoice #${invoice.id}`);
        }

        // Optionally refund PayPal payment
        // const refundResult = await controller.refundPayment(
        //   captureResult.id,
        //   invoice.total,
        //   "USD"
        // );
      } else {
        console.log(
          `Server created successfully for invoice #${invoice.id} (Server ID: ${result.serverId}, Pterodactyl ID: ${result.pterodactylId})`,
        );
      }
    }

    return new Response(undefined, { status: 204 });
  } catch (error) {
    console.error("Error processing PayPal webhook:", error);
    return new Response("Internal error", { status: 500 });
  }
};
