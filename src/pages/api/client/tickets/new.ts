import { clients } from "@/database/index";
import { tickets, ticketContents } from "@/database/index";
import { Tickets } from "@/database/entities/Tickets";
import { TicketContents } from "@/database/entities/TicketContents";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";

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
    if (client.sessionToken !== c.sessionToken) {
      return redirect("/");
    }
    const data = Object.fromEntries(new URLSearchParams(await request.text()));
    if (!data.subject || !data.message) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.create.error.general");
    }
    if (data.subject.length > 255) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.create.error.subject");
    }
    if (data.message.length > 500) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.create.error.message");
    }
    if (data.subject.length < 5) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.create.error.subject");
    }
    if (data.message.length < 30) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.create.error.message");
    }
    const newTicket = new Tickets();
    newTicket.subject = data.subject;
    newTicket.clientId = client.id;
    await tickets.save(newTicket);
    const newTicketContent = new TicketContents();
    newTicketContent.ticketId = newTicket.id;
    newTicketContent.replierId = client.id;
    newTicketContent.message = data.message;
    await ticketContents.save(newTicketContent);
    return redirect(`/client/tickets/${newTicket.id}`);
  }
  return redirect("/");
};
