import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Credits1725487977943 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "credits",
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
                        name: "client_id",
                        type: "int",
                        unsigned: true,
                    },
                    {
                        name: "details",
                        type: "varchar",
                        length: "255",
                    },
                    {
                        name: "change",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        unsigned: true,
                    },
                    {
                        name: "balance",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        unsigned: true,
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
        await queryRunner.dropTable("credits");
    }
}
