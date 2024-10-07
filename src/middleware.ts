import { defineMiddleware } from "astro:middleware";

// Rate limit and time frame settings
const PAGE_RATE_LIMIT = 75; // Maximum number of requests allowed for pages
const PAGE_TIME_FRAME = 60 * 1000; // Time window for page routes (1 minute)
const PAGE_BAN_TIME = 10 * 60 * 1000; // Ban time for pages in milliseconds (10 minutes)

const API_RATE_LIMIT = 5; // Maximum number of requests allowed for API routes
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

  const referrer = context.request.headers.get("referer") || "";
  const allowedReferrers = [import.meta.env.STORE_URL];
  if (!allowedReferrers.some((allowedRef) => referrer.startsWith(allowedRef))) {
    return new Response("Blocked: Are you a bot?", { status: 403 });
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

  return next();
});
