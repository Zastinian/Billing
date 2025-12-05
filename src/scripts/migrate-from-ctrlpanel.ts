import { createInterface } from "readline";
import { DataSource } from "typeorm";
import AppDataSource from "../database/data-source";
import { Categories } from "../database/entities/Categories";
import { Clients } from "../database/entities/Clients";
import { Coupons } from "../database/entities/Coupons";
import { Credits } from "../database/entities/Credits";
import { PlanCycles } from "../database/entities/PlanCycles";
import { Plans } from "../database/entities/Plans";
import { Servers } from "../database/entities/Servers";

const rl = createInterface({
  input: process.stdin,
  output: process.stdout,
});

const question = (query: string): Promise<string> => {
  return new Promise((resolve) => {
    rl.question(query, resolve);
  });
};

async function migrateFromCtrlPanel() {
  console.log("=== Migration from CtrlPanel ===\n");
  console.log("Please enter the CtrlPanel database connection details:\n");

  const host = await question("Database host [localhost]: ");
  const port = await question("Database port [3306]: ");
  const database = await question("Database name: ");
  const username = await question("Database username: ");
  const password = await question("Database password: ");

  console.log("\nConnecting to CtrlPanel database...");

  const ctrlPanelDataSource = new DataSource({
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
    await ctrlPanelDataSource.initialize();
    console.log("✓ Connected to CtrlPanel database\n");

    if (!AppDataSource.isInitialized) {
      await AppDataSource.initialize();
    }
    console.log("✓ Connected to new billing database\n");

    console.log("Starting migration process...\n");

    console.log("Creating default category...");
    const defaultCategory = new Categories();
    defaultCategory.name = "CtrlPanel Migrated";
    defaultCategory.description = "Products migrated from CtrlPanel";
    defaultCategory.order = 1000;
    await AppDataSource.manager.save(defaultCategory);
    console.log(`✓ Created default category (ID: ${defaultCategory.id})\n`);

    console.log("Migrating users to clients...");
    const ctrlPanelUsers = await ctrlPanelDataSource.query("SELECT * FROM users");
    const userIdMapping = new Map<number, number>();

    for (const ctrlUser of ctrlPanelUsers) {
      const client = new Clients();
      client.email = ctrlUser.email;
      client.emailVerifiedAt = ctrlUser.email_verified_at;
      client.userId = ctrlUser.pterodactyl_id;

      client.password = ctrlUser.password;
      client.credit = Number.parseFloat(ctrlUser.credits || 0);
      client.currency = 1;

      client.autoRenew = 1;

      client.isActive = 1;

      client.isAdmin = ctrlUser.role === "admin" ? 1 : 0;
      client.isVerified = ctrlUser.email_verified_at ? 1 : 0;
      client.sessionToken = null;
      client.createdAt = ctrlUser.created_at;
      client.updatedAt = ctrlUser.updated_at;

      const savedClient = await AppDataSource.manager.save(client);
      userIdMapping.set(ctrlUser.id, savedClient.id);
    }
    console.log(`✓ Migrated ${ctrlPanelUsers.length} users to clients\n`);

    console.log("Migrating products to plans...");
    const ctrlPanelProducts = await ctrlPanelDataSource.query("SELECT * FROM products");
    const productIdMapping = new Map<string, number>();

    for (const ctrlProduct of ctrlPanelProducts) {
      const plan = new Plans();
      plan.name = ctrlProduct.name || "Unnamed Plan";
      plan.description = ctrlProduct.description;
      plan.categoryId = defaultCategory.id;
      plan.ram = ctrlProduct.memory || 0;
      plan.cpu = ctrlProduct.cpu || 100;
      plan.disk = ctrlProduct.disk || 1000;
      plan.swap = ctrlProduct.swap || 0;
      plan.io = ctrlProduct.io || 500;
      plan.databases = ctrlProduct.databases || 0;
      plan.backups = ctrlProduct.backups || 0;
      plan.extraPorts = ctrlProduct.allocations || 0;
      plan.locationsNodesId = "[]";

      plan.nestsEggsId = "[]";

      plan.serverDescription = null;
      plan.discount = null;
      plan.coupons = null;
      plan.daysBeforeSuspend = 7;

      plan.daysBeforeDelete = 30;

      plan.globalLimit = null;
      plan.perClientLimit = null;
      plan.order = 1000;
      plan.createdAt = ctrlProduct.created_at;
      plan.updatedAt = ctrlProduct.updated_at;

      const savedPlan = await AppDataSource.manager.save(plan);
      productIdMapping.set(ctrlProduct.id, savedPlan.id);

      const planCycle = new PlanCycles();
      planCycle.planId = savedPlan.id;
      planCycle.cycleLength = 1;
      planCycle.cycleType = 2;

      planCycle.initPrice = Number.parseFloat(ctrlProduct.price || 0);
      planCycle.renewPrice = Number.parseFloat(ctrlProduct.price || 0);
      planCycle.setupFee = 0;
      await AppDataSource.manager.save(planCycle);
    }
    console.log(`✓ Migrated ${ctrlPanelProducts.length} products to plans\n`);

    console.log("Migrating servers...");
    const ctrlPanelServers = await ctrlPanelDataSource.query("SELECT * FROM servers");
    let migratedServersCount = 0;
    let skippedServersCount = 0;

    for (const ctrlServer of ctrlPanelServers) {
      const newClientId = userIdMapping.get(ctrlServer.user_id);
      const newPlanId = productIdMapping.get(ctrlServer.product_id);

      if (!newClientId || !newPlanId) {
        console.log(`⚠ Skipping server ${ctrlServer.id}: client or plan mapping not found`);
        skippedServersCount++;
        continue;
      }

      const planCycle = await AppDataSource.manager.findOne(PlanCycles, {
        where: { planId: newPlanId },
      });

      if (!planCycle) {
        console.log(
          `⚠ Skipping server ${ctrlServer.id}: no plan cycle found for plan ${newPlanId}`,
        );
        skippedServersCount++;
        continue;
      }

      const server = new Servers();
      server.serverId = ctrlServer.pterodactyl_id;
      server.identifier = ctrlServer.identifier;
      server.clientId = newClientId;
      server.planId = newPlanId;
      server.planCycle = planCycle.id;
      server.dueDate = null;

      server.serverName = ctrlServer.name || "Unnamed Server";

      server.status = ctrlServer.suspended ? 3 : 1;
      server.createdAt = ctrlServer.created_at;
      server.updatedAt = ctrlServer.updated_at;
      server.lastNotif = null;

      await AppDataSource.manager.save(server);
      migratedServersCount++;
    }
    console.log(`✓ Migrated ${migratedServersCount} servers (${skippedServersCount} skipped)\n`);

    console.log("Migrating vouchers to coupons...");
    try {
      const ctrlVouchers = await ctrlPanelDataSource.query("SELECT * FROM vouchers");
      let migratedVouchers = 0;

      for (const voucher of ctrlVouchers) {
        const coupon = new Coupons();
        coupon.code = voucher.code;

        coupon.percentOff = 100;

        coupon.oneTime = voucher.uses === 1 ? 1 : 0;
        coupon.globalLimit = voucher.uses;
        coupon.perClientLimit = 1;
        coupon.isGlobal = 1;

        coupon.endDate = voucher.expires_at;
        coupon.createdAt = voucher.created_at;
        coupon.updatedAt = voucher.updated_at;

        await AppDataSource.manager.save(coupon);
        migratedVouchers++;
      }
      console.log(`✓ Migrated ${migratedVouchers} vouchers to coupons\n`);
      console.log("⚠ Note: Vouchers were converted to 100% discount coupons\n");
    } catch {
      console.log("⚠ Vouchers table not found, skipping...\n");
    }

    console.log("Migrating payments to credits history...");
    try {
      const ctrlPayments = await ctrlPanelDataSource.query(
        "SELECT * FROM payments WHERE status = 'COMPLETED'",
      );
      let migratedPayments = 0;

      for (const payment of ctrlPayments) {
        const newClientId = userIdMapping.get(payment.user_id);
        if (!newClientId) {
          continue;
        }

        const client = await AppDataSource.manager.findOne(Clients, {
          where: { id: newClientId },
        });
        if (!client) {
          continue;
        }

        const credit = new Credits();
        credit.clientId = newClientId;
        credit.details = `Payment ${payment.payment_id || "imported"} - ${payment.type || "CtrlPanel"}`;
        credit.change = Number.parseFloat(payment.amount || payment.price || 0);
        credit.balance = client.credit;
        credit.createdAt = payment.created_at;

        await AppDataSource.manager.save(credit);
        migratedPayments++;
      }
      console.log(`✓ Migrated ${migratedPayments} payments to credit history\n`);
    } catch {
      console.log("⚠ Payments table not found or error migrating, skipping...\n");
    }

    console.log("✓ Migration completed successfully!\n");

    console.log("=== Migration Summary ===");
    console.log(`Users → Clients: ${ctrlPanelUsers.length}`);
    console.log(`Products → Plans: ${ctrlPanelProducts.length}`);
    console.log(`Servers: ${migratedServersCount} migrated, ${skippedServersCount} skipped`);

    console.log("\n⚠ Important Notes:");
    console.log("- Plan cycles were created with default monthly billing.");
    console.log("- Plans need manual configuration for locations_nodes_id and nests_eggs_id.");
    console.log("- User passwords were preserved from CtrlPanel.");
    console.log("- Users can log in with their existing credentials.");
    console.log("- Vouchers were converted to 100% discount coupons.");
    console.log("- Completed payments were added to credit history.");
    console.log("\n⚠ Not Migrated from CtrlPanel:");
    console.log("  - locations, nodes, nests, eggs (Pterodactyl-specific)");
    console.log("  - These need to be configured in your Pterodactyl panel");
    console.log("  - Update plan locations_nodes_id and nests_eggs_id accordingly");
  } catch (error) {
    console.error("❌ Migration failed:", error);
    throw error;
  } finally {
    if (ctrlPanelDataSource.isInitialized) {
      await ctrlPanelDataSource.destroy();
    }
    rl.close();
    process.exit(0);
  }
}

migrateFromCtrlPanel().catch((error) => {
  console.error("Fatal error:", error);
  process.exit(1);
});
