import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("logs")
export class Logs {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "event_id" })
  eventId: number;

  @Column("int", { name: "client_id", nullable: true })
  clientId: number | null;

  @Column("text", { name: "details" })
  details: string;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
