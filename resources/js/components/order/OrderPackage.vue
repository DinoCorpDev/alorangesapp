<template>
    <div style="background-color: #fafcfc;">
        <!-- <Steps :order-details="order" /> -->
        <div class="my-5">
            <h6>Lista de Pedido</h6>
            <v-divider class="my-3" />
            <v-row>
                <v-col cols="12" v-for="(product, i) in items" :key="i">
                    <ProductCart
                        :productDetails="product"
                        :productCartType="'purchase-history'"
                        :name="product?.name"
                        :price="product?.price"
                        icon1="/public/assets/img/icons/back.svg"
                        icon3="/public/assets/img/icons/marker.svg"
                        :brand="product?.brandName"
                        :cart_id="product?.id"
                        :quantity="product?.quantity"
                        :showOperation="false"
                        :isCollection="product?.isCollection ?? false"
                        :productsCollection="product?.products"
                    />
                </v-col>
            </v-row>
            <!-- <div class="grey lighten-4 border border-gray-200 pa-4 rounded d-flex justify-space-between align-center">
                <span class="fs-16 fw-700 lh-1">{{ $t("order_details") }}</span>
                <div
                    class="fs-12 red--text c-pointer"
                    v-if="order.delivery_status == 'order_placed' && order.payment_status == 'unpaid'"
                    @click="cancelOrder(order)"
                >
                    {{ $t("Cancelar orden") }}
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
            </div> -->

            <v-divider class="my-3" />
            <!-- <v-row>
                <v-col cols="6" sm="9" class="py-1 d-flex justify-start justify-sm-end align-center">
                    <p class="subtitle-2 text-uppercase font-weight-bold mb-0">Sub-total</p>
                </v-col>

                <v-col cols="6" sm="3" class="py-1 d-flex justify-end align-center">
                    <p class="body-2 mb-0">{{ format_price(priceTotal) }} COP</p>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6" sm="9" class="py-1 d-flex justify-start justify-sm-end align-center">
                    <p class="subtitle-2 text-uppercase font-weight-bold mb-0">IVA</p>
                </v-col>

                <v-col cols="6" sm="3" class="py-1 d-flex justify-end align-center">
                    <p class="body-2 mb-0">$0 COP</p>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6" sm="9" class="py-1 d-flex justify-start justify-sm-end align-center">
                    <p class="subtitle-2 text-uppercase font-weight-bold mb-0">FLETE</p>
                </v-col>

                <v-col cols="6" sm="3" class="py-1 d-flex justify-end align-center">
                    <p class="body-2 mb-0">$0 COP</p>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6" sm="9" class="py-1 d-flex justify-start justify-sm-end align-center">
                    <p class="subtitle-2 text-uppercase font-weight-bold mb-0">TOTAL</p>
                </v-col>

                <v-col cols="6" sm="3" class="py-1 d-flex justify-end align-center">
                    <p class="body-1 mb-0">{{ format_price(priceTotal) }} COP</p>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6" sm="8" class="py-1 d-flex justify-start justify-sm-end align-center"> </v-col>

                <v-col cols="12" sm="4" class="py-1 d-flex justify-center align-center">
                    <v-divider />
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="0" sm="8" class="py-1 d-flex justify-start justify-sm-end align-center"> </v-col>

                <v-col cols="12" sm="4" class="py-1 d-flex justify-start align-center" style="gap: 50px;">
                    <p class="body-1 mb-0">NUMERO DE ARTICULOS</p>
                    <p class="body-2"><Cubo /> {{totalArticles}}</p>
                </v-col>
            </v-row> -->
            <v-divider class="my-3" />
        </div>

        <ReviewDialog ref="submitReview" />
        <ConfirmDialog ref="confirmCancel" />
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import ConfirmDialog from "../../components/inc/ConfirmDialog";
import ProductCart from "../../components/global/ProductCart.vue";
import ReviewDialog from "./ReviewDialog";
import Cubo from "../../components/icons/Cubo.vue";
import Steps from "./Steps";

export default {
    components: {
        ConfirmDialog,
        ProductCart,
        Cubo,
        ReviewDialog,
        Steps
    },
    props: {
        orderDetails: { type: Object, default: () => {} }
    },
    data() {
        return {
            items: [],
            priceTotal: 0,
            totalArticles: 0,
        };
    },
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
    },
    created() {
        this.items = this.order?.products?.data;
        this.items.map(col =>{
            this.priceTotal += col.price,
            this.totalArticles += col.quantity
        });

        if (this.order?.collections && this.order?.collections.length > 0) {
            this.order?.collections.map(col => {
                this.items.push({
                    reference: col?.collection?.referencia,
                    name: col?.collection?.coleccion,
                    price: col?.price,
                    brandName: col?.brand?.name,
                    cart_id: col?.id,
                    qty: col?.quantity,
                    isCollection: true,
                    products: col?.collection?.productos
                });
            });
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

.div-total p {
    padding-left: 5px;
}
</style>
