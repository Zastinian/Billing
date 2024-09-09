import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("announcements")
export class Announcements {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("tinyint", { name: "enabled" })
  enabled: number;

  @Column("varchar", { name: "subject", length: 255 })
  subject: string;

  @Column("text", { name: "content" })
  content: string;

  @Column("tinyint", { name: "theme" })
  theme: number;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}