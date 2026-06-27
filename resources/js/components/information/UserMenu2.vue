<template>
    <div style="width: 100%">
        <div style="width: 100%" class="d-none d-lg-block">
            <div class="buttons">
                <custom-button
                    block
                    class="mb-3 buttons-res mt-3"
                    color="nero"
                    text="Terminos y condiciones"
                    :to="{ name: 'TerminosCondiciones' }"
                />
                <custom-button
                    block
                    class="mb-3 buttons-res"
                    color="nero"
                    text="Poliza de garantia"
                    :to="{ name: 'PolizaGarantia' }"
                />
                <custom-button
                    block
                    class="mb-3 buttons-res"
                    color="nero"
                    text="Pacto ambiental"
                    :to="{ name: 'PactoAmbiental' }"
                />
                <custom-button
                    block
                    class="mb-3"
                    color="nero"
                    text="Logistica de envio"
                    :to="{ name: 'LogisticaEnvio' }"
                />
                <custom-button block class="mb-3" color="nero" text="Metodo de pago" :to="{ name: 'MetodoPago' }" />
                <custom-button
                    block
                    class="mb-3"
                    color="nero"
                    text="Privacidad y cookies"
                    :to="{ name: 'PrivacidadCokies' }"
                />
            </div>
        </div>
        <div style="width: 100%" class="mt-5 d-lg-none d-sm-flex d-md-flex">
            <v-row class="buttons">
                <v-col cols="col">
                    <custom-button
                        block
                        color="nero"
                        text="Terminos y condiciones"
                        :to="{ name: 'TerminosCondiciones' }"
                    />
                </v-col>
                <v-col cols="col">
                    <custom-button block color="nero" text="Poliza de garantia" :to="{ name: 'PolizaGarantia' }" />
                </v-col>
                <v-col cols="col">
                    <custom-button block color="nero" text="Pacto ambiental" :to="{ name: 'PactoAmbiental' }" />
                </v-col>
                <v-col cols="col">
                    <custom-button block color="nero" text="Logistica de envio" :to="{ name: 'LogisticaEnvio' }" />
                </v-col>
                <v-col cols="col">
                    <custom-button block class="" color="nero" text="Metodo de pago" :to="{ name: 'MetodoPago' }" />
                </v-col>
                <v-col cols="col">
                    <custom-button block color="nero" text="Privacidad y cookies" :to="{ name: 'PrivacidadCokies' }" />
                </v-col>
            </v-row>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CustomButton from "../../components/global/CustomButton.vue";
export default {
    components: {
        CustomButton
    },
    computed: {
        ...mapGetters("app", ["generalSettings", "getUnseenProductQuerries"]),
        routes() {
            return this.getMenuItems().sort((a, b) => a.order - b.order);
        }
    },
    methods: {
        ...mapActions(["auth/logout"]),
        ...mapActions("cart", ["resetCart"]),
        ...mapActions("wishlist", ["resetWishlist"]),
        async logout() {
            const res = await this.call_api("get", "auth/logout");
            this["auth/logout"]();
            this.resetCart();
            this.resetWishlist();
            this.$router.push({ name: "Home2" }).catch(() => {});
        },
        getMenuItems() {
            let items = [
                {
                    label: this.$i18n.t("dashboard"),
                    order: 10,
                    icon: "las la-th-large",
                    to: "DashBoard"
                },
                {
                    label: this.$i18n.t("purchase_history"),
                    order: 20,
                    icon: "las la-file-invoice-dollar",
                    to: "PurchaseHistory"
                },
                {
                    label: this.$i18n.t("wishlist"),
                    order: 30,
                    icon: "la la-heart-o",
                    to: "Wishlist"
                },
                {
                    label: this.$i18n.t("coupons"),
                    order: 50,
                    icon: "las la-tags",
                    to: "Coupon"
                },
                {
                    label: this.$i18n.t("manage_profile"),
                    order: 60,
                    icon: "las la-user",
                    to: "Profile"
                }
            ];

            let wallet = {
                label: this.$i18n.t("my_wallet"),
                order: 40,
                icon: "las la-wallet",
                to: "Wallet"
            };
            if (this.generalSettings.wallet_system == 1) {
                items.push(wallet);
            }

            let refund_request = {
                label: this.$i18n.t("refund_requests"),
                order: 21,
                icon: "las la-undo-alt",
                to: "RefundRequests"
            };
            if (this.is_addon_activated("refund")) {
                items.push(refund_request);
            }

            let shop = {
                label: this.$i18n.t("followed_shops"),
                order: 35,
                icon: "las la-store-alt",
                to: "FollowedShops"
            };

            if (this.is_addon_activated("multi_vendor")) {
                items.push(shop);
            }

            // conversation
            let conversation = {
                label: this.$i18n.t("product_querries") + "( " + this.getUnseenProductQuerries + " )",
                order: 36,
                icon: "la la-comment",
                to: "Conversations"
            };
            if (this.generalSettings.conversation_system == 1 && this.is_addon_activated("multi_vendor")) {
                items.push(conversation);
            }

            return items;
        }
    }
};
</script>

<style lang="scss" scoped>
.divider {
    background-color: #dfdfdf;
    width: 100%;
    height: 2px;
}

.col {
    padding: 3px;
}

.buttons {
    padding: 0 10px;
    margin-left: 0px;
    width: 100%;
}

.buttons-res {
    padding-right: 15px !important;
}
</style>
