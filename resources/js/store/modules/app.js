import Mixin from "./../../utils/mixin";

const shopSetting = window.shopSetting;

const loadState = () => ({
    appName: shopSetting.appName,
    appMetaTitle: shopSetting.appMetaTitle,
    appLogo: shopSetting.appLogo,
    appUrl: shopSetting.appUrl,
    demoMode: shopSetting.demoMode,
    maintenanceMode: shopSetting.maintenanceMode,
    appLanguage: shopSetting.appLanguage,
    paymentMethods: shopSetting.paymentMethods,
    offlinePaymentMethods: shopSetting.offlinePaymentMethods,
    userCountry: localStorage.getItem("shopSelectedCountry") || "US",
    userCurrency: localStorage.getItem("shopSelectedCurrency") || shopSetting.appCurrency,
    userLanguage: localStorage.getItem("shopSelectedLanguage") || shopSetting.appLanguage,
    userTheme: localStorage.getItem("shopSelectedTheme") || "day",
    allCountries: shopSetting.allCountries,
    allCurrencies: shopSetting.allCurrencies,
    allLanguages: shopSetting.allLanguages,
    availableCountries: shopSetting.availableCountries.length > 0 ? shopSetting.availableCountries : ["CO"],
    generalSettings: shopSetting.general_settings,
    addons: shopSetting.addons,
    banners: shopSetting.banners,
    apiPath: "/api/v1/",
    routerLoading: true,
    refundSettings: shopSetting.refundSettings,
    productQuerries: [],
    unseenProductQuerries: 0,
    previewAvatar: null,
    authFooterLinks: [
        { label: "Información", link: "#" },
        { label: "Solicitudes", link: "#" },
        { label: "Contacto", link: "#" }
    ],
    themeItems: [
        { code: "day", label: "Day" },
        { code: "night", label: "Night" },
        { code: "device", label: "Device" }
    ],
    prefersDark: window.matchMedia("(prefers-color-scheme: dark)").matches,
    shopRegistrationMessageTitle: shopSetting.shop_registration_message.shop_registration_message_title,
    shopRegistrationMessageContent: shopSetting.shop_registration_message.shop_registration_message_content
});

export default {
    namespaced: true,
    state: loadState(),
    getters: {
        appName(state) {
            return state.appName;
        },
        appMetaTitle(state) {
            return state.appMetaTitle;
        },
        appLogo(state) {
            return state.appLogo;
        },
        appUrl(state) {
            return state.appUrl.slice(-1) == "/" ? state.appUrl.slice(0, state.appUrl.length - 1) : state.appUrl;
        },
        demoMode(state) {
            return state.demoMode;
        },
        maintenanceMode(state) {
            return state.maintenanceMode;
        },
        apiPath(state) {
            return state.apiPath;
        },
        appLanguage(state) {
            return state.appLanguage;
        },
        userCountryObj(state) {
            return state.allCountries.find(country => country.code === state.userCountry);
        },
        userCurrencyObj(state) {
            return state.allCurrencies.find(currency => currency.code === state.userCurrency);
        },
        userLanguage(state) {
            return state.userLanguage;
        },
        userLanguageObj(state) {
            return state.allLanguages.find(language => language.code === state.userLanguage);
        },
        userThemeObj(state) {
            return state.themeItems.find(theme => theme.code === state.userTheme);
        },
        paymentMethods(state) {
            return state.paymentMethods;
        },
        offlinePaymentMethods(state) {
            return state.offlinePaymentMethods;
        },
        allLanguages(state) {
            return state.allLanguages;
        },
        availableCountries(state) {
            return state.availableCountries;
        },
        allCurrencies(state) {
            return state.allCurrencies;
        },
        generalSettings(state) {
            return state.generalSettings;
        },
        banners(state) {
            return state.banners;
        },
        addons(state) {
            return state.addons;
        },
        routerLoading(state) {
            return state.routerLoading;
        },
        refundSettings(state) {
            return state.refundSettings;
        },
        getProductQuerries(state) {
            return state.productQuerries;
        },
        getUnseenProductQuerries(state) {
            return state.unseenProductQuerries;
        },
        shopRegistrationMessageTitle(state) {
            return state.shopRegistrationMessageTitle;
        },
        shopRegistrationMessageContent(state) {
            return state.shopRegistrationMessageContent;
        }
    },
    mutations: {
        setCountry(state, country) {
            if (state.userCountry !== country) {
                state.userCountry = country;

                localStorage.removeItem("shopSelectedCountry");
                localStorage.setItem("shopSelectedCountry", country);
            }
        },
        setCurrency(state, currency) {
            if (state.userCurrency !== currency) {
                state.userCurrency = currency;

                localStorage.removeItem("shopSelectedCurrency");
                localStorage.setItem("shopSelectedCurrency", currency);
            }
        },
        setLanguage(state, lang) {
            if (state.userLanguage !== lang) {
                state.userLanguage = lang;

                localStorage.removeItem("shopSelectedLanguage");
                localStorage.setItem("shopSelectedLanguage", lang);
            }
        },
        setPreviewAvatar(state, avatar) {
            state.previewAvatar = avatar;
        },
        setProductQuerries(state, data) {
            state.productQuerries = data;
            let unseenData = data.filter(data => {
                return data.sender_viewed == 0;
            });
            state.unseenProductQuerries = unseenData.length;
        },
        setRouterLoading(state, status) {
            state.routerLoading = status;
        },
        setTheme(state, theme) {
            if (state.userTheme !== theme) {
                state.userTheme = theme;

                localStorage.removeItem("shopSelectedTheme");
                localStorage.setItem("shopSelectedTheme", state.userTheme);
            }
        },
        removeCurrency(state) {
            state.userCurrency = state.appCurrency;
            localStorage.removeItem("shopSelectedCurrency");
        },
        removeLanguage(state) {
            state.userLanguage = state.appLanguage;
            localStorage.removeItem("shopSelectedLanguage");
        },
        setRouterLoading(state, status) {
            state.routerLoading = status;
        }
    },
    actions: {
        async fetchProductQuerries({ commit }) {
            if (this.getters["auth/isAuthenticated"]) {
                const res = await Mixin.methods.call_api("get", `user/querries`);
                if (res.data.success) {
                    commit("setProductQuerries", res.data.data);
                }
            }
        },
        removeLanguage({ commit }) {
            commit("removeLanguage");
        },
        setCountry({ commit }, country) {
            commit("setCountry", country);
        },
        setCurrency({ commit }, currency) {
            commit("setCurrency", currency);
        },
        setLanguage({ commit }, lang) {
            commit("setLanguage", lang);
        },
        setTheme({ commit }, theme) {
            commit("setTheme", theme);
        }
    }
};
