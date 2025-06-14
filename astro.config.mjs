// @ts-check
import { defineConfig, envField } from "astro/config";
import bun from "@hedystia/astro-bun";

// https://astro.build/config
export default defineConfig({
  output: "server",
  adapter: bun(),
  server: {
    host: "0.0.0.0",
    port: 3000,
    allowedHosts: [(new URL(String(process.env.STORE_URL))).hostname]
  },
  devToolbar: {
    enabled: false,
  },
  env: {
    schema: {
      APP_KEY: envField.string({ context: "server", access: "secret" }),

      DB_CONNECTION: envField.string({ context: "server", access: "secret" }),
      DB_HOST: envField.string({ context: "server", access: "secret" }),
      DB_PORT: envField.number({ context: "server", access: "secret", optional: true }),
      DB_DATABASE: envField.string({ context: "server", access: "secret" }),
      DB_USERNAME: envField.string({ context: "server", access: "secret" }),
      DB_PASSWORD: envField.string({ context: "server", access: "secret" }),

      STORE_URL: envField.string({ context: "server", access: "public", url: true }),
    },
    validateSecrets: true,
  },
  vite: {
    server: {
      allowedHosts: [(new URL(String(process.env.STORE_URL))).hostname]
    },
    preview: {
      allowedHosts: [(new URL(String(process.env.STORE_URL))).hostname]
    }
  },
});
