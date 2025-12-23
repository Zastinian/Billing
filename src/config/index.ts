const APP_KEY = process.env.APP_KEY;

if (!APP_KEY) {
  throw new Error("APP_KEY is not set, use `bun run key:generate` to generate a new key");
}

export default {
  APP_KEY,
  STORE_URL: process.env.STORE_URL,
  DB_CONNECTION: process.env.DB_CONNECTION,
  DB_DATABASE: process.env.DB_DATABASE,
  DB_HOST: process.env.DB_HOST,
  DB_PASSWORD: process.env.DB_PASSWORD,
  DB_PORT: process.env.DB_PORT,
  DB_USERNAME: process.env.DB_USERNAME,
};
