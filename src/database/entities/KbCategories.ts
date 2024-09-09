import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("kb_categories")
export class KbCategories {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("varchar", { name: "name", length: 255 })
    name: string;

    @Column("int", { name: "order", default: () => "1000" })
    order: number;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
