import { defaultLang, type FlattenedUiType, flattenedUi, type ui } from "./ui";

export function useTranslations(lang: keyof typeof ui) {
  return function t(key: keyof FlattenedUiType) {
    return flattenedUi[lang][key] || flattenedUi[defaultLang][key];
  };
}
