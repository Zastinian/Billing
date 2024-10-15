import { type MigrationInterface, type QueryRunner, Table } from "typeorm";

export class Settings1725919183938 implements MigrationInterface {
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.createTable(
      new Table({
        name: "settings",
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
            name: "key",
            type: "varchar",
            length: "255",
            isUnique: true,
            isNullable: false,
          },
          {
            name: "value",
            type: "varchar",
            length: "255",
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
    await queryRunner.manager.insert("settings", [
      {
        key: "company_name",
        value: import.meta.env.COMPANY_NAME,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "logo_path",
        value: "/favicon.webp",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "favicon_path",
        value: "/favicon.webp",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "open_registration",
        value: "true",
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "panel_url",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "panel_client_api_key",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "panel_app_api_key",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "discord_url",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "phpmyadmin_url",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "hcaptcha_site_key",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "hcaptcha_secret_key",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        key: "google_analytics_id",
        value: null,
        created_at: new Date(),
        updated_at: new Date(),
      },
    ]);
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.dropTable("settings");
  }
}
