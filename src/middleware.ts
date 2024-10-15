import { defineMiddleware } from "astro:middleware";
import profile from "@/utils/profile";
import { clients } from "@/database/index";

// Rate limit and time frame settings
const PAGE_RATE_LIMIT = 75; // Maximum number of requests allowed for pages
const PAGE_TIME_FRAME = 60 * 1000; // Time window for page routes (1 minute)
const PAGE_BAN_TIME = 10 * 60 * 1000; // Ban time for pages in milliseconds (10 minutes)

const API_RATE_LIMIT = 1000; // Maximum number of requests allowed for API routes
const API_TIME_FRAME = 5 * 60 * 1000; // Time window for API routes (5 minutes)
const API_BAN_TIME = 5 * 60 * 1000; // Ban time for API in milliseconds (5 minutes)

const MAX_REQUEST_SIZE = 1 * 1024 * 1024; // Maximum request size in bytes

const sessionRequestsMap = new Map<
  string,
  { requests: number; lastRequestTime: number; bannedUntil?: number }
>();

const generateSessionID = () => Math.random().toString(36).slice(2, 24);

export const onRequest = defineMiddleware(async (context, next) => {
  let sessionID: string | null = null;

  const storeUrl = new URL(import.meta.env.STORE_URL ?? "");
  const requestUrl = new URL(context.request.url);
  if (requestUrl.origin !== storeUrl.origin) {
    return new Response("Blocked: Are you a bot?", { status: 403 });
  }

  if (["OPTIONS", "HEAD"].includes(context.request.method)) {
    return new Response(null, { status: 204 });
  }

  if (["POST", "PUT", "PATCH"].includes(context.request.method)) {
    // Limit request body size (preventing excessive payloads)
    const contentLength = parseInt(context.request.headers.get("content-length") || "0", 10);
    if (contentLength > MAX_REQUEST_SIZE) {
      return new Response("Blocked: Request too large", { status: 413 });
    }
  }

  const referrer = context.request.headers.get("referer");
  const allowedReferrers = [new URL(import.meta.env.STORE_URL ?? "").origin];

  if (referrer) {
    const referrerOrigin = new URL(referrer).origin;
    if (!allowedReferrers.some((allowedRef) => referrerOrigin.startsWith(allowedRef))) {
      return new Response("Blocked: Are you a bot?", { status: 403 });
    }
  }

  const connectSidId = context.cookies.get("connect.sid.id");

  if (connectSidId && connectSidId.value) {
    sessionID = connectSidId.value;
  }

  if (!sessionID) {
    sessionID = generateSessionID();
    context.cookies.set("connect.sid.id", sessionID, { path: "/" });
  } else {
    const connectSidId = context.cookies.get("connect.sid.id");
    if (!connectSidId) {
      return new Response("Blocked: No connection ID found. Are you a bot?", { status: 403 });
    }
  }

  const currentTime = Date.now();
  const sessionData = sessionRequestsMap.get(sessionID) || {
    requests: 0,
    lastRequestTime: currentTime,
  };

  // Check if the session is currently banned
  if (sessionData.bannedUntil && currentTime < sessionData.bannedUntil) {
    return new Response("Too many requests. Please try again later.", { status: 429 });
  }

  // Determine if the request is for an API route or a page route
  const isApiRoute = requestUrl.pathname.startsWith("/api");

  // Set the appropriate rate limit, time frame, and ban time based on the route type
  const rateLimit = isApiRoute ? API_RATE_LIMIT : PAGE_RATE_LIMIT;
  const timeFrame = isApiRoute ? API_TIME_FRAME : PAGE_TIME_FRAME;
  const banTime = isApiRoute ? API_BAN_TIME : PAGE_BAN_TIME;

  // Reset the request count if the time frame has passed
  if (currentTime - sessionData.lastRequestTime > timeFrame) {
    sessionData.requests = 0;
    sessionData.lastRequestTime = currentTime;
  }

  sessionData.requests++;

  // If the request count exceeds the rate limit, ban the session
  if (sessionData.requests > rateLimit) {
    sessionData.bannedUntil = currentTime + banTime;
    sessionRequestsMap.set(sessionID, sessionData);
    const banTimeInMinutes = banTime / (60 * 1000);
    return new Response(`Rate limit exceeded. You are banned for ${banTimeInMinutes} minutes.`, {
      status: 429,
    });
  }

  // Update the session data with the current request count and time
  sessionRequestsMap.set(sessionID, sessionData);

  const cookie = context.request.headers.get("cookie");
  if (cookie) {
    const getSessionToken = (cookieString: string): string | null => {
      const match = cookieString.match(/_SECURE_SESSION_TOKEN_=([^;]+)/);
      return match ? match[1] : null;
    };
    const sessionToken = getSessionToken(cookie);
    if (sessionToken) {
      const c = profile(sessionToken);
      if (c.success === true && c.clientId !== null) {
        if (!c.sessionToken) {
          context.cookies.set("_SECURE_SESSION_TOKEN_", sessionToken, {
            path: "/",
            maxAge: 0,
            sameSite: "strict",
            secure: true,
          });
          return next();
        }
        const client = await clients
          .findOneBy({ id: c.clientId })
          .then((client) => {
            if (client?.email === c.email) {
              return client;
            }
          });
        if (!client) {
          context.cookies.set("_SECURE_SESSION_TOKEN_", sessionToken, {
            path: "/",
            maxAge: 0,
            sameSite: "strict",
            secure: true,
          });
          return next();
        }
        if (client.sessionToken !== c.sessionToken) {
          context.cookies.set("_SECURE_SESSION_TOKEN_", sessionToken, {
            path: "/",
            maxAge: 0,
            sameSite: "strict",
            secure: true,
          });
          return next();
        }
      }
    }
  }

  const isAdminRoute = requestUrl.pathname.startsWith("/admin");
  const isClientRoute = requestUrl.pathname.startsWith("/client");
  const isOrderRoute = requestUrl.pathname.startsWith("/order");
  const isAdminApiRoute = requestUrl.pathname.startsWith("/api/admin");
  const isLoginRoute = requestUrl.pathname.startsWith("/api/auth/login");
  const isRegisterRoute = requestUrl.pathname.startsWith("/api/auth/register");
  if (
    isAdminRoute ||
    isClientRoute ||
    isOrderRoute ||
    (isApiRoute && !isLoginRoute && !isRegisterRoute)
  ) {
    if (!cookie) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    const getSessionToken = (cookieString: string): string | null => {
      const match = cookieString.match(/_SECURE_SESSION_TOKEN_=([^;]+)/);
      return match ? match[1] : null;
    };
    const sessionToken = getSessionToken(cookie);
    if (!sessionToken) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    const c = profile(sessionToken);
    if (c.success !== true || !c.clientId) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    if (!c.sessionToken) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    const client = await clients
      .findOneBy({ id: c.clientId })
      .then((client) => {
        if (client?.email === c.email) {
          return client;
        }
      });
    if (!client) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    if (client.sessionToken !== c.sessionToken) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    if (client.email !== c.email) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/404");
    }
    if (client.isAdmin !== 1) {
      if (isOrderRoute) {
        return next("/?type=danger&msg=order.needs_login");
      }
      return next("/?type=danger&msg=client.suspended");
    }
    if (isAdminRoute && client.isAdmin !== 1) {
      return next("/404");
    }
    if (isAdminApiRoute && client.isAdmin !== 1) {
      return next("/404");
    }
  }

  return next();
});
