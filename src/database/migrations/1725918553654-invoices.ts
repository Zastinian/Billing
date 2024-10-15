import { type MigrationInterface, type QueryRunner, Table } from "typeorm";
import { invoiceStatus } from "@/utils/status";

export class Invoices1725918553654 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "invoices",
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
            name: "server_id",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "total",
            type: "decimal",
            precision: 16,
            scale: 6,
            unsigned: true,
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
            name: "late_fee",
            type: "decimal",
            precision: 16,
            scale: 6,
            unsigned: true,
            default: 0.0,
          },
          {
            name: "payment_method",
            type: "varchar",
            length: "255",
            isNullable: true,
          },
          {
            name: "payment_link",
            type: "text",
            isNullable: true,
          },
          {
            name: "due_date",
            type: "datetime",
            isNullable: true,
          },
          {
            name: "paid",
            type: "tinyint",
            default: invoiceStatus.pending,
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
    await queryRunner.dropTable("invoices");
  }
}
