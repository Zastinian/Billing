import { Table, type MigrationInterface, type QueryRunner } from "typeorm";

export class Affiliate1723866226042 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "affiliate_earnings",
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
                        name: "buyer_id",
                        type: "int",
                        unsigned: true,
                    },
                    {
                        name: "product",
                        type: "varchar",
                        length: "255",
                    },
                    {
                        name: "commission",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                    },
                    {
                        name: "conversion",
                        type: "decimal",
                        precision: 6,
                        scale: 3,
                        unsigned: true,
                    },
                    {
                        name: "status",
                        type: "tinyint",
                        unsigned: true,
                        default: 1,
                    },
                ],
            }),
        );
        await queryRunner.createTable(
            new Table({
                name: "affiliate_programs",
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
                        name: "key",
                        type: "varchar",
                        length: "255",
                    },
                    {
                        name: "value",
                        type: "varchar",
                        length: "255",
                    },
                ],
            }),
        );
        await queryRunner.query(
            `INSERT INTO "affiliate_programs" ("key", "value") VALUES ("enabled", "true")`,
        );
        await queryRunner.query(
            `INSERT INTO "affiliate_programs" ("key", "value") VALUES ("conversion", "50")`,
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("affiliate_earnings");
        await queryRunner.dropTable("affiliate_programs");
    }
}
