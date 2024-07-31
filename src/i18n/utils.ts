import { type FlattenedUiType, defaultLang, flattenedUi, showDefaultLang, ui } from "./ui";

export function getLangFromUrl(url: URL) {
	const [, lang] = url.pathname.split("/");
	if (lang in ui) return lang as keyof typeof ui;
	return defaultLang;
}

export function useTranslations(lang: keyof typeof ui) {
	return function t(key: keyof FlattenedUiType) {
		return flattenedUi[lang][key] || flattenedUi[defaultLang][key];
	};
}

export function useTranslatedPath(lang: keyof typeof ui) {
	return function translatePath(path: string, l: string = lang) {
		return !showDefaultLang && l === defaultLang ? path : `/${l}${path}`;
	};
}
