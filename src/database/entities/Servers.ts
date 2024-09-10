import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

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

  @Column("varchar", { name: "payment_method", length: 255 })
  paymentMethod: string;

  @Column("varchar", { name: "server_name", length: 255 })
  serverName: string;

  @Column("int", { name: "nest_id" })
  nestId: number;

  @Column("int", { name: "egg_id" })
  eggId: number;

  @Column("int", { name: "location_id" })
  locationId: number;

  @Column("int", { name: "node_id" })
  nodeId: number;

  @Column("varchar", { name: "ip_address", nullable: true, length: 255 })
  ipAddress: string | null;

  @Column("tinyint", { name: "status", default: () => "1" })
  status: number;

  @Column("datetime", { name: "created_at", nullable: true })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", nullable: true })
  updatedAt: Date | null;

  @Column("datetime", { name: "last_notif", nullable: true })
  lastNotif: Date | null;
}
