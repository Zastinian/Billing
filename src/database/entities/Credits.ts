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

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
