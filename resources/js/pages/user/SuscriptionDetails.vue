<template>
    <v-container class="px-0 px-md-3">
        <v-row>
            <v-col cols="12 invoice-div">
                <SuscriptionHistory1
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

        <div class="form">
            <v-row>
                <v-col cols="12" md="6">
                    <v-row>
                        <v-col cols="6">
                            <p>COBERTURA</p>
                        </v-col>
                        <v-col cols="6">
                            <p>Nacional</p>
                        </v-col>
                        <v-col cols="6">
                            <p>GARANTIA</p>
                        </v-col>
                        <v-col cols="6">
                            <p>3 Meses</p>
                        </v-col><v-col cols="6">
                            <p>JORNADAS DE SERVICIO</p>
                        </v-col>
                        <v-col cols="6">
                            <p>7 am-11 am y 1 pm-5 pm</p>
                        </v-col>
                    </v-row>    
                </v-col>
            </v-row>
        </div>
        <v-divider class="mt-6 mb-3"></v-divider>
        <Summary :order-details="order" />
    </v-container>
</template>

<script>
import SuscriptionHistory1 from "../../components/global/SuscriptionHistory1.vue";
import Summary from "../../components/order/Summary.vue";

export default {
    data() {
        return {
            dialog: false,
            order: {}
        };
    },
    components: {
        SuscriptionHistory1,
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

.form {
    border: 1px solid #f5f5f5;
    border-radius: 10px;
    padding: 10px;
    background: #f5f5f5;
}
</style>
