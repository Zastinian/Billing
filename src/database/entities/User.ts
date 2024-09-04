import { createHmac } from "crypto";
import { Entity, PrimaryGeneratedColumn, Column } from "typeorm";

@Entity()
export class User {
    @PrimaryGeneratedColumn("increment")
    id: number;

    @Column()
    email: string;

    @Column()
    verified_at: Date;

    @Column()
    password: string;

    @Column()
    credits: number;

    @Column()
    currency: string;

    @Column()
    is_active: boolean;

    @Column()
    is_admin: boolean;

    @Column()
    created_at: Date;

    @Column()
    updated_at: Date;

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
