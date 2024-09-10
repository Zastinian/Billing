import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Logs1725918955565 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "logs",
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
            name: "event_id",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "client_id",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "details",
            type: "text",
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
    await queryRunner.dropTable("logs");
  }
}
