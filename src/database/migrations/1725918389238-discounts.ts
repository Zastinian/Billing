import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Discounts1725918389238 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "discounts",
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
            name: "percent_off",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "is_global",
            type: "tinyint",
            isNullable: false,
          },
          {
            name: "end_date",
            type: "datetime",
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
    await queryRunner.dropTable("discounts");
  }
}
