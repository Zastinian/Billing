import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("discounts")
export class Discounts {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255 })
  name: string;

  @Column("int", { name: "percent_off" })
  percentOff: number;

  @Column("tinyint", { name: "is_global" })
  isGlobal: number;

  @Column("datetime", { name: "end_date", nullable: true })
  endDate: Date | null;

  @Column("datetime", { name: "created_at", nullable: true })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", nullable: true })
  updatedAt: Date | null;
}
