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

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
