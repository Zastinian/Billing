import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class PlanCycles1725919024889 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "plan_cycles",
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
            name: "plan_id",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "cycle_length",
            type: "int",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "cycle_type",
            type: "tinyint",
            unsigned: true,
            isNullable: false,
          },
          {
            name: "init_price",
            type: "decimal",
            precision: 16,
            scale: 6,
            unsigned: true,
            isNullable: false,
          },
          {
            name: "renew_price",
            type: "decimal",
            precision: 16,
            scale: 6,
            unsigned: true,
            isNullable: false,
          },
          {
            name: "setup_fee",
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
            name: "trial_length",
            type: "int",
            unsigned: true,
            isNullable: true,
          },
          {
            name: "trial_type",
            type: "tinyint",
            unsigned: true,
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
    await queryRunner.dropTable("plan_cycles");
  }
}
