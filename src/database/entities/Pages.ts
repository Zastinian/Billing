import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("pages")
export class Pages {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255, unique: true })
  name: string;

  @Column("text", { name: "content", nullable: true })
  content: string | null;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
