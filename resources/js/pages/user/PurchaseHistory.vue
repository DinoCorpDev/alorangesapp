<template>
    <v-row>
        <v-col class="pa-0" cols="12">
            <div v-if="orders.length > 0">
                <h5 class="mb-5 mt-1" style="font-size: 25px; font-weight: 400; text-align: left;">Orden de pedidos</h5>
                <v-row
                    no-gutters
                    class="compras-items"
                    style="max-width: 100%; max-height: 450px; overflow-y: auto"
                >
                    <v-col cols="12" v-for="(item, i) in orders" :key="i">
                        <OrderHistory
                            style="background-color: #f5f5f5"
                            :order="item?.code"
                            :date="item?.date"
                            colorStatus="red"
                            :descriptionStatus="item.orders[0]?.payment_status"
                            icon1="la-download"
                            icon2="la-eye"
                            icon3="la-print"
                        />
                    </v-col>
                </v-row>
            </div>
            <div v-else class="text-center">
                <div class="emptycart">
                    <div class="cuadro-emptycart">
                        <v-img class="img-cartempty mb-6" src="/public/assets/img/icons/factura.svg" />
                        <p class="text-cartempty">AUN NO HAY FACTURAS REGISTRADAS</p>
                        <CustomButton text="IR A PRODUCTOS" color="nero" class="mt-2" :to="{ name: 'Shop' }" />
                    </div>
                </div>
            </div>
        </v-col>
    </v-row>
</template>

<script>
import OrderHistory from "../../components/global/OrderHistory.vue";

export default {
    data: () => ({
        loading: true,
        currentPage: 1,
        totalPages: 1,
        orders: [],
        tab: null,
        selectedOrder: {}
    }),
    components: {
        OrderHistory
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
.compras {
    &-title {
        font-weight: 600;
        font-size: 13px;
        background: #000;
        width: 30%;
        padding: 5px;
        color: #fff;
        text-align: center;
        border-radius: 15px;
    }

    &-items {
        gap: 0.5rem;
    }
}

.container {
    background-color: #fafcfc;
}

.theme--light.v-tabs-items {
    background-color: #fafcfc !important;
}
.invoice-div {
    padding: 8px 10px 0 10px;
}

// Estilos carrito vacio //
.emptycart {
    display: flex;
    justify-content: center;
}
.cuadro-emptycart {
    padding: 40px 50px;
    text-align: center;
    border: 1px solid #f5f5f5;
    background-color: #fff;
    margin-top: 7%;
    border-radius: 10px;
}
.text-cartempty {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 0;
}
.img-cartempty {
    width: 45%;
    margin: auto;
}

.v-tabs {
    &::v-deep {
        .v-slide-group__prev,
        .v-slide-group__next {
            display: none !important;
        }

        .v-tabs-bar__content {
            gap: 0.75rem;

            @media (min-width: 600px) {
                gap: 1.5rem;
            }
        }
    }

    .v-tab {
        background-color: #fafcfc;
        border: 1px solid #dfdfdf;
        border-radius: 100px;
        color: #000000 !important;
        font-size: var(--font-size-btn);
        font-weight: 600;
        letter-spacing: 1.25px;
        line-height: 17px;
        // flex: 1; // All tabs same width

        &:not(.v-tab--active):hover {
            background-color: #dfdfdf;
            color: #000000 !important;
        }

        &--active {
            background-color: #000000;
            color: #ffffff !important;
        }
    }
}

@media (min-width: 800px) {
    .barra-superior {
        width: 70%;
    }
}
</style>
