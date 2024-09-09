import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Jobs1725918762227 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "jobs",
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
                        name: "queue",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "payload",
                        type: "longtext",
                        isNullable: false,
                    },
                    {
                        name: "attempts",
                        type: "tinyint",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "reserved_at",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "available_at",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "created_at",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                ],
                indices: [
                    {
                        name: "jobs_queue_index",
                        columnNames: ["queue"],
                    },
                ],
            }),
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("jobs");
    }
}
