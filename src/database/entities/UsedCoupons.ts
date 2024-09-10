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

  @Column("datetime", { name: "created_at", nullable: true })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", nullable: true })
  updatedAt: Date | null;
}
