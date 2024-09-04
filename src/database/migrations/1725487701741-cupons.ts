import { Table, type MigrationInterface, type QueryRunner } from "typeorm";

export class Cupons1725487701741 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "cupons",
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
                    },
                    {
                        name: "percent_off",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        unsigned: true,
                    },
                    {
                        name: "one_time",
                        type: "tinyint",
                        unsigned: true,
                        default: 0,
                    },
                    {
                        name: "global_limit",
                        type: "int",
                        unsigned: true,
                        default: null,
                    },
                    {
                        name: "per_client_limit",
                        type: "int",
                        unsigned: true,
                        default: null,
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
        await queryRunner.dropTable("cupons");
    }
}
