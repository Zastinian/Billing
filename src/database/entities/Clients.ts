import { createHmac, randomBytes } from "crypto";
import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("clients")
export class Clients {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "email", length: 255, unique: true })
  email: string;

  @Column("datetime", { name: "email_verified_at", nullable: true })
  emailVerifiedAt: Date | null;

  @Column("int", { name: "user_id", nullable: true, unique: true })
  userId: number | null;

  @Column("varchar", { name: "password", length: 255, unique: true })
  password: string;

  @Column("decimal", {
    name: "credit",
    precision: 16,
    scale: 6,
    default: 0,
  })
  credit: number;

  @Column("varchar", { name: "session_token", length: 24, nullable: true })
  sessionToken: string | null;

  @Column("tinyint", { name: "currency", default: 1 })
  currency: number;

  @Column("tinyint", { name: "auto_renew", default: 1 })
  autoRenew: number;

  @Column("tinyint", { name: "is_active", default: 1 })
  isActive: number;

  @Column("tinyint", { name: "is_admin", default: 0 })
  isAdmin: number;

  @Column("tinyint", { name: "is_verified", default: 0 })
  isVerified: number;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date;

  async setPassword(password: string): Promise<void> {
    this.password = createHmac("sha256", String(import.meta.env.APP_KEY))
      .update(password)
      .digest("base64");
  }

  async verifyPassword(password: string): Promise<boolean> {
    return (
      createHmac("sha256", String(import.meta.env.APP_KEY))
        .update(password)
        .digest("base64") === this.password
    );
  }

  setSessionToken(): void {
    this.sessionToken = randomBytes(24).toString("hex");
  }
}
