import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Pages1725918990765 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "pages",
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
            isUnique: true,
            isNullable: false,
          },
          {
            name: "content",
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
    await queryRunner.manager.insert("pages", [
      {
        name: "home",
        content:
          "<h1>Welcome to your new store.</h1>\n<p>This is the home page. You may edit this page in the admin area.</p>",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        name: "status",
        content:
          "<h1>Welcome to your System Status page.</h1>\n<p>You may edit this page in the admin area.</p>",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        name: "terms",
        content:
          "<h1>Welcome to your Terms of Service page.</h1>\n<p>You may edit this page in the admin area.</p>",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        name: "privacy",
        content:
          "<h1>Welcome to your Privacy Policy page.</h1>\n<p>You may edit this page in the admin area.</p>",
        created_at: new Date(),
        updated_at: new Date(),
      },
    ]);
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.dropTable("pages");
  }
}
