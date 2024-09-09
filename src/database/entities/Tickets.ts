import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("tickets")
export class Tickets {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("int", { name: "client_id" })
    clientId: number;

    @Column("varchar", { name: "subject", length: 255 })
    subject: string;

    @Column("int", { name: "server_id", nullable: true })
    serverId: number | null;

    @Column("int", { name: "department_id", nullable: true })
    departmentId: number | null;

    @Column("int", { name: "category_id", nullable: true })
    categoryId: number | null;

    @Column("tinyint", { name: "status", default: () => "1" })
    status: number;

    @Column("tinyint", { name: "is_locked", default: () => "0" })
    isLocked: number;

    @Column("tinyint", { name: "priority", nullable: true })
    priority: number | null;

    @Column("datetime", { name: "created_at", nullable: true })
    createdAt: Date | null;

    @Column("datetime", { name: "updated_at", nullable: true })
    updatedAt: Date | null;
}
