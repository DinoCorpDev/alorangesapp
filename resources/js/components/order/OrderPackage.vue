<template>
    <div>
        <div class="my-5">
            <h6>Lista de Pedido</h6>
            <v-divider class="my-3" />
            <v-row>
                <v-col cols="12" v-for="(product, i) in order.products.data" :key="i">
                    <ProductCart
                        :name="product?.name"
                        :price="product?.price"
                        icon1="/public/assets/img/icons/back.svg"
                        icon3="/public/assets/img/icons/marker.svg"
                        :brand="product?.brandName"
                        :cart_id="product?.id"
                        :quantity="product?.quantity"
                        :showOperation="false"
                    />
                </v-col>
            </v-row>
            <div class="grey lighten-4 border border-gray-200 pa-4 rounded d-flex justify-space-between align-center">
                <span class="fs-16 fw-700 lh-1">{{ $t("order_details") }}</span>
                <div
                    class="fs-12 red--text c-pointer"
                    v-if="order.delivery_status == 'order_placed' && order.payment_status == 'unpaid'"
                    @click="cancelOrder(order)"
                >
                    {{ $t("cancel_order") }}
                </div>
                <div
                    class="fs-12 red--text c-pointer"
                    v-if="
                        is_addon_activated('refund') &&
                        !order.has_refund_request &&
                        order.payment_status == 'paid' &&
                        today <= order.created_at + refundSettings.refund_request_time_period &&
                        refundSettings.refund_request_order_status.includes(order.delivery_status)
                    "
                    @click="refundRequest(order.id)"
                >
                    {{ $t("request_refund") }}
                </div>
            </div>
            <v-divider class="my-3" />
            <div class="div-total">
                <div>
                    <p class="subtitle-2 text-uppercase font-weight-bold">Sub-total</p>
                    <p class="body-2">000.000.000 COP</p>
                </div>
                <div>
                    <p class="subtitle-2 text-uppercase font-weight-bold">Iva</p>
                    <p class="body-2">000.000.000 COP</p>
                </div>
                <div>
                    <p class="subtitle-2 text-uppercase font-weight-bold">Flete</p>
                    <p class="body-2">000.000.000 COP</p>
                </div>
                <div>
                    <p class="subtitle-1 text-uppercase font-weight-bold">Total</p>
                    <p class="body-1">000.000.000 COP</p>
                </div>
            </div>
            <v-divider class="my-3" />
        </div>

        <Steps :order-details="order" />

        <ReviewDialog ref="submitReview" />
        <ConfirmDialog ref="confirmCancel" />
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import ConfirmDialog from "../../components/inc/ConfirmDialog";
import ProductCart from "../../components/global/ProductCart.vue";
import ReviewDialog from "./ReviewDialog";
import Steps from "./Steps";

export default {
    components: {
        ConfirmDialog,
        ProductCart,
        ReviewDialog,
        Steps
    },
    props: {
        orderDetails: { type: Object, default: () => {} }
    },
    data: () => ({
        cod_sticker: Vue.helpers.asset("/assets/img/cod_sticker.svg"),
        paid_sticker: Vue.helpers.asset("/assets/img/paid_sticker.svg"),
        today: new Date().getTime() / 1000
    }),
    computed: {
        order: {
            get() {
                return this.orderDetails;
            },
            set(newVal) {}
        },
        headers() {
            let headers = [
                {
                    text: "#",
                    align: "start",
                    sortable: false,
                    value: "serial"
                },
                {
                    text: this.$i18n.t("product"),
                    sortable: false,
                    value: "product"
                },
                {
                    text: this.$i18n.t("quantity"),
                    sortable: false,
                    value: "quantity"
                },
                {
                    text: this.$i18n.t("unit_price"),
                    sortable: false,
                    value: "unit_price"
                },
                {
                    text: this.$i18n.t("total"),
                    sortable: false,
                    align: "end",
                    value: "total"
                }
            ];

            if (this.order.delivery_status == "delivered") {
                headers.push({
                    text: "",
                    align: "end",
                    sortable: false,
                    value: "review"
                });
            }

            return headers;
        },
        ...mapGetters("app", ["refundSettings"])
    },
    methods: {
        cancelOrder(order) {
            this.$refs.confirmCancel
                .open(this.$i18n.t("confirm_cancel"), this.$i18n.t("are_you_sure_you_want_to_cancel_this_order"))
                .then(res => {
                    if (res) {
                        this.cancelConfirmed(order.id);
                    }
                });
        },
        refundRequest(order_id) {
            this.$router.push({ name: "CreateRefundRequest", params: { orderId: order_id } });
        },
        async cancelConfirmed(order_id) {
            this.snack({
                message: this.$i18n.t("cancelling_order"),
                timeout: -1
            });
            const res = await this.call_api("get", `user/order/cancel/${order_id}`);
            if (res.data.success) {
                this.snack({ message: res.data.message });
                this.order.delivery_status = "cancelled";
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }
        }
    }
};
</script>

<style scoped>
.div-total {
    display: flex;
    flex-direction: column;
}

.div-total div {
    display: flex;
    justify-content: flex-end;
}
</style>
