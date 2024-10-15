import { clients } from "@/database/index";
import { tickets, ticketContents } from "@/database/index";
import profile from "@/utils/profile";
import type { APIRoute } from "astro";
import { ticketStatus } from "@/utils/status";
import { TicketContents } from "@/database/entities/TicketContents";

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
    const params = new URLSearchParams(requestUrl.search);
    if (params.get("id") === undefined) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.error");
    }
    const ticket = await tickets
      .findOneBy({ id: Number(params.get("id")) })
      .then((ticket) => {
        if (ticket?.clientId === client?.id) {
          return ticket;
        }
      });
    if (!ticket) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.error");
    }
    if (Boolean(data.solved)) {
      ticket.status = ticketStatus.resolved;
      ticket.updatedAt = new Date();
      await tickets.save(ticket);
      return redirect(`/client/tickets?type=success&msg=client.tickets.success.solved`);
    }
    if (data.message && data.message.length > 5 && data.message.length <= 500) {
      const newTicketContent = new TicketContents();
      newTicketContent.ticketId = ticket.id;
      newTicketContent.replierId = client.id;
      newTicketContent.message = data.message;
      await ticketContents.save(newTicketContent);
      ticket.updatedAt = new Date();
      await tickets.save(ticket);
      return redirect(`/client/tickets/${ticket.id}`);
    }
    return redirect("/client/tickets?type=danger&msg=client.tickets.error");
  }
  return redirect("/");
};
