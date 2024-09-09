import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Currencies1725918367770 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "currencies",
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
                        isUnique: true,
                        isNullable: false,
                    },
                    {
                        name: "symbol",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "rate",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "precision",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "default",
                        type: "tinyint",
                        default: 0,
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
        await queryRunner.manager.insert("currencies", [
            {
                name: "USD",
                symbol: "&#36;",
                rate: 1.0,
                precision: 2,
                default: 1,
                created_at: new Date(),
                updated_at: new Date(),
            },
        ]);
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("currencies");
    }
}
