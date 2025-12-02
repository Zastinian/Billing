import type { APIRoute } from "astro";
import { TicketContents } from "@/database/entities/TicketContents";
import { clients, ticketContents, tickets } from "@/database/index";
import profile from "@/utils/profile";
import { ticketStatus } from "@/utils/status";

export const POST: APIRoute = async ({ cookies, request, redirect, params }) => {
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
    if (params.id === undefined) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.error");
    }
    const ticket = await tickets.findOneBy({ id: Number(params.id) }).then((ticket) => {
      if (ticket?.clientId === client?.id) {
        return ticket;
      }
    });
    if (!ticket) {
      return redirect("/client/tickets?type=danger&msg=client.tickets.error");
    }
    if (data.solved) {
      ticket.status = ticketStatus.resolved;
      ticket.updatedAt = new Date();
      await tickets.save(ticket);
      return redirect("/client/tickets?type=success&msg=client.tickets.success.solved");
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
