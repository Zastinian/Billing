import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("plan_cycles")
export class PlanCycles {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "plan_id" })
  planId: number;

  @Column("int", { name: "cycle_length" })
  cycleLength: number;

  @Column("tinyint", { name: "cycle_type" })
  cycleType: number;

  @Column("decimal", { name: "init_price", precision: 16, scale: 6 })
  initPrice: number;

  @Column("decimal", { name: "renew_price", precision: 16, scale: 6 })
  renewPrice: number;

  @Column("decimal", {
    name: "setup_fee",
    precision: 16,
    scale: 6,
    default: 0,
  })
  setupFee: number;

  @Column("decimal", {
    name: "late_fee",
    precision: 16,
    scale: 6,
    default: 0,
  })
  lateFee: number;

  @Column("int", { name: "trial_length", nullable: true })
  trialLength: number | null;

  @Column("tinyint", { name: "trial_type", nullable: true })
  trialType: number | null;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
