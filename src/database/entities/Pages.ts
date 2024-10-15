import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("pages")
export class Pages {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255, unique: true })
  name: string;

  @Column("text", { name: "content", nullable: true })
  content: string | null;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
