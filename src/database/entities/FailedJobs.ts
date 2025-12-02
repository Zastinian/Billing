import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("failed_jobs")
export class FailedJobs {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "uuid", length: 255, unique: true })
  uuid: string;

  @Column("text", { name: "connection" })
  connection: string;

  @Column("text", { name: "queue" })
  queue: string;

  @Column("text", { name: "payload" })
  payload: string;

  @Column("text", { name: "exception" })
  exception: string;

  @Column("datetime", {
    name: "failed_at",
    default: () => "CURRENT_TIMESTAMP",
  })
  failedAt: Date;
}
