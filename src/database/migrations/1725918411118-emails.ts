import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Emails1725918411118 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "emails",
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
                        isNullable: false,
                    },
                    {
                        name: "server_id",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "invoice_id",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "ticket_id",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "affiliate_id",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "subject",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "body_message",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "body_action",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "button_text",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "button_url",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "notice",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
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
        await queryRunner.dropTable("emails");
    }
}
