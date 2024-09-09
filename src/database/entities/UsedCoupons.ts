import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("used_coupons")
export class UsedCoupons {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "coupon_id" })
  couponId: number;

  @Column("int", { name: "client_id" })
  clientId: number;

  @Column("int", { name: "server_id" })
  serverId: number;

  @Column("timestamp", { name: "created_at", nullable: true })
  createdAt: NonNullable<unknown> | null;

  @Column("timestamp", { name: "updated_at", nullable: true })
  updatedAt: NonNullable<unknown> | null;
}
