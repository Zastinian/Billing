import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("coupons")
export class Coupons {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "code", length: 255, unique: true })
  code: string;

  @Column("decimal", { name: "percent_off", precision: 8, scale: 2 })
  percentOff: number;

  @Column("tinyint", { name: "one_time", default: () => "0" })
  oneTime: number;

  @Column("int", { name: "global_limit", nullable: true })
  globalLimit: number | null;

  @Column("int", { name: "per_client_limit", nullable: true })
  perClientLimit: number | null;

  @Column("tinyint", { name: "is_global", default: () => "0" })
  isGlobal: number;

  @Column("timestamp", { name: "end_date", nullable: true })
  endDate: NonNullable<unknown> | null;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
