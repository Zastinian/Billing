export const serverStatus = {
  cancelled: 0,
  pending: 1,
  active: 2,
  suspended: 3,
  terminated: 4,
};

export const serverStatusName = {
  [serverStatus.cancelled]: "Cancelled",
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
  cancelled: 0,
  pending: 1,
  paid: 2,
  overdue: 3,
};

export const invoiceStatusName = {
  [invoiceStatus.cancelled]: "Cancelled",
  [invoiceStatus.pending]: "Pending",
  [invoiceStatus.paid]: "Paid",
  [invoiceStatus.overdue]: "Overdue",
};

export const cycleType = {
  daily: 0,
  weekly: 1,
  monthly: 2,
  yearly: 3,
};

export const cycleTypeName = {
  [cycleType.daily]: "Daily",
  [cycleType.weekly]: "Weekly",
  [cycleType.monthly]: "Monthly",
  [cycleType.yearly]: "Yearly",
};