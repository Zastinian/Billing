import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Plans1725919112520 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "plans",
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
                        name: "category_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "ram",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "cpu",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "disk",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "swap",
                        type: "int",
                        isNullable: false,
                    },
                    {
                        name: "io",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "databases",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "backups",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "extra_ports",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "locations_nodes_id",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "min_port",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "max_port",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "nests_eggs_id",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "server_description",
                        type: "text",
                        isNullable: true,
                    },
                    {
                        name: "discount",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "coupons",
                        type: "varchar",
                        length: "255",
                        isNullable: true,
                    },
                    {
                        name: "days_before_suspend",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                    },
                    {
                        name: "days_before_delete",
                        type: "int",
                        unsigned: true,
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
        await queryRunner.dropTable("plans");
    }
}
