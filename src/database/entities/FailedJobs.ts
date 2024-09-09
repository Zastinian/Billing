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

  @Column("longtext", { name: "payload" })
  payload: NonNullable<unknown>;

  @Column("longtext", { name: "exception" })
  exception: NonNullable<unknown>;

  @Column("timestamp", {
    name: "failed_at",
    default: () => "CURRENT_TIMESTAMP",
  })
  failedAt: NonNullable<unknown>;
}
