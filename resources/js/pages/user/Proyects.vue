<template>
    <v-row class="proyects">
        <v-col cols="12">
            <h5 class="proyects-title">COLECCIONES</h5>
            <v-divider class="my-3" />
            <v-row v-if="orders.length > 0" no-gutters class="proyects-items" style="max-width: 95%; max-height: 450px; overflow-y: scroll;">
                <v-col cols="12" v-for="(item, i) in orders" :key="i">
                    <ProyectHistory
                        style="background-color: #f5f5f5;"
                        :order="item?.code"
                        :date="item?.date"
                        colorStatus="red"
                        :descriptionStatus="item.orders[0]?.payment_status"
                        icon1="compartir"
                        icon2="ojo"
                        icon3="favorito"
                    />
                </v-col>
            </v-row>
            <div v-else class="text-center">
                <div class="emptycart">
                    <div class="cuadro-emptycart">
                        <v-img class="img-cartempty mb-6" src="/public/assets/img/icons/proyectos.svg" />
                        <p class="text-cartempty">AUN NO HAY PROYECTOS ACTIVOS</p>
                        <CustomButton text="IR A PRODUCTOS" color="nero" class="mt-2" :to="{ name: 'Shop' }" />
                    </div>
                </div>
            </div>
        </v-col>
    </v-row>
</template>

<script>
import ProyectHistory from "../../components/global/ProyectHistory.vue";
import { mapActions, mapGetters, mapMutations } from "vuex";
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
        ProyectHistory,
        CustomButton
    },
    async created() {
        await this.getUser();
        console.log(this.getUser());
    },
    computed: {
        ...mapActions("auth", ["getUser"]),
        ...mapGetters("auth", ["currentUser"]),

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
            var form_data = new FormData();
            form_data.append("user_id", this.currentUser.id);

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
.proyects {
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
    background-color: #ffffff;
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
    background-color: #f5f5f5;
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
</style>
