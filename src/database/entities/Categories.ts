import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("categories")
export class Categories {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255 })
  name: string;

  @Column("text", { name: "description", nullable: true })
  description: string | null;

  @Column("int", { name: "global_limit", nullable: true })
  globalLimit: number | null;

  @Column("int", { name: "per_client_limit", nullable: true })
  perClientLimit: number | null;

  @Column("int", { name: "per_client_trial_limit", nullable: true })
  perClientTrialLimit: number | null;

  @Column("int", { name: "order", default: () => "1000" })
  order: number;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
