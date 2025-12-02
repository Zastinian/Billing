import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Extensions1749916212496 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "extensions",
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
            name: "extension",
            type: "varchar",
            length: "255",
            isNullable: false,
          },
          {
            name: "key",
            type: "varchar",
            length: "255",
            isNullable: false,
          },
          {
            name: "value",
            type: "text",
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
    await queryRunner.dropTable("extensions");
  }
}
