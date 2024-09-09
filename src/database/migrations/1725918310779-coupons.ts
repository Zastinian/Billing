import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Coupons1725918310779 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "coupons",
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
                        name: "code",
                        type: "varchar",
                        length: "255",
                        isUnique: true,
                        isNullable: false,
                    },
                    {
                        name: "percent_off",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "one_time",
                        type: "tinyint",
                        default: 0,
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
                        name: "is_global",
                        type: "tinyint",
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
        await queryRunner.dropTable("coupons");
    }
}
