import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("settings")
export class Settings {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "key", length: 255, unique: true })
  key: string;

  @Column("varchar", { name: "value", nullable: true, length: 255 })
  value: string | null;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
