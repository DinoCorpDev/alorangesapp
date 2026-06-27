import Vue from "vue";
import Vuex from "vuex";
import addressModule from "./modules/address";
import appModule from "./modules/app";
import authModule from "./modules/auth";
import cartModule from "./modules/cart";
import recentlyViewed from "./modules/recentlyViewed";
import snackBar from "./modules/snackbar";
import wishlistModule from "./modules/wishlist";
import breadcrumb from "./modules/breadcrumb";
import followModule from "./modules/follow";
import compareList from "./modules/compareList";

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        app: appModule,
        auth: authModule,
        address: addressModule,
        wishlist: wishlistModule,
        breadcrumb: breadcrumb,
        follow: followModule,
        cart: cartModule,
        snackbar: snackBar,
        recentlyViewed: recentlyViewed,
        compareList: compareList
    }
});

export default store;
