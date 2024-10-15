import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("plans")
export class Plans {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255 })
  name: string;

  @Column("text", { name: "description", nullable: true })
  description: string | null;

  @Column("int", { name: "category_id" })
  categoryId: number;

  @Column("int", { name: "ram" })
  ram: number;

  @Column("int", { name: "cpu" })
  cpu: number;

  @Column("int", { name: "disk" })
  disk: number;

  @Column("int", { name: "swap" })
  swap: number;

  @Column("int", { name: "io" })
  io: number;

  @Column("int", { name: "databases" })
  databases: number;

  @Column("int", { name: "backups" })
  backups: number;

  @Column("int", { name: "extra_ports" })
  extraPorts: number;

  @Column("varchar", { name: "locations_nodes_id", length: 255 })
  locationsNodesId: string;

  @Column("int", { name: "min_port", nullable: true })
  minPort: number | null;

  @Column("int", { name: "max_port", nullable: true })
  maxPort: number | null;

  @Column("varchar", { name: "nests_eggs_id", length: 255 })
  nestsEggsId: string;

  @Column("text", { name: "server_description", nullable: true })
  serverDescription: string | null;

  @Column("int", { name: "discount", nullable: true })
  discount: number | null;

  @Column("varchar", { name: "coupons", nullable: true, length: 255 })
  coupons: string | null;

  @Column("int", { name: "days_before_suspend", nullable: true })
  daysBeforeSuspend: number | null;

  @Column("int", { name: "days_before_delete", nullable: true })
  daysBeforeDelete: number | null;

  @Column("int", { name: "global_limit", nullable: true })
  globalLimit: number | null;

  @Column("int", { name: "per_client_limit", nullable: true })
  perClientLimit: number | null;

  @Column("int", { name: "per_client_trial_limit", nullable: true })
  perClientTrialLimit: number | null;

  @Column("int", { name: "order", default: 1000 })
  order: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
