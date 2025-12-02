import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Jobs1725918762227 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "jobs",
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
            name: "queue",
            type: "varchar",
            length: "255",
            isNullable: false,
          },
          {
            name: "payload",
            type: "text",
            isNullable: false,
          },
          {
            name: "created_at",
            type: "datetime",
          },
        ],
        indices: [
          {
            name: "jobs_queue_index",
            columnNames: ["queue"],
          },
        ],
      }),
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.dropTable("jobs");
  }
}
