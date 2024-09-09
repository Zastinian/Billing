import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("taxes")
export class Taxes {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("varchar", { name: "country", length: 255, unique: true })
    country: string;

    @Column("decimal", {
        name: "percent",
        precision: 5,
        scale: 3,
        default: () => "0",
    })
    percent: number;

    @Column("decimal", {
        name: "amount",
        precision: 16,
        scale: 6,
        default: () => "0",
    })
    amount: number;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
