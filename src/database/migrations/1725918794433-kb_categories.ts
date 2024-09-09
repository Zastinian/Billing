import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class KbCategories1725918794433 implements MigrationInterface {
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
                        isNullable: false,
                    },
                    {
                        name: "order",
                        type: "int",
                        default: 1000,
                    },
                    {
                        name: "created_at",
                        type: "datetime",
                        isNullable: true,
                    },
                    {
                        name: "updated_at",
                        type: "datetime",
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
