import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity("discounts")
export class Discounts {
    @PrimaryGeneratedColumn({ type: "integer", name: "id" })
    id: number;

    @Column("varchar", { name: "name", length: 255 })
    name: string;

    @Column("int", { name: "percent_off" })
    percentOff: number;

    @Column("tinyint", { name: "is_global" })
    isGlobal: number;

    @Column("timestamp", { name: "end_date", nullable: true })
    endDate: NonNullable<unknown> | null;

    @Column("timestamp", { name: "created_at", nullable: true })
    createdAt: NonNullable<unknown> | null;

    @Column("timestamp", { name: "updated_at", nullable: true })
    updatedAt: NonNullable<unknown> | null;
}
