// @ts-check

import bun from "@hedystia/astro-bun";
import { defineConfig } from "astro/config";

// https://astro.build/config
export default defineConfig({
  output: "server",
  adapter: bun(),
  server: {
    host: "0.0.0.0",
    port: 3000,
    allowedHosts: [new URL(String(process.env.STORE_URL)).hostname],
  },
  devToolbar: {
    enabled: false,
  },
  env: {
    validateSecrets: true,
  },
  vite: {
    server: {
      allowedHosts: [new URL(String(process.env.STORE_URL)).hostname],
    },
    preview: {
      allowedHosts: [new URL(String(process.env.STORE_URL)).hostname],
    },
  },
});
