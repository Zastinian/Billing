import { createHmac } from "crypto";
import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("clients")
export class Clients {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("varchar", { name: "email", length: 255, unique: true })
    email: string;

    @Column("datetime", { name: "email_verified_at", nullable: true })
    emailVerifiedAt: Date | null;

    @Column("varchar", { name: "recent_ip", nullable: true, length: 255 })
    recentIp: string | null;

    @Column("int", { name: "user_id", nullable: true, unique: true })
    userId: number | null;

    @Column("varchar", { name: "password", length: 255, unique: true })
    password: string;

    @Column("decimal", {
        name: "credit",
        precision: 16,
        scale: 6,
        default: () => "0",
    })
    credit: number;

    @Column("varchar", { name: "currency", length: 255, default: () => "0" })
    currency: string;

    @Column("varchar", {
        name: "country",
        length: 255,
        default: () => "'Global'",
    })
    country: string;

    @Column("varchar", { name: "timezone", length: 255, default: () => "'UTC'" })
    timezone: string;

    @Column("varchar", { name: "language", length: 255, default: () => "'EN'" })
    language: string;

    @Column("tinyint", { name: "auto_renew", default: () => "1" })
    autoRenew: number;

    @Column("tinyint", { name: "is_active", default: () => "1" })
    isActive: number;

    @Column("tinyint", { name: "is_admin", default: () => "0" })
    isAdmin: number;

    @Column("datetime", { name: "created_at", nullable: true })
    createdAt: Date | null;

    @Column("datetime", { name: "updated_at", nullable: true })
    updatedAt: Date | null;

    async setPassword(password: string): Promise<void> {
        this.password = createHmac("sha256", String(process.env.APP_KEY))
            .update(password)
            .digest("base64");
    }

    async verifyPassword(password: string): Promise<boolean> {
        return (
            createHmac("sha256", String(process.env.APP_KEY)).update(password).digest("base64") ===
            this.password
        );
    }
}
