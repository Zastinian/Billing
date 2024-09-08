import * as fs from "fs";
import * as crypto from "crypto";

const appKey = crypto.randomBytes(32).toString("base64");
const envFilePath = ".env";

let envFileContent = fs.readFileSync(envFilePath, "utf-8");

envFileContent = envFileContent.includes("APP_KEY=")
    ? envFileContent.replace(/APP_KEY=.*/, `APP_KEY=base64:${appKey}`)
    : envFileContent + `\nAPP_KEY=base64:${appKey}`;

fs.writeFileSync(envFilePath, envFileContent, "utf-8");
console.log("App Key Generated!");
