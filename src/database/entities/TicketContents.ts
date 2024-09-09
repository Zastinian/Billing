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

  @Column("varchar", { name: "attachment", nullable: true, length: 255 })
  attachment: string | null;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
