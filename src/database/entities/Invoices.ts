import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("invoices")
export class Invoices {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("int", { name: "client_id" })
    clientId: number;

    @Column("int", { name: "server_id", nullable: true })
    serverId: number | null;

    @Column("decimal", { name: "total", precision: 16, scale: 6 })
    total: number;

    @Column("decimal", {
        name: "credit",
        precision: 16,
        scale: 6,
        default: () => "0",
    })
    credit: number;

    @Column("decimal", {
        name: "late_fee",
        precision: 16,
        scale: 6,
        default: () => "0",
    })
    lateFee: number;

    @Column("varchar", { name: "payment_method", nullable: true, length: 255 })
    paymentMethod: string | null;

    @Column("text", { name: "payment_link", nullable: true })
    paymentLink: string | null;

    @Column("timestamp", { name: "due_date", nullable: true })
    dueDate: NonNullable<unknown> | null;

    @Column("tinyint", { name: "paid", default: () => "0" })
    paid: number;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
