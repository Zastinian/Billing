import type { APIRoute } from "astro";
import { clients, servers } from "@/database/index";
import profile from "@/utils/profile";
import { getPterodactylSettings, suspendPterodactylServer } from "@/utils/pterodactylServer";
import { serverStatus } from "@/utils/status";

export const POST: APIRoute = async ({ cookies, params }) => {
  try {
    const cookie: string = `${cookies.get("_SECURE_SESSION_TOKEN_")?.value}`;
    const c = profile(cookie);

    if (!c.success || !c.clientId || !c.email || !c.sessionToken) {
      return new Response(JSON.stringify({ error: "Unauthorized" }), {
        status: 401,
        headers: { "Content-Type": "application/json" },
      });
    }

    const admin = await clients.findOneBy({ id: c.clientId });
    if (
      !admin ||
      admin.email !== c.email ||
      admin.sessionToken !== c.sessionToken ||
      admin.isAdmin !== 1
    ) {
      return new Response(JSON.stringify({ error: "Unauthorized" }), {
        status: 401,
        headers: { "Content-Type": "application/json" },
      });
    }

    const serverId = params.id;
    if (!serverId || Number.isNaN(Number(serverId))) {
      return new Response(JSON.stringify({ error: "Invalid server ID" }), {
        status: 400,
        headers: { "Content-Type": "application/json" },
      });
    }

    const server = await servers.findOneBy({ id: Number(serverId) });
    if (!server) {
      return new Response(JSON.stringify({ error: "Server not found" }), {
        status: 404,
        headers: { "Content-Type": "application/json" },
      });
    }

    if (server.status === serverStatus.suspended) {
      return new Response(JSON.stringify({ error: "Server is already suspended" }), {
        status: 400,
        headers: { "Content-Type": "application/json" },
      });
    }

    if (server.status === serverStatus.canceled || server.status === serverStatus.terminated) {
      return new Response(
        JSON.stringify({ error: "Cannot suspend a canceled or terminated server" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json" },
        },
      );
    }

    const pterodactylSettings = await getPterodactylSettings();
    if (!pterodactylSettings) {
      return new Response(JSON.stringify({ error: "Pterodactyl settings not configured" }), {
        status: 500,
        headers: { "Content-Type": "application/json" },
      });
    }

    if (server.serverId) {
      const suspended = await suspendPterodactylServer(server.serverId, pterodactylSettings);
      if (!suspended) {
        return new Response(JSON.stringify({ error: "Failed to suspend server in Pterodactyl" }), {
          status: 500,
          headers: { "Content-Type": "application/json" },
        });
      }
    }

    server.status = serverStatus.suspended;
    server.updatedAt = new Date();
    await servers.save(server);

    return new Response(JSON.stringify({ success: "Server suspended successfully" }), {
      status: 200,
      headers: { "Content-Type": "application/json" },
    });
  } catch (error) {
    console.error("Error suspending server:", error);
    return new Response(JSON.stringify({ error: "Internal server error" }), {
      status: 500,
      headers: { "Content-Type": "application/json" },
    });
  }
};
