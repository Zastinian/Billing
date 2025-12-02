import { Column, Entity, Index, PrimaryGeneratedColumn } from "typeorm";

@Index("password_resets_email_index", ["email"], {})
@Entity("password_resets")
export class PasswordResets {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "email", length: 255 })
  email: string;

  @Column("varchar", { name: "token", length: 255 })
  token: string;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "expires_at", nullable: true })
  expiresAt: Date | null;
}
