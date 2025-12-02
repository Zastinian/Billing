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

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date;
}
