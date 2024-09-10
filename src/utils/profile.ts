import jwt from "jsonwebtoken";
import type profileType from "@/types/profile";

export default function profile(myToken: string) {
  let clientId: number | null = null;
  let email: string | null = null;
  let success = true;
  switch (myToken) {
    case "undefined":
      success = false;
      return { success: success, clientId: clientId, email: email };
    case undefined:
      success = false;
      return { success: success, clientId: clientId, email: email };
    case "null":
      success = false;
      return { success: success, clientId: clientId, email: email };
    case null:
      success = false;
      return { success: success, clientId: clientId, email: email };
    default: {
      const data = jwt.verify(myToken, import.meta.env.APP_KEY) as profileType;
      if (data.clientId) {
        clientId = data.clientId;
        email = data.email;
      } else {
        success = false;
      }
      return { success: success, clientId: clientId, email: email };
    }
  }
}
