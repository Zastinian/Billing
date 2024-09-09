import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Servers1725919156381 implements MigrationInterface {
    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.createTable(
            new Table({
                name: "servers",
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
                        name: "server_id",
                        type: "int",
                        unsigned: true,
                        isNullable: true,
                        isUnique: true,
                    },
                    {
                        name: "identifier",
                        type: "varchar",
                        length: "255",
                        isNullable: true,
                        isUnique: true,
                    },
                    {
                        name: "client_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "plan_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "plan_cycle",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "due_date",
                        type: "timestamp",
                        isNullable: true,
                    },
                    {
                        name: "payment_method",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "server_name",
                        type: "varchar",
                        length: "255",
                        isNullable: false,
                    },
                    {
                        name: "nest_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "egg_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "location_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "node_id",
                        type: "int",
                        unsigned: true,
                        isNullable: false,
                    },
                    {
                        name: "ip_address",
                        type: "varchar",
                        length: "255",
                        isNullable: true,
                    },
                    {
                        name: "status",
                        type: "tinyint",
                        unsigned: true,
                        default: 1,
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
                    {
                        name: "last_notif",
                        type: "timestamp",
                        isNullable: true,
                    },
                ],
            }),
        );
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.dropTable("servers");
    }
}
