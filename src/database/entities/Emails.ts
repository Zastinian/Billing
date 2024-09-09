import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("emails")
export class Emails {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("int", { name: "client_id" })
    clientId: number;

    @Column("int", { name: "server_id", nullable: true })
    serverId: number | null;

    @Column("int", { name: "invoice_id", nullable: true })
    invoiceId: number | null;

    @Column("int", { name: "ticket_id", nullable: true })
    ticketId: number | null;

    @Column("int", { name: "affiliate_id", nullable: true })
    affiliateId: number | null;

    @Column("varchar", { name: "subject", length: 255 })
    subject: string;

    @Column("varchar", { name: "body_message", length: 255 })
    bodyMessage: string;

    @Column("varchar", { name: "body_action", length: 255 })
    bodyAction: string;

    @Column("varchar", { name: "button_text", length: 255 })
    buttonText: string;

    @Column("varchar", { name: "button_url", length: 255 })
    buttonUrl: string;

    @Column("varchar", { name: "notice", length: 255 })
    notice: string;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
