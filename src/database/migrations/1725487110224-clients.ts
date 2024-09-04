import { Table, type MigrationInterface, type QueryRunner } from "typeorm";

export class Clients1725487110224 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "clients",
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
                        isUnique: true,
                    },
                    {
                        name: "verified_at",
                        type: "timestamp",
                        isNullable: true,
                    },
                    {
                        name: "password",
                        type: "varchar",
                        length: "255",
                    },
                    {
                        name: "credits",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                        default: 0.0,
                    },
                    {
                        name: "currency",
                        type: "varchar",
                        length: "255",
                        default: "0",
                    },
                    {
                        name: "is_active",
                        type: "tinyint",
                        unsigned: true,
                        default: 1,
                    },
                    {
                        name: "is_admin",
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
        await queryRunner.dropTable("clients");
    }
}
