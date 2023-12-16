<template>
    <v-container class="px-0 px-md-3">
        <v-row>
            <v-col cols="12 invoice-div">
                <ProyectHistory1
                    style="background-color: #f5f5f5;"
                    :order="order?.code"
                    :date="order?.date"
                    colorStatus="red"
                    :descriptionStatus="order.orders[0].payment_status"
                    icon1="la-download"
                    icon3="la-print"
                />
            </v-col>
        </v-row>
        <v-divider class="mt-6 mb-3"></v-divider>
        <Summary :order-details="order" ></Summary>
    </v-container>
</template>

<script>
import ProyectHistory1 from "../../components/global/ProyectHistory1.vue";
import Summary from "../../components/order/Summary.vue";

export default {
    data() {
        return {
            dialog: false,
            order: {}
        };
    },
    components: {
        ProyectHistory1,
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
    background-color: #fafcfc;
}

.invoice-div {
    padding: 8px 10px 0 10px;
}
</style>
