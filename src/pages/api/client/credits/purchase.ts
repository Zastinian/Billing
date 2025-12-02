import type { APIRoute } from "astro";
import { Invoices } from "@/database/entities/Invoices";
import { clients, invoices } from "@/database/index";
import { generatePayPalLink, getEnabledGateways } from "@/utils/paymentGateways";
import profile from "@/utils/profile";
import { invoiceStatus } from "@/utils/status";

export const POST: APIRoute = async ({ cookies, request, redirect }) => {
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);

  if (!c.success || !c.clientId) {
    return redirect("/?type=danger&msg=auth.invalid");
  }

  const client = await clients.findOneBy({ id: c.clientId });
  if (!client || client.email !== c.email || client.sessionToken !== c.sessionToken) {
    return redirect("/?type=danger&msg=auth.invalid");
  }

  try {
    const data = Object.fromEntries(new URLSearchParams(await request.text()));

    const creditAmount = Number.parseFloat(data.credit);
    if (
      !data.credit ||
      Number.isNaN(creditAmount) ||
      creditAmount < 0.01 ||
      creditAmount > 999999.99
    ) {
      return redirect(
        "/client/credits?type=danger&msg=Invalid credit amount (min: 0.01, max: 999999.99)",
      );
    }

    if (!data.gateway) {
      return redirect("/client/credits?type=danger&msg=Please select a payment method");
    }

    const enabledGateways = await getEnabledGateways();
    const selectedGateway = enabledGateways.find((g) => g.name === data.gateway);

    if (!selectedGateway) {
      return redirect("/client/credits?type=danger&msg=Selected payment method is not available");
    }

    const invoice = new Invoices();
    invoice.clientId = client.id;
    invoice.serverId = null;
    invoice.total = creditAmount;
    invoice.credit = creditAmount;
    invoice.paymentMethod = selectedGateway.name;
    invoice.paid = invoiceStatus.pending;
    invoice.dueDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
    invoice.createdAt = new Date();
    invoice.updatedAt = new Date();

    const savedInvoice = await invoices.save(invoice);

    let paymentUrl: string | null = null;

    if (selectedGateway.name === "PayPal") {
      paymentUrl = await generatePayPalLink(savedInvoice);
    }

    if (!paymentUrl) {
      return redirect("/client/credits?type=danger&msg=Failed to generate payment link");
    }

    return redirect(`/client/invoices/${savedInvoice.id}`);
  } catch (error) {
    console.error("Error creating credit purchase invoice:", error);
    return redirect("/client/credits?type=danger&msg=error");
  }
};
