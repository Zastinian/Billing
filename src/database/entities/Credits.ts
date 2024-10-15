import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("credits")
export class Credits {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("int", { name: "client_id" })
  clientId: number;

  @Column("varchar", { name: "details", length: 255 })
  details: string;

  @Column("decimal", { name: "change", precision: 8, scale: 2 })
  change: number;

  @Column("decimal", { name: "balance", precision: 8, scale: 2 })
  balance: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;
}
