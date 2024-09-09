import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class PasswordResets1725919004832 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "password_resets",
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
                        name: "email",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "token",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "created_at",
                        type: "datetime",
                        isNullable: true,
                    },
                    {
                        name: "expires_at",
                        type: "datetime",
                        isNullable: true,
                    },
                ],
                indices: [
                    {
                        name: "password_resets_email_index",
                        columnNames: ["email"],
                    },
                ],
            }),
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("password_resets");
    }
}
