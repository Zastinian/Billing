import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";
import { serverStatus } from "@/utils/status";
@Entity("servers")
export class Servers {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "server_id", nullable: true, unique: true })
  serverId: number | null;

  @Column("varchar", {
    name: "identifier",
    nullable: true,
    length: 255,
    unique: true,
  })
  identifier: string | null;

  @Column("int", { name: "client_id" })
  clientId: number;

  @Column("int", { name: "plan_id" })
  planId: number;

  @Column("int", { name: "plan_cycle" })
  planCycle: number;

  @Column("datetime", { name: "due_date", nullable: true })
  dueDate: Date | null;

  @Column("varchar", { name: "server_name", length: 255 })
  serverName: string;

  @Column("tinyint", { name: "status", default: serverStatus.pending })
  status: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;

  @Column("datetime", { name: "last_notif", nullable: true, default: null })
  lastNotif: Date | null;
}
