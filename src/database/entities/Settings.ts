import { createCipheriv, createDecipheriv, randomBytes } from "crypto";
import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";
import config from "@/config/index";

const { APP_KEY } = config;

@Entity("settings")
export class Settings {
  @PrimaryGeneratedColumn({ type: "integer", name: "id" })
  id: number;

  @Column("varchar", { name: "key", length: 255, unique: true })
  key: string;

  @Column("varchar", { name: "value", nullable: true, length: 255 })
  value: string | null;

  @Column("datetime", { name: "created_at", default: () => "CURRENT_TIMESTAMP" })
  createdAt: Date | null;

  @Column("datetime", { name: "updated_at", default: () => "CURRENT_TIMESTAMP" })
  updatedAt: Date | null;

  async setValue(value: string | null): Promise<void> {
    this.value = value ? this.encrypt(value) : null;
  }

  async getValue(): Promise<string | null> {
    if (!this.value) {
      return null;
    }
    return this.decrypt(this.value);
  }

  private encrypt(text: string): string {
    const iv = randomBytes(16);
    const rawKey = APP_KEY.startsWith("base64:") ? APP_KEY.slice(7) : APP_KEY;
    const key = Buffer.from(rawKey, "base64");
    const cipher = createCipheriv("aes-256-cbc", key, iv);
    let encrypted = cipher.update(text, "utf8", "base64");
    encrypted += cipher.final("base64");
    return `${iv.toString("base64")}:${encrypted}`;
  }

  private decrypt(text: string): string {
    const [ivPart, encryptedPart] = text.split(":");
    if (!ivPart || !encryptedPart) {
      throw new Error("Invalid encrypted value");
    }
    const iv = Buffer.from(ivPart, "base64");
    const rawKey = APP_KEY.startsWith("base64:") ? APP_KEY.slice(7) : APP_KEY;
    const key = Buffer.from(rawKey, "base64");
    const decipher = createDecipheriv("aes-256-cbc", key, iv);
    let decrypted = decipher.update(encryptedPart, "base64", "utf8");
    decrypted += decipher.final("utf8");
    return decrypted;
  }
}
