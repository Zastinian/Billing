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

    @Column("tinyint", { name: "default", default: () => "0" })
    default: number;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
