<template>
    <div>
        <button
            class="side-cart-btn text-center fw-500 fs-12 d-none d-lg-block"
            @click.stop="updateCartDrawer(true)"
        >
            <span class="d-block">
                <i class="las la-shopping-cart" />
                <span>{{ getCartCount }} {{ $t("items") }}</span>
            </span>
            <span class="d-block white primary--text rounded mt-1 px-1 py-2 lh-1" >{{ format_price(getCartPrice - getTotalCouponDiscount) }}</span>
        </button>
        <!-- width fijo de 400px: en moviles de 360-390px el panel era MAS
             ancho que la pantalla. Y con `hide-overlay` no habia fondo que
             tocar para cerrarlo, asi que en tactil quedaba practicamente
             atrapado. Ahora ocupa el ancho completo en xs y tiene overlay. -->
        <v-navigation-drawer
            class="cart-drawer"
            :width="$vuetify.breakpoint.xsOnly ? '100%' : 400"
            :value="cartDrawerOpen"
            fixed
            temporary
            right
            clipped
            @input="updateCartDrawer"
        >
        
            <cart-for-multi v-if="is_addon_activated('multi_vendor')"/>
            <cart-for-single v-else />
            
        </v-navigation-drawer>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import CartForMulti from './CartForMulti';
import CartForSingle from './CartForSingle';
export default {
    components: { CartForMulti, CartForSingle },
    computed: {
        ...mapGetters("cart", [
            "getCartCount",
            "getCartPrice",
            "getTotalCouponDiscount",
        ]),
        ...mapGetters("auth", ["cartDrawerOpen"]),
    },
    methods: {
        ...mapActions("cart", ["fetchCartProducts"]),
        ...mapMutations("auth", ["updateCartDrawer"]),
    },
    created() {
        this.fetchCartProducts();
    }
};
</script>

<style scoped>
.cart-drawer {
    z-index: 610;
}
</style>
