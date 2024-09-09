import { DataSource } from "typeorm";

if (!process.env.APP_KEY) {
    throw new Error("APP_KEY is not set, use `bun run --bun key:generate` to generate a new key");
}

import entities from "./entities";

const getDatabaseConfig = () => {
    switch (process.env.DB_CONNECTION) {
        case "mariadb":
        case "mysql": {
            return {
                type: process.env.DB_CONNECTION,
                host: process.env.DB_HOST,
                port: Number(process.env.DB_PORT || 3306),
                username: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_DATABASE,
            };
        }
        case "postgres": {
            return {
                type: process.env.DB_CONNECTION,
                host: process.env.DB_HOST,
                port: Number(process.env.DB_PORT || 5432),
                username: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_DATABASE,
            };
        }
        case "cockroachdb": {
            return {
                type: process.env.DB_CONNECTION,
                host: process.env.DB_HOST,
                port: Number(process.env.DB_PORT || 26257),
                username: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_DATABASE,
                timeTravelQueries: true,
            };
        }
        case "sqlite": {
            return {
                type: process.env.DB_CONNECTION,
                database: `${`${process.env.DB_DATABASE}`.includes(".sqlite") ? process.env.DB_DATABASE : `${process.env.DB_DATABASE}.sqlite`}`,
            };
        }
        case "mssql": {
            return {
                type: process.env.DB_CONNECTION,
                host: process.env.DB_HOST,
                port: Number(process.env.DB_PORT || 1433),
                username: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_DATABASE,
            };
        }
        case "mongodb": {
            return {
                type: process.env.DB_CONNECTION,
                host: process.env.DB_HOST,
                port: Number(process.env.DB_PORT || 27017),
                username: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_DATABASE,
            };
        }
        default:
            throw new Error(
                `Unsupported DB_CONNECTION: ${process.env.DB_CONNECTION}, available connections: mariadb, mysql, postgres, cockroachdb, sqlite, mssql, mongodb`,
            );
    }
};

const AppDataSource = new DataSource({
    ...getDatabaseConfig(),
    synchronize: false,
    logging: false,
    entities: entities,
    migrations: ["src/database/migrations/*.ts"],
    subscribers: [],
});

await AppDataSource.initialize().catch(() => {
    console.error("Unable to connect to the database, please check your configuration.");
    console.error("Try using `bun run --bun db:migrate` to create the database tables.");
    process.exit(1);
});

export default AppDataSource;
