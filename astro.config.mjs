import { defineConfig } from "astro/config";

import bun from "@nurodev/astro-bun";

// https://astro.build/config
export default defineConfig({
    output: "server",
    adapter: bun(),
    server: {
        host: "0.0.0.0",
        port: 3000,
    },
    devToolbar: {
        enabled: false,
    },
});
