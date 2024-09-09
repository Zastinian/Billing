import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class FailedJobs1725918466111 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "failed_jobs",
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
                        name: "uuid",
                        type: "varchar",
                        length: "255",
                        isUnique: true,
                        isNullable: false,
                    },
                    {
                        name: "connection",
                        type: "text",
                        isNullable: false,
                    },
                    {
                        name: "queue",
                        type: "text",
                        isNullable: false,
                    },
                    {
                        name: "payload",
                        type: "longtext",
                        isNullable: false,
                    },
                    {
                        name: "exception",
                        type: "longtext",
                        isNullable: false,
                    },
                    {
                        name: "failed_at",
                        type: "timestamp",
                        default: "CURRENT_TIMESTAMP",
                    },
                ],
            }),
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("failed_jobs");
    }
}
