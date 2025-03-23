import jwt from "jsonwebtoken";
import type profileType from "@/types/profile";
import { APP_KEY } from "astro:env/server";

export default function profile(myToken: string) {
  let clientId: number | null = null;
  let email: string | null = null;
  let sessionToken: string | null = null;
  let success = true;
  switch (myToken) {
    case "undefined":
      success = false;
      return { success: success, clientId: clientId, email: email, sessionToken: sessionToken };
    case undefined:
      success = false;
      return { success: success, clientId: clientId, email: email, sessionToken: sessionToken };
    case "null":
      success = false;
      return { success: success, clientId: clientId, email: email, sessionToken: sessionToken };
    case null:
      success = false;
      return { success: success, clientId: clientId, email: email, sessionToken: sessionToken };
    default: {
      const data = jwt.verify(myToken, APP_KEY) as profileType;
      if (data.clientId) {
        clientId = data.clientId;
        email = data.email;
        sessionToken = data.sessionToken;
      } else {
        success = false;
      }
      return { success: success, clientId: clientId, email: email, sessionToken: sessionToken };
    }
  }
}
