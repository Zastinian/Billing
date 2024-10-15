import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Clients1725918291066 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "clients",
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
            name: "email",
            type: "varchar",
            length: "255",
            isUnique: true,
            isNullable: false,
          },
          {
            name: "email_verified_at",
            type: "datetime",
            isNullable: true,
          },
          {
            name: "user_id",
            type: "int",
            unsigned: true,
            isNullable: true,
            isUnique: true,
          },
          {
            name: "password",
            type: "varchar",
            length: "255",
            isUnique: true,
            isNullable: false,
          },
          {
            name: "credit",
            type: "decimal",
            precision: 16,
            scale: 6,
            unsigned: true,
            default: 0.0,
          },
          {
            name: "session_token",
            type: "varchar",
            length: "24",
            isNullable: true,
          },
          {
            name: "currency",
            type: "tinyint",
            default: 1,
          },
          {
            name: "auto_renew",
            type: "tinyint",
            default: 1,
          },
          {
            name: "is_active",
            type: "tinyint",
            default: 1,
          },
          {
            name: "is_admin",
            type: "tinyint",
            default: 0,
          },
          {
            name: "is_verified",
            type: "tinyint",
            default: 0,
          },
          {
            name: "created_at",
            type: "datetime",
          },
          {
            name: "updated_at",
            type: "datetime",
          },
        ],
      }),
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.dropTable("clients");
  }
}
