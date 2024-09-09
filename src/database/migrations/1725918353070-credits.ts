import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Credits1725918353070 implements MigrationInterface {
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
                        isNullable: false,
                    },
                    {
                        name: "details",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "change",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        isNullable: false,
                    },
                    {
                        name: "balance",
                        type: "decimal",
                        precision: 8,
                        scale: 2,
                        unsigned: true,
                        isNullable: false,
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
        await queryRunner.dropTable("credits");
    }
}
