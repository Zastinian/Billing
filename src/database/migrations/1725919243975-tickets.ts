import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Tickets1725919243975 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "tickets",
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
            name: "subject",
            type: "varchar",
            length: "255",
            isNullable: false,
          },
          {
            name: "server_id",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "department_id",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "category_id",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "status",
            type: "tinyint",
            default: 1,
          },
          {
            name: "is_locked",
            type: "tinyint",
            default: 0,
          },
          {
            name: "priority",
            type: "tinyint",
            isNullable: true,
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
    await queryRunner.dropTable("tickets");
  }
}
