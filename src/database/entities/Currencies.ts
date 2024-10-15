import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("currencies")
export class Currencies {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "name", length: 255, unique: true })
  name: string;

  @Column("varchar", { name: "symbol", length: 255 })
  symbol: string;

  @Column("decimal", { name: "rate", precision: 16, scale: 6 })
  rate: number;

  @Column("int", { name: "precision" })
  precision: number;

  @Column("int", { name: "tax", default: 1 })
  tax: number;

  @Column("tinyint", { name: "default", default: 0 })
  default: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;
}
