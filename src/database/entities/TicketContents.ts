import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("ticket_contents")
export class TicketContents {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "ticket_id" })
  ticketId: number;

  @Column("int", { name: "replier_id" })
  replierId: number;

  @Column("text", { name: "message" })
  message: string;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
