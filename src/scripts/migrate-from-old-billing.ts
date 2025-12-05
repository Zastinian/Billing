import { createInterface } from "readline";
import { DataSource } from "typeorm";
import AppDataSource from "../database/data-source";
import { Announcements } from "../database/entities/Announcements";
import { Categories } from "../database/entities/Categories";
import { Clients } from "../database/entities/Clients";
import { Coupons } from "../database/entities/Coupons";
import { Credits } from "../database/entities/Credits";
import { Currencies } from "../database/entities/Currencies";
import { Discounts } from "../database/entities/Discounts";
import { Invoices } from "../database/entities/Invoices";
import { KbArticles } from "../database/entities/KbArticles";
import { KbCategories } from "../database/entities/KbCategories";
import { PlanCycles } from "../database/entities/PlanCycles";
import { Plans } from "../database/entities/Plans";
import { Servers } from "../database/entities/Servers";
import { UsedCoupons } from "../database/entities/UsedCoupons";

const rl = createInterface({
  input: process.stdin,
  output: process.stdout,
});

const question = (query: string): Promise<string> => {
  return new Promise((resolve) => {
    rl.question(query, resolve);
  });
};

async function migrateFromOldBilling() {
  console.log("=== Migration from Old Billing System ===\n");
  console.log("Please enter the OLD billing database connection details:\n");

  const host = await question("Database host [localhost]: ");
  const port = await question("Database port [3306]: ");
  const database = await question("Database name: ");
  const username = await question("Database username: ");
  const password = await question("Database password: ");

  console.log("\nConnecting to old billing database...");

  const oldDataSource = new DataSource({
    type: "mysql",
    host: host || "localhost",
    port: Number.parseInt(port, 10) || 3306,
    username,
    password,
    database,
    synchronize: false,
    logging: false,
  });

  try {
    await oldDataSource.initialize();
    console.log("✓ Connected to old billing database\n");

    if (!AppDataSource.isInitialized) {
      await AppDataSource.initialize();
    }
    console.log("✓ Connected to new billing database\n");

    console.log("Starting migration process...\n");

    console.log("Migrating categories...");
    const oldCategories = await oldDataSource.query("SELECT * FROM categories");
    for (const oldCat of oldCategories) {
      const category = new Categories();
      category.id = oldCat.id;
      category.name = oldCat.name;
      category.description = oldCat.description;
      category.order = oldCat.order || 1000;
      category.createdAt = oldCat.created_at;
      category.updatedAt = oldCat.updated_at;
      await AppDataSource.manager.save(category);
    }
    console.log(`✓ Migrated ${oldCategories.length} categories\n`);

    console.log("Migrating currencies...");
    const oldCurrencies = await oldDataSource.query("SELECT * FROM currencies");
    for (const oldCur of oldCurrencies) {
      const currency = new Currencies();
      currency.id = oldCur.id;
      currency.name = oldCur.name;
      currency.symbol = oldCur.symbol;
      currency.rate = Number.parseFloat(oldCur.rate);
      currency.precision = oldCur.precision;
      currency.default = oldCur.default ? 1 : 0;
      currency.createdAt = oldCur.created_at;
      currency.updatedAt = oldCur.updated_at;
      await AppDataSource.manager.save(currency);
    }
    console.log(`✓ Migrated ${oldCurrencies.length} currencies\n`);

    console.log("Migrating clients...");
    const oldClients = await oldDataSource.query("SELECT * FROM clients");
    for (const oldClient of oldClients) {
      const client = new Clients();
      client.id = oldClient.id;
      client.email = oldClient.email;
      client.emailVerifiedAt = oldClient.email_verified_at;
      client.userId = oldClient.user_id;
      client.password = oldClient.password;
      client.credit = Number.parseFloat(oldClient.credit);

      const currencyValue = Number.parseInt(oldClient.currency, 10);
      client.currency = Number.isNaN(currencyValue) ? 1 : currencyValue;
      client.autoRenew = oldClient.auto_renew ? 1 : 0;
      client.isActive = oldClient.is_active ? 1 : 0;
      client.isAdmin = oldClient.is_admin ? 1 : 0;

      client.isVerified = 0;

      client.sessionToken = null;

      client.createdAt = oldClient.created_at;
      client.updatedAt = oldClient.updated_at;
      await AppDataSource.manager.save(client);
    }
    console.log(`✓ Migrated ${oldClients.length} clients\n`);

    console.log("Migrating plans...");
    const oldPlans = await oldDataSource.query("SELECT * FROM plans");
    for (const oldPlan of oldPlans) {
      const plan = new Plans();
      plan.id = oldPlan.id;
      plan.name = oldPlan.name;
      plan.description = oldPlan.description;
      plan.categoryId = oldPlan.category_id;
      plan.ram = oldPlan.ram;
      plan.cpu = oldPlan.cpu;
      plan.disk = oldPlan.disk;
      plan.swap = oldPlan.swap;
      plan.io = oldPlan.io;
      plan.databases = oldPlan.databases;
      plan.backups = oldPlan.backups;
      plan.extraPorts = oldPlan.extra_ports;
      plan.locationsNodesId = oldPlan.locations_nodes_id;
      plan.nestsEggsId = oldPlan.nests_eggs_id;
      plan.serverDescription = oldPlan.server_description;
      plan.discount = oldPlan.discount;
      plan.coupons = oldPlan.coupons;
      plan.daysBeforeSuspend = oldPlan.days_before_suspend;
      plan.daysBeforeDelete = oldPlan.days_before_delete;
      plan.globalLimit = oldPlan.global_limit;
      plan.perClientLimit = oldPlan.per_client_limit;
      plan.order = oldPlan.order || 1000;
      plan.createdAt = oldPlan.created_at;
      plan.updatedAt = oldPlan.updated_at;
      await AppDataSource.manager.save(plan);
    }
    console.log(`✓ Migrated ${oldPlans.length} plans\n`);

    console.log("Migrating plan cycles...");
    const oldPlanCycles = await oldDataSource.query("SELECT * FROM plan_cycles");
    for (const oldCycle of oldPlanCycles) {
      const cycle = new PlanCycles();
      cycle.id = oldCycle.id;
      cycle.planId = oldCycle.plan_id;
      cycle.cycleLength = oldCycle.cycle_length;
      cycle.cycleType = oldCycle.cycle_type;
      cycle.initPrice = Number.parseFloat(oldCycle.init_price);
      cycle.renewPrice = Number.parseFloat(oldCycle.renew_price);
      cycle.setupFee = Number.parseFloat(oldCycle.setup_fee || 0);
      cycle.createdAt = oldCycle.created_at;
      cycle.updatedAt = oldCycle.updated_at;

      await AppDataSource.manager.save(cycle);
    }
    console.log(`✓ Migrated ${oldPlanCycles.length} plan cycles\n`);

    console.log("Migrating servers...");
    const oldServers = await oldDataSource.query("SELECT * FROM servers");
    for (const oldServer of oldServers) {
      const server = new Servers();
      server.id = oldServer.id;
      server.serverId = oldServer.server_id;
      server.identifier = oldServer.identifier;
      server.clientId = oldServer.client_id;
      server.planId = oldServer.plan_id;
      server.planCycle = oldServer.plan_cycle;
      server.dueDate = oldServer.due_date;
      server.serverName = oldServer.server_name;
      server.status = oldServer.status || 1;
      server.createdAt = oldServer.created_at;
      server.updatedAt = oldServer.updated_at;
      server.lastNotif = oldServer.last_notif || null;

      await AppDataSource.manager.save(server);
    }
    console.log(`✓ Migrated ${oldServers.length} servers\n`);

    console.log("Migrating coupons...");
    try {
      const oldCoupons = await oldDataSource.query("SELECT * FROM coupons");
      for (const oldCoupon of oldCoupons) {
        const coupon = new Coupons();
        coupon.id = oldCoupon.id;
        coupon.code = oldCoupon.code;
        coupon.percentOff = Number.parseFloat(oldCoupon.percent_off);
        coupon.oneTime = oldCoupon.one_time ? 1 : 0;
        coupon.globalLimit = oldCoupon.global_limit;
        coupon.perClientLimit = oldCoupon.per_client_limit;
        coupon.isGlobal = oldCoupon.is_global ? 1 : 0;
        coupon.endDate = oldCoupon.end_date;
        coupon.createdAt = oldCoupon.created_at;
        coupon.updatedAt = oldCoupon.updated_at;
        await AppDataSource.manager.save(coupon);
      }
      console.log(`✓ Migrated ${oldCoupons.length} coupons\n`);
    } catch {
      console.log("⚠ Coupons table not found or empty, skipping...\n");
    }

    console.log("Migrating discounts...");
    try {
      const oldDiscounts = await oldDataSource.query("SELECT * FROM discounts");
      for (const oldDiscount of oldDiscounts) {
        const discount = new Discounts();
        discount.id = oldDiscount.id;
        discount.name = oldDiscount.name;
        discount.percentOff = oldDiscount.percent_off;
        discount.isGlobal = oldDiscount.is_global ? 1 : 0;
        discount.endDate = oldDiscount.end_date;
        discount.createdAt = oldDiscount.created_at;
        discount.updatedAt = oldDiscount.updated_at;
        await AppDataSource.manager.save(discount);
      }
      console.log(`✓ Migrated ${oldDiscounts.length} discounts\n`);
    } catch {
      console.log("⚠ Discounts table not found or empty, skipping...\n");
    }

    console.log("Migrating announcements...");
    try {
      const oldAnnouncements = await oldDataSource.query("SELECT * FROM announcements");
      for (const oldAnn of oldAnnouncements) {
        const announcement = new Announcements();
        announcement.id = oldAnn.id;

        announcement.subject = oldAnn.title || oldAnn.subject || "Announcement";
        announcement.content = oldAnn.content;

        announcement.enabled = 1;

        announcement.theme = 1;

        announcement.createdAt = oldAnn.created_at;
        announcement.updatedAt = oldAnn.updated_at;
        await AppDataSource.manager.save(announcement);
      }
      console.log(`✓ Migrated ${oldAnnouncements.length} announcements\n`);
    } catch {
      console.log("⚠ Announcements table not found or empty, skipping...\n");
    }

    console.log("Migrating KB categories...");
    try {
      const oldKbCats = await oldDataSource.query("SELECT * FROM kb_categories");
      for (const oldKbCat of oldKbCats) {
        const kbCat = new KbCategories();
        kbCat.id = oldKbCat.id;
        kbCat.name = oldKbCat.name;

        kbCat.order = oldKbCat.order || 1000;
        kbCat.createdAt = oldKbCat.created_at;
        kbCat.updatedAt = oldKbCat.updated_at;
        await AppDataSource.manager.save(kbCat);
      }
      console.log(`✓ Migrated ${oldKbCats.length} KB categories\n`);
    } catch {
      console.log("⚠ KB categories table not found or empty, skipping...\n");
    }

    console.log("Migrating KB articles...");
    try {
      const oldKbArticles = await oldDataSource.query("SELECT * FROM kb_articles");
      for (const oldArticle of oldKbArticles) {
        const article = new KbArticles();
        article.id = oldArticle.id;
        article.categoryId = oldArticle.category_id;

        article.subject = oldArticle.title || oldArticle.subject || "Article";
        article.content = oldArticle.content;
        article.order = oldArticle.order || 1000;
        article.createdAt = oldArticle.created_at;
        article.updatedAt = oldArticle.updated_at;
        await AppDataSource.manager.save(article);
      }
      console.log(`✓ Migrated ${oldKbArticles.length} KB articles\n`);
    } catch {
      console.log("⚠ KB articles table not found or empty, skipping...\n");
    }

    console.log("Migrating invoices...");
    try {
      const oldInvoices = await oldDataSource.query("SELECT * FROM invoices");
      for (const oldInvoice of oldInvoices) {
        const invoice = new Invoices();
        invoice.id = oldInvoice.id;
        invoice.clientId = oldInvoice.client_id;
        invoice.serverId = oldInvoice.server_id;
        invoice.total = Number.parseFloat(oldInvoice.total);
        invoice.paymentLink = oldInvoice.payment_link;
        invoice.paymentMethod = oldInvoice.payment_method;
        invoice.dueDate = oldInvoice.due_date;

        invoice.paid = oldInvoice.paid ? 1 : 0;
        invoice.credit = Number.parseFloat(oldInvoice.credit || 0);
        invoice.createdAt = oldInvoice.created_at;
        invoice.updatedAt = oldInvoice.updated_at;

        await AppDataSource.manager.save(invoice);
      }
      console.log(`✓ Migrated ${oldInvoices.length} invoices\n`);
    } catch {
      console.log("⚠ Invoices table not found or empty, skipping...\n");
    }

    console.log("Migrating credits...");
    try {
      const oldCredits = await oldDataSource.query("SELECT * FROM credits");
      for (const oldCredit of oldCredits) {
        const credit = new Credits();
        credit.id = oldCredit.id;
        credit.clientId = oldCredit.client_id;
        credit.details = oldCredit.details;
        credit.change = Number.parseFloat(oldCredit.change);
        credit.balance = Number.parseFloat(oldCredit.balance);
        credit.createdAt = oldCredit.created_at;

        await AppDataSource.manager.save(credit);
      }
      console.log(`✓ Migrated ${oldCredits.length} credits\n`);
    } catch {
      console.log("⚠ Credits table not found or empty, skipping...\n");
    }

    console.log("Migrating used coupons...");
    try {
      const oldUsedCoupons = await oldDataSource.query("SELECT * FROM used_coupons");
      for (const oldUsed of oldUsedCoupons) {
        const usedCoupon = new UsedCoupons();
        usedCoupon.id = oldUsed.id;
        usedCoupon.clientId = oldUsed.client_id;
        usedCoupon.couponId = oldUsed.coupon_id;
        usedCoupon.createdAt = oldUsed.created_at;
        usedCoupon.updatedAt = oldUsed.updated_at;
        await AppDataSource.manager.save(usedCoupon);
      }
      console.log(`✓ Migrated ${oldUsedCoupons.length} used coupons\n`);
    } catch {
      console.log("⚠ Used coupons table not found or empty, skipping...\n");
    }

    console.log("✓ Migration completed successfully!\n");

    console.log("=== Migration Summary ===");
    console.log(`Categories: ${oldCategories.length}`);
    console.log(`Currencies: ${oldCurrencies.length}`);
    console.log(`Clients: ${oldClients.length}`);
    console.log(`Plans: ${oldPlans.length}`);
    console.log(`Plan Cycles: ${oldPlanCycles.length}`);
    console.log(`Servers: ${oldServers.length}`);
  } catch (error) {
    console.error("❌ Migration failed:", error);
    throw error;
  } finally {
    if (oldDataSource.isInitialized) {
      await oldDataSource.destroy();
    }
    rl.close();
    process.exit(0);
  }
}

migrateFromOldBilling().catch((error) => {
  console.error("Fatal error:", error);
  process.exit(1);
});
