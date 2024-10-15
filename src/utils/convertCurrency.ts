import type { Currencies } from "@/database/entities/Currencies";

export default (amount: number, fromCurrency: Currencies, toCurrency: Currencies): string => {
  const convertedAmount = (amount * toCurrency.rate) / fromCurrency.rate;
  return convertedAmount.toFixed(toCurrency.precision);
};
