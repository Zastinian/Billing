import AppDataSource from "./data-source";

await AppDataSource.initialize().catch((e) => {
  console.error("Unable to connect to the database, please check your configuration.");
  console.error("Try using `npm run db:migrate` to create the database tables.");

  console.error(`Error: ${e}`);
  process.exit(1);
});

import { Announcements } from "./entities/Announcements";
import { Categories } from "./entities/Categories";
import { Clients } from "./entities/Clients";
import { Coupons } from "./entities/Coupons";
import { Credits } from "./entities/Credits";
import { Currencies } from "./entities/Currencies";
import { Discounts } from "./entities/Discounts";
import { Emails } from "./entities/Emails";
import { FailedJobs } from "./entities/FailedJobs";
import { Invoices } from "./entities/Invoices";
import { Jobs } from "./entities/Jobs";
import { KbArticles } from "./entities/KbArticles";
import { KbCategories } from "./entities/KbCategories";
import { Logs } from "./entities/Logs";
import { Pages } from "./entities/Pages";
import { PasswordResets } from "./entities/PasswordResets";
import { PlanCycles } from "./entities/PlanCycles";
import { Plans } from "./entities/Plans";
import { Servers } from "./entities/Servers";
import { Settings } from "./entities/Settings";
import { Taxes } from "./entities/Taxes";
import { TicketContents } from "./entities/TicketContents";
import { Tickets } from "./entities/Tickets";
import { UsedCoupons } from "./entities/UsedCoupons";

// Entities

export const announcements = AppDataSource.getRepository(Announcements);
export const categories = AppDataSource.getRepository(Categories);
export const clients = AppDataSource.getRepository(Clients);
export const coupons = AppDataSource.getRepository(Coupons);
export const credits = AppDataSource.getRepository(Credits);
export const currencies = AppDataSource.getRepository(Currencies);
export const discounts = AppDataSource.getRepository(Discounts);
export const emails = AppDataSource.getRepository(Emails);
export const failedJobs = AppDataSource.getRepository(FailedJobs);
export const invoices = AppDataSource.getRepository(Invoices);
export const jobs = AppDataSource.getRepository(Jobs);
export const kbArticles = AppDataSource.getRepository(KbArticles);
export const kbCategories = AppDataSource.getRepository(KbCategories);
export const logs = AppDataSource.getRepository(Logs);
export const pages = AppDataSource.getRepository(Pages);
export const passwordResets = AppDataSource.getRepository(PasswordResets);
export const planCycles = AppDataSource.getRepository(PlanCycles);
export const plans = AppDataSource.getRepository(Plans);
export const servers = AppDataSource.getRepository(Servers);
export const settings = AppDataSource.getRepository(Settings);
export const taxes = AppDataSource.getRepository(Taxes);
export const ticketContents = AppDataSource.getRepository(TicketContents);
export const tickets = AppDataSource.getRepository(Tickets);
export const usedCoupons = AppDataSource.getRepository(UsedCoupons);
