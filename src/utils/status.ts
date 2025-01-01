export const serverStatus = {
  canceled: 0,
  pending: 1,
  active: 2,
  suspended: 3,
  terminated: 4,
};

export const serverStatusName = {
  [serverStatus.canceled]: "canceled",
  [serverStatus.pending]: "Pending",
  [serverStatus.active]: "Active",
  [serverStatus.suspended]: "Suspended",
  [serverStatus.terminated]: "Terminated",
};

export const ticketStatus = {
  pending: 1,
  open: 2,
  closed: 3,
  resolved: 4,
};

export const ticketStatusName = {
  [ticketStatus.pending]: "Pending",
  [ticketStatus.open]: "Open",
  [ticketStatus.closed]: "Closed",
  [ticketStatus.resolved]: "Resolved",
};

export const invoiceStatus = {
  canceled: 0,
  pending: 1,
  paid: 2,
  overdue: 3,
};

export const invoiceStatusName = {
  [invoiceStatus.canceled]: "canceled",
  [invoiceStatus.pending]: "Pending",
  [invoiceStatus.paid]: "Paid",
  [invoiceStatus.overdue]: "Overdue",
};

export const cycleType = {
  oneTime: 0,
  hourly: 1,
  daily: 2,
  monthly: 3,
  yearly: 4,
};

export const cycleTypeName = {
  [cycleType.oneTime]: "One-time",
  [cycleType.hourly]: "Hourly",
  [cycleType.daily]: "Daily",
  [cycleType.monthly]: "Monthly",
  [cycleType.yearly]: "Yearly",
};