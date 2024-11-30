<template>
    <v-app class="d-flex flex-column">
        <Navbar display="position: fixed; top: 0px" v-if="$route.meta.hasHeader && $route.name == 'Home2'" />
        <NavbarAuth v-if="$route.meta.hasHeader && $route.name != 'Home2'" @toggleMenu="toggleMenu" />
    
        <v-main class="aiz-main-wrap">
            <Breadcrumb />
            
            <!-- prettier-ignore -->
            <v-navigation-drawer v-model="userNavDrawerActive" fixed temporary right style="z-index: 999">
                <SideMenu class="pa-3" />
            </v-navigation-drawer>
            <router-view :key="['ShopDetails','ShopCoupons','ShopProducts'].includes($route.name) ? null : $route.path"></router-view>
        </v-main>

        <Footer v-if="$route.meta.hasFooter" :class="[{ 'd-none': routerLoading }]" />

        <AddToCartDialog />
        <LoginDialog v-if="!isAuthenticated" />
        <SnackBar />
    </v-app>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";

import AddToCartDialog from "./product/AddToCartDialog";
import Footer from "./footer/Footer";
import LoginDialog from "./auth/LoginDialog.vue";
import Navbar from "./header/Navbar.vue";
import NavbarAuth from "./header/NavbarAuth.vue";
import SnackBar from "./inc/SnackBar";
import Breadcrumb from "./header/Breadcrumb.vue";
import SideMenu from "./user/SideMenu";

export default {
    metaInfo() {
        return {
            title: this.appMetaTitle,
            
        };
    },
    data(){
        return{
            userNavDrawerActive: false,
        }
    },
    components: {
        AddToCartDialog,
        Footer,
        LoginDialog,
        Breadcrumb,
        Navbar,
        NavbarAuth,
        SideMenu,
        SnackBar
    },
    watch: {
        $route(to, from) {
            window.scrollTo(0, 0); // Esto forzará el scroll al tope en cada cambio de ruta
        }
    },
    computed: {
        ...mapGetters("auth", ["isAuthenticated"]),
        ...mapGetters("cart", ["getTempUserId"]),
        ...mapGetters("app", ["appMetaTitle", "userLanguageObj", "routerLoading", "maintenanceMode"])
    },
    methods: {
        ...mapActions("auth", ["getUser", "checkSocialLoginStatus"]),
        ...mapActions("wishlist", ["fetchWislistProducts", "fetchWislistServices", "fetchWislistBrands"]),
        ...mapActions("cart", ["fetchCartProducts"]),
        ...mapMutations("auth", ["setSociaLoginStatus"]),
        changeRTL() {
            if (this.userLanguageObj.rtl == 1) {
                this.$vuetify.rtl = true;
            } else {
                this.$vuetify.rtl = false;
            }
        },
        toggleMenu(){
            this.userNavDrawerActive = !this.userNavDrawerActive;
        },
        async getTempCartData() {
            if (this.isAuthenticated && this.getTempUserId) {
                const res = await this.call_api("post", "temp-id-cart-update", {
                    temp_user_id: this.getTempUserId
                });
                this.fetchCartProducts();
            }
        },
        async getCartData() {
            if (this.isAuthenticated) {
                this.fetchCartProducts();
                this.fetchWislistProducts();
                this.fetchWislistServices();
                this.fetchWislistBrands();
            }
        },
    },
    async created() {
        this.changeRTL();
        await this.getUser();
        setTimeout(() => {
            this.checkSocialLoginStatus();
            this.getTempCartData();
            this.getCartData();
        }, 200);
    }
};
</script>

<style scoped>
.absolute-full {
    background: #fff;
    z-index: 10000;
}
</style>
