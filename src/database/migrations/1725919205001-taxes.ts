import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Taxes1725919205001 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "taxes",
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
                        name: "country",
                        type: "varchar",
                        length: "255",
                        isUnique: true,
                        isNullable: false,
                    },
                    {
                        name: "percent",
                        type: "decimal",
                        precision: 5,
                        scale: 3,
                        unsigned: true,
                        default: 0.0,
                    },
                    {
                        name: "amount",
                        type: "decimal",
                        precision: 16,
                        scale: 6,
                        unsigned: true,
                        default: 0.0,
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
        await queryRunner.manager.insert("taxes", [
            {
                country: "Global",
                percent: 0.0,
                amount: 0.0,
                created_at: new Date(),
                updated_at: new Date(),
            },
        ]);
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("taxes");
    }
}
