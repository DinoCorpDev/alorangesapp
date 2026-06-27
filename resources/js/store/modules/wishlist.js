import Mixin from "./../../utils/mixin";
import { i18n } from "./../../plugins/i18n";

const loadState = () => ({
    wislistLoaded: false,
    wislistProductIds: [],
    wislistServicesIds: [],
    wislistBrandsIds: [],
    wislistProducts: [],
    wisListServices: [],
    wisListBrands: []
});

export default {
    namespaced: true,
    state: loadState(),
    getters: {
        wislistLoaded(state) {
            return state.wislistLoaded;
        },
        isThisWishlisted: state => product_id => {
            return state.wislistProductIds.includes(product_id);
        },
        isThisWishlistedServices: state => service_id => {
            return state.wislistServicesIds.includes(service_id);
        },
        isThisWishlistedBrands: state => brand_id => {
            return state.wislistBrandsIds.includes(brand_id);
        },
        getTotalWishlisted(state) {
            return state.wislistProductIds.length;
        },
        getWislistProducts(state) {
            return state.wislistProducts;
        },
        getWisListServices(state) {
            return state.wisListServices;
        },
        getWisListBrands(state) {
            return state.wisListBrands;
        }
    },
    mutations: {
        setWislistProducts(state, data) {
            state.wislistProducts = data;
            state.wislistProductIds = data.map(item => item.id);
        },
        setWislistServices(state, data) {
            state.wisListServices = data;
            state.wislistServicesIds = data.map(item => item.id);
        },
        setWislistBrands(state, data) {
            state.wisListBrands = data;
            state.wislistBrandsIds = data.map(item => item.id);
        },
        addNewWishlistId(state, product_id) {
            state.wislistProductIds.push(product_id);
        },
        addNewWishlistServiceId(state, service_id) {
            state.wislistServicesIds.push(service_id);
        },
        addNewWishlistBrandId(state, brand_id) {
            state.wislistBrandsIds.push(brand_id);
        },

        addNewWishlist(state, product) {
            if (product.type === "product") {
                if (!state.wislistProducts.find(item => item.id === product.id)) {
                    state.wislistProducts.push(product);
                }
            }
            if (product.type === "service") {
                if (!state.wisListServices.find(item => item.id === product.id)) {
                    state.wisListServices.push(product);
                }
            }
            if (product.type === "brand") {
                if (!state.wisListBrands.find(item => item.id === product.id)) {
                    state.wisListBrands.push(product);
                }
            }
        },

        removeFromWishlistID(state, product_id) {
            state.wislistProductIds = state.wislistProductIds.filter(val => val !== product_id);
        },
        removeFromWishlistServiceID(state, service_id) {
            state.wislistServicesIds = state.wislistServicesIds.filter(val => val !== service_id);
        },
        removeFromWishlistBrandID(state, brand_id) {
            state.wislistBrandsIds = state.wislistBrandsIds.filter(val => val !== brand_id);
            console.log(state.wislistBrandsIds);
        },

        removeFromWishlist(state, product) {
            if (product.type === "product") {
                if (state.wislistProducts.find(item => item.id === product.id)) {
                    state.wislistProducts = state.wislistProducts.filter(val => val.id !== product.id);
                }
            }
            if (product.type === "service") {
                if (state.wisListServices.find(item => item.id === product.id)) {
                    state.wisListServices = state.wisListServices.filter(val => val.id !== product.id);
                }
            }
            if (product.type === "brand") {
                if (state.wisListBrands.find(item => item.id === product.id)) {
                    state.wisListBrands = state.wisListBrands.filter(val => val.id !== product.id);
                }
            }
        }
    },
    actions: {
        async fetchWislistServices({ commit, getters }) {
            if (this.getters["auth/isAuthenticated"] && !getters.wislistLoaded) {
                const res = await Mixin.methods.call_api("get", `user/wishlists/services`);
                if (res.data.success) {
                    commit("setWislistServices", res.data.data);
                }
            }
        },
        async fetchWislistProducts({ commit, getters }) {
            if (this.getters["auth/isAuthenticated"] && !getters.wislistLoaded) {
                const res = await Mixin.methods.call_api("get", `user/wishlists`);
                if (res.data.success) {
                    commit("setWislistProducts", res.data.data);
                }
            }
        },
        async fetchWislistBrands({ commit, getters }) {
            if (this.getters["auth/isAuthenticated"] && !getters.wislistLoaded) {
                const res = await Mixin.methods.call_api("get", `user/wishlists/brands`);
                if (res.data.success) {
                    commit("setWislistBrands", res.data.data);
                }
            }
        },
        async addNewWishlist({ commit }, product_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("addNewWishlistId", product_id);
                const res = await Mixin.methods.call_api("post", "user/wishlists", { product_id: product_id });
                if (res.data.success) {
                    res.data.product.type = "product";
                    commit("addNewWishlist", res.data.product);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("removeFromWishlistID", product_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        async addNewWishlistBrands({ commit }, brand_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("addNewWishlistBrandId", brand_id);
                const res = await Mixin.methods.call_api("post", "user/wishlists/brands", { brands_id: brand_id });
                if (res.data.success) {
                    res.data.brand.type = "brand";
                    commit("addNewWishlist", res.data.brand);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("removeFromWishlistBrandID", brand_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        async addNewWishlistServices({ commit }, service_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("addNewWishlistServiceId", service_id);
                const res = await Mixin.methods.call_api("post", "user/wishlists/services", {
                    services_id: service_id
                });
                if (res.data.success) {
                    res.data.service.type = "service";
                    commit("addNewWishlist", res.data.service);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("removeFromWishlistServiceID", service_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        async removeFromWishlist({ commit }, product_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("removeFromWishlistID", product_id);
                const res = await Mixin.methods.call_api("delete", `user/wishlists/${product_id}`);
                if (res.data.success) {
                    const objState = {
                        id: product_id,
                        type: "product"
                    };
                    commit("removeFromWishlist", objState);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("addNewWishlistId", product_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        async removeFromWishlistServices({ commit }, service_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("removeFromWishlistServiceID", service_id);
                const res = await Mixin.methods.call_api("delete", `user/wishlists/services/${service_id}`);
                if (res.data.success) {
                    product.type = "service";
                    const objState = {
                        id: service_id,
                        type: "service"
                    };
                    commit("removeFromWishlist", objState);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("addNewWishlistServiceId", service_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        async removeFromWishlistBrands({ commit }, brand_id) {
            if (this.getters["auth/isAuthenticated"]) {
                commit("removeFromWishlistBrandID", brand_id);
                const res = await Mixin.methods.call_api("delete", `user/wishlists/brands/${brand_id}`);
                if (res.data.success) {
                    const objState = {
                        id: brand_id,
                        type: "brand"
                    };
                    commit("removeFromWishlist", objState);
                    Mixin.methods.snack({
                        message: i18n.t(res.data.message),
                        color: "green"
                    });
                } else {
                    commit("addNewWishlistBrandId", brand_id);
                    Mixin.methods.snack({
                        message: i18n.t("something_went_wrong"),
                        color: "red"
                    });
                }
            } else {
                commit("auth/showLoginDialog", true, { root: true });
            }
        },
        resetWishlist({ commit }) {
            commit("resetWishlist");
        }
    }
};
