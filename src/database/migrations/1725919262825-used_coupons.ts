import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class UsedCoupons1725919262825 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "used_coupons",
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
            name: "coupon_id",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "client_id",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "server_id",
            type: "int",
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
    await queryRunner.dropTable("used_coupons");
  }
}
