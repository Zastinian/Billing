import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";
import { invoiceStatus } from "@/utils/status";

@Entity("invoices")
export class Invoices {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "client_id" })
  clientId: number;

  @Column("int", { name: "server_id", nullable: true })
  serverId: number | null;

  @Column("decimal", { name: "total", precision: 16, scale: 6 })
  total: number;

  @Column("text", { name: "payment_link", nullable: true })
  paymentLink: string | null;

  @Column("text", { name: "payment_method", nullable: true })
  paymentMethod: string | null;

  @Column("datetime", { name: "due_date", nullable: true })
  dueDate: Date | null;

  @Column("tinyint", { name: "paid", default: invoiceStatus.pending })
  paid: number;

  @Column("decimal", { name: "credit", precision: 16, scale: 6, nullable: true })
  credit: number | null;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
