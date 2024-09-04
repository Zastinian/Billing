import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Discounts1725488428300 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "discounts",
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
                        name: "name",
                        type: "varchar",
                        length: "255",
                    },
                    {
                        name: "percent_off",
                        type: "int",
                        unsigned: true,
                    },
                    {
                        name: "is_global",
                        type: "tinyint",
                        unsigned: true,
                        default: 0,
                    },
                    {
                        name: "end_date",
                        type: "timestamp",
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
        await queryRunner.dropTable("discounts");
    }
}
