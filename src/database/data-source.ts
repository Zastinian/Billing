import path from "path";
import { DataSource } from "typeorm";
import config from "../config";

const { DB_CONNECTION, DB_DATABASE, DB_HOST, DB_PASSWORD, DB_PORT, DB_USERNAME, STORE_URL } =
  config;

if (!STORE_URL) {
  throw new Error("STORE_URL is not set in .env");
}

import { default as entitiesData } from "./entities";
import { default as migrationsData } from "./migrations";

const getDatabaseConfig = () => {
  switch (DB_CONNECTION) {
    case "mariadb":
    case "mysql": {
      return {
        type: DB_CONNECTION,
        host: DB_HOST,
        port: Number(DB_PORT || 3306),
        username: DB_USERNAME,
        password: DB_PASSWORD,
        database: DB_DATABASE,
      };
    }
    case "postgres": {
      return {
        type: DB_CONNECTION,
        host: DB_HOST,
        port: Number(DB_PORT || 5432),
        username: DB_USERNAME,
        password: DB_PASSWORD,
        database: DB_DATABASE,
      };
    }
    case "cockroachdb": {
      return {
        type: DB_CONNECTION,
        host: DB_HOST,
        port: Number(DB_PORT || 26257),
        username: DB_USERNAME,
        password: DB_PASSWORD,
        database: DB_DATABASE,
        timeTravelQueries: true,
      };
    }
    case "sqlite": {
      return {
        type: DB_CONNECTION,
        database: path.resolve(
          `${DB_DATABASE}${DB_DATABASE?.includes(".sqlite") ? "" : ".sqlite"}`,
        ),
      };
    }
    case "mssql": {
      return {
        type: DB_CONNECTION,
        host: DB_HOST,
        port: Number(DB_PORT || 1433),
        username: DB_USERNAME,
        password: DB_PASSWORD,
        database: DB_DATABASE,
      };
    }
    case "mongodb": {
      return {
        type: DB_CONNECTION,
        host: DB_HOST,
        port: Number(DB_PORT || 27017),
        username: DB_USERNAME,
        password: DB_PASSWORD,
        database: DB_DATABASE,
      };
    }
    default:
      throw new Error(
        `Unsupported DB_CONNECTION: ${DB_CONNECTION}, available connections: mariadb, mysql, postgres, cockroachdb, sqlite, mssql, mongodb`,
      );
  }
};

const AppDataSource = new DataSource({
  ...getDatabaseConfig(),
  synchronize: false,
  logging: false,
  entities: entitiesData,
  migrations: migrationsData,
  migrationsRun: true,
  subscribers: [],
});

export default AppDataSource;
