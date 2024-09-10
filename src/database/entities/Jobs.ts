import { Column, Entity, Index, PrimaryGeneratedColumn } from "typeorm";

@Index("jobs_queue_index", ["queue"], {})
@Entity("jobs")
export class Jobs {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "queue", length: 255 })
  queue: string;

  @Column("text", { name: "payload" })
  payload: string;

  @Column("tinyint", { name: "attempts" })
  attempts: number;

  @Column("int", { name: "reserved_at", nullable: true })
  reservedAt: number | null;

  @Column("int", { name: "available_at" })
  availableAt: number;

  @Column("int", { name: "created_at" })
  createdAt: number;
}
