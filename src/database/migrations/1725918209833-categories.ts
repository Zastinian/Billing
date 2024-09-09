import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Categories1725918209833 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "categories",
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
                        name: "description",
                        type: "text",
                        isNullable: true,
                    },
                    {
                        name: "global_limit",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "per_client_limit",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "per_client_trial_limit",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "order",
                        type: "int",
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
        await queryRunner.dropTable("categories");
    }
}
