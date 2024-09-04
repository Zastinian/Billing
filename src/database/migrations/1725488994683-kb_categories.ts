import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class KbCategories1725488994683 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "kb_categories",
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
                        name: "order",
                        type: "int",
                        unsigned: true,
                        default: 1000,
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
        await queryRunner.dropTable("kb_categories");
    }
}
