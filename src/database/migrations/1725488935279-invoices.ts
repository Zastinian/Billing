import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Invoices1725488935279 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "invoices",
                columns: [
                    {
                        name: "id",
                        type: "integer",
                        isPrimary: true,
                        isGenerated: true,
                        generationStrategy: "increment",
                        unsigned: true,
                    },
                    {
                        name: "client_id",
                        type: "int",
                        unsigned: true,
                    },
                    {
                        name: "server_id",
                        type: "int",
                        unsigned: true,
                        default: null,
                    },
                    {
                        name: "total",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                    },
                    {
                        name: "credit",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                        default: 0.0,
                    },
                    {
                        name: "payment_method",
                        type: "varchar",
                        length: "255",
                        default: null,
                    },
                    {
                        name: "payment_link",
                        type: "text",
                        default: null,
                    },
                    {
                        name: "due_date",
                        type: "timestamp",
                        isNullable: true,
                    },
                    {
                        name: "paid",
                        type: "tinyint",
                        unsigned: true,
                        default: 0,
                    },
                    {
                        name: "created_at",
                        type: "timestamp",
                        isNullable: true,
                    },
                    {
                        name: "updated_at",
                        type: "timestamp",
                        isNullable: true,
                    },
                ],
            }),
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("invoices");
    }
}
