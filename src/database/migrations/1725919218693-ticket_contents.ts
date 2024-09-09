import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class TicketContents1725919218693 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "ticket_contents",
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
                        name: "ticket_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "replier_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "message",
                        type: "text",
                        isNullable: false,
                    },
                    {
                        name: "attachment",
                        type: "varchar",
                        length: "255",
                        isNullable: true,
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
        await queryRunner.dropTable("ticket_contents");
    }
}
