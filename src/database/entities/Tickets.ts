import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";
import { ticketStatus } from "@/utils/status";

@Entity("tickets")
export class Tickets {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "client_id" })
  clientId: number;

  @Column("varchar", { name: "subject", length: 255 })
  subject: string;

  @Column("tinyint", { name: "status", default: ticketStatus.pending })
  status: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
