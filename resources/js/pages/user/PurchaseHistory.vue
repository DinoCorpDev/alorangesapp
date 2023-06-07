<template>
    <v-container>
        <h6>Compras</h6>
        <v-divider class="my-3"></v-divider>
        <v-row v-for="(item, i) in orders" :key="i">
            <v-col cols="12 invoice-div">
                <order-history
                    style="background-color: #f5f5f5"
                    :order="item?.code"
                    :date="item?.date"
                    colorStatus="red"
                    :descriptionStatus="item.orders[0].payment_status"
                    icon1="la-download"
                    icon2="la-eye"
                    icon3="la-print"
                />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import OrderHistory from "../../components/global/OrderHistory.vue";
import CustomButton from "../../components/global/CustomButton.vue";

export default {
    data: () => ({
        loading: true,
        currentPage: 1,
        totalPages: 1,
        orders: [],
        selectedOrder: {}
    }),
    components: {
        OrderHistory,
        CustomButton
    },
    computed: {
        headers() {
            return [
                {
                    text: this.$i18n.t("details"),
                    align: "start",
                    sortable: false,
                    value: "details"
                },
                {
                    text: this.$i18n.t("info"),
                    sortable: false,
                    value: "info"
                },
                {
                    text: this.$i18n.t("amount"),
                    sortable: false,
                    value: "grand_total"
                },
                {
                    text: this.$i18n.t("actions"),
                    sortable: false,
                    align: "end",
                    value: "actions"
                }
            ];
        }
    },
    watch: {
        currentPage() {
            this.$router
                .push({
                    query: {
                        ...this.$route.query,
                        page: this.currentPage
                    }
                })
                .catch(() => {});
        }
    },
    methods: {
        getProductsCount(combinedOrder) {
            let count = 0;
            combinedOrder.orders.forEach(order => {
                count += order.products.data.length;
            });
            return count;
        },
        async getList(number) {
            this.loading = true;
            const res = await this.call_api("get", `user/orders?page=${number}`);
            if (res.data.success) {
                this.orders = res.data.data;
                this.totalPages = res.data.meta.last_page;
                this.currentPage = res.data.meta.current_page;
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.loading = false;
        },
        openOrderDetails(order) {
            this.$router.push({ name: "OrderDetails", params: { code: order.code } });
        }
    },
    created() {
        let page = this.$route.query.page || this.currentPage;
        this.getList(page);
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
