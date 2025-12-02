import type { APIRoute } from "astro";
import { Credits } from "@/database/entities/Credits";
import { clients, credits as creditsRepo, invoices } from "@/database/index";
import { createServerFromPayPalInvoice } from "@/utils/paypalServerCreation";
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

    const clientCredit = client.credit || 0;
    if (clientCredit < invoice.total) {
      return redirect(
        `/client/invoices/${invoiceId}?type=danger&msg=Insufficient credit balance (Have: ${clientCredit}, Need: ${invoice.total})`,
      );
    }

    client.credit = clientCredit - invoice.total;
    client.updatedAt = new Date();
    await clients.save(client);

    const creditTransaction = new Credits();
    creditTransaction.clientId = client.id;
    creditTransaction.change = -invoice.total;
    creditTransaction.balance = client.credit;
    creditTransaction.details = invoice.credit
      ? `Credit purchase - Invoice #${invoice.id}`
      : `Server purchase - Invoice #${invoice.id}`;
    creditTransaction.createdAt = new Date();
    await creditsRepo.save(creditTransaction);

    invoice.paid = invoiceStatus.paid;
    invoice.paymentMethod = "Credits";
    invoice.updatedAt = new Date();
    await invoices.save(invoice);

    if (invoice.credit) {
      client.credit = client.credit + invoice.credit;
      await clients.save(client);

      const creditAddition = new Credits();
      creditAddition.clientId = client.id;
      creditAddition.change = invoice.credit;
      creditAddition.balance = client.credit;
      creditAddition.details = `Credit added - Invoice #${invoice.id}`;
      creditAddition.createdAt = new Date();
      await creditsRepo.save(creditAddition);
    }

    if (invoice.serverId) {
      try {
        await createServerFromPayPalInvoice(invoice.id);
      } catch (error) {
        console.error("Error creating server after credit payment:", error);
      }
    }

    return redirect(`/client/invoices/${invoiceId}?type=success&msg=Payment successful`);
  } catch (error) {
    console.error("Error processing credit payment:", error);
    return redirect(`/client/invoices/${invoiceId}?type=danger&msg=error`);
  }
};
