import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("kb_articles")
export class KbArticles {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("int", { name: "category_id" })
    categoryId: number;

    @Column("varchar", { name: "subject", length: 255 })
    subject: string;

    @Column("text", { name: "content" })
    content: string;

    @Column("int", { name: "order", default: () => "1000" })
    order: number;

    @Column("datetime", { name: "created_at", nullable: true })
    createdAt: Date | null;

    @Column("datetime", { name: "updated_at", nullable: true })
    updatedAt: Date | null;
}
