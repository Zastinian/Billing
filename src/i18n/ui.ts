import en from "./language/en-US/billing.json";
import es from "./language/es-ES/billing.json";

export const showDefaultLang = false;

export const languages = {
    en: "English",
    es: "Español",
};

export const defaultLang = "en";

export const ui = {
    en,
    es,
} as const;

export interface FlattenedUiType {
    [key: string]: string;
}

function flattenUi(ui: typeof en): FlattenedUiType {
    const flattenedUi: FlattenedUiType = {};

    function flatten(obj: Record<string, any>, path: string[] = []) {
        for (const key in obj) {
            const newPath = [...path, key];
            if (typeof obj[key] === "object" && obj[key] !== null) {
                flatten(obj[key], newPath);
            } else {
                flattenedUi[newPath.join(".")] = obj[key];
            }
        }
    }

    flatten(ui);

    return flattenedUi;
}

export const flattenedUi = {
    en: flattenUi(en),
    es: flattenUi(es),
} as const;
