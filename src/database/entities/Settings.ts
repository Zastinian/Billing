import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("settings")
export class Settings {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("varchar", { name: "key", length: 255, unique: true })
    key: string;

    @Column("varchar", { name: "value", nullable: true, length: 255 })
    value: string | null;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
