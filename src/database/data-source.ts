import { DataSource } from "typeorm";

if (!import.meta.env.APP_KEY) {
  throw new Error("APP_KEY is not set, use `bun run --bun key:generate` to generate a new key");
}

import entities from "./entities";
import migrations from "./migrations";

const getDatabaseConfig = () => {
  switch (import.meta.env.DB_CONNECTION) {
    case "mariadb":
    case "mysql": {
      return {
        type: import.meta.env.DB_CONNECTION,
        host: import.meta.env.DB_HOST,
        port: Number(import.meta.env.DB_PORT || 3306),
        username: import.meta.env.DB_USERNAME,
        password: import.meta.env.DB_PASSWORD,
        database: import.meta.env.DB_DATABASE,
      };
    }
    case "postgres": {
      return {
        type: import.meta.env.DB_CONNECTION,
        host: import.meta.env.DB_HOST,
        port: Number(import.meta.env.DB_PORT || 5432),
        username: import.meta.env.DB_USERNAME,
        password: import.meta.env.DB_PASSWORD,
        database: import.meta.env.DB_DATABASE,
      };
    }
    case "cockroachdb": {
      return {
        type: import.meta.env.DB_CONNECTION,
        host: import.meta.env.DB_HOST,
        port: Number(import.meta.env.DB_PORT || 26257),
        username: import.meta.env.DB_USERNAME,
        password: import.meta.env.DB_PASSWORD,
        database: import.meta.env.DB_DATABASE,
        timeTravelQueries: true,
      };
    }
    case "sqlite": {
      return {
        type: import.meta.env.DB_CONNECTION,
        database: `${`${import.meta.env.DB_DATABASE}`.includes(".sqlite") ? import.meta.env.DB_DATABASE : `${import.meta.env.DB_DATABASE}.sqlite`}`,
      };
    }
    case "mssql": {
      return {
        type: import.meta.env.DB_CONNECTION,
        host: import.meta.env.DB_HOST,
        port: Number(import.meta.env.DB_PORT || 1433),
        username: import.meta.env.DB_USERNAME,
        password: import.meta.env.DB_PASSWORD,
        database: import.meta.env.DB_DATABASE,
      };
    }
    case "mongodb": {
      return {
        type: import.meta.env.DB_CONNECTION,
        host: import.meta.env.DB_HOST,
        port: Number(import.meta.env.DB_PORT || 27017),
        username: import.meta.env.DB_USERNAME,
        password: import.meta.env.DB_PASSWORD,
        database: import.meta.env.DB_DATABASE,
      };
    }
    default:
      throw new Error(
        `Unsupported DB_CONNECTION: ${import.meta.env.DB_CONNECTION}, available connections: mariadb, mysql, postgres, cockroachdb, sqlite, mssql, mongodb`,
      );
  }
};

const AppDataSource = new DataSource({
  ...getDatabaseConfig(),
  synchronize: false,
  logging: false,
  entities: entities,
  migrations: migrations,
  subscribers: [],
});

export default AppDataSource;
