import type { APIRoute } from "astro";
import { clients, invoices } from "@/database/index";
import { regeneratePaymentLink } from "@/utils/paymentGateways";
import profile from "@/utils/profile";
import { invoiceStatus } from "@/utils/status";

export const POST: APIRoute = async ({ cookies, params, redirect }) => {
  const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
  const c = profile(cookie);

  if (!c.success || !c.clientId) {
    return redirect("/?type=danger&msg=auth.invalid");
  }

  const client = await clients.findOneBy({ id: c.clientId });
  if (!client || client.email !== c.email || client.sessionToken !== c.sessionToken) {
    return redirect("/?type=danger&msg=auth.invalid");
  }

  const invoiceId = Number.parseInt(params.id || "0", 10);
  if (!invoiceId) {
    return redirect("/client/invoices?type=danger&msg=Invalid invoice ID");
  }

  try {
    const invoice = await invoices.findOneBy({ id: invoiceId });

    if (!invoice) {
      return redirect("/client/invoices?type=danger&msg=Invoice not found");
    }

    if (invoice.clientId !== client.id) {
      return redirect("/client/invoices?type=danger&msg=Unauthorized");
    }

    if (invoice.paid === invoiceStatus.paid) {
      return redirect(`/client/invoices/${invoiceId}?type=warning&msg=Invoice already paid`);
    }

    const newPaymentUrl = await regeneratePaymentLink(invoice);

    if (!newPaymentUrl) {
      return redirect(
        `/client/invoices/${invoiceId}?type=danger&msg=Failed to regenerate payment link`,
      );
    }

    return Response.redirect(newPaymentUrl, 302);
  } catch (error) {
    console.error("Error regenerating payment link:", error);
    return redirect(`/client/invoices/${invoiceId}?type=danger&msg=error`);
  }
};
