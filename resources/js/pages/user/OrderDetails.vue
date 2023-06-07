<template>
    <v-container>
        <v-row>
            <v-col cols="12 invoice-div">
                <OrderHistory
                    style="background-color: #f5f5f5"
                    :order="order?.code"
                    :date="order?.date"
                    colorStatus="red"
                    :descriptionStatus="order.orders[0].payment_status"
                    icon1="la-download"
                    icon2="la-eye"
                    icon3="la-print"
                />
            </v-col>
        </v-row>
        <v-divider class="mt-6 mb-3"></v-divider>
        <Summary :order-details="order" />
    </v-container>
</template>

<script>
import OrderHistory from "../../components/global/OrderHistory.vue";
import Summary from "./../../components/order/Summary";

export default {
    data() {
        return {
            dialog: false,
            order: {}
        };
    },
    components: {
        OrderHistory,
        Summary
    },
    methods: {
        async getDetails(orderCode) {
            const res = await this.call_api("get", `user/order/${orderCode}`);
            if (res.data.success) {
                this.order = res.data.data;
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
                return;
            }
        }
    },
    created() {
        this.getDetails(this.$route.params.code);
    }
};
</script>

<style lang="scss" scoped>
.container {
    background-color: #ffffff;
}

.invoice-div {
    padding: 8px 10px 0 10px;
}
</style>
