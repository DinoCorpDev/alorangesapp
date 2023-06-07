<template>
    <div v-if="!is_empty_obj(orderDetails) && orderDetails.orders.length > 0">
        <div class="grey lighten-4 border border-gray-200 pa-4 rounded fs-18 fw-700 lh-1">{{ $t('order_summary') }}</div>
        <v-row class="mb-3">
            <v-col md="6" cols="12" class="pb-0 pb-md-3">
                <v-list dense>
                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('order_code') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">{{ orderDetails.code }}</v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('name') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">{{ orderDetails.user.name }}</v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('email') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">{{ orderDetails.user.email }}</v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content class="fw-700 align-self-baseline">{{ $t('shipping_address') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">
                            <span class="d-block lh-1-6">{{ orderDetails.shipping_address.address }}, {{ orderDetails.shipping_address.postal_code }}</span>
                            <span class="d-block lh-1-6">{{ orderDetails.shipping_address.city }}, {{ orderDetails.shipping_address.state }}, {{ orderDetails.shipping_address.country }}</span>
                            <span class="lh-1-6">{{ orderDetails.shipping_address.phone }}</span>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-col>
            <v-col md="6" cols="12" class="pt-0 pt-md-3">
                <v-list dense>
                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('total_order_amount') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">{{ format_price(orderDetails.grand_total) }}</v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('payment_method') }} :</v-list-item-content>
                        <v-list-item-content class="align-end text-capitalize">{{ $t(orderDetails.orders[0].payment_type) }}</v-list-item-content>
                    </v-list-item>
                    <!-- show offline payment data -->
                    <v-list-item v-if="orderDetails.orders[0].payment_type === 'offline_payment'">
                    <v-list-item-content class="fw-700">{{ $t('payment_details') }} :</v-list-item-content>
                        <v-list-item-content class="align-end text-capitalize">
                           <span>{{ $t('transaction_id') }}:  {{ $t(orderDetails.orders[0].manual_payment_data.transactionId) }}</span>
                           <span>
                                {{ $t('paid_via') }}: {{ $t(orderDetails.orders[0].manual_payment_data.payment_method) }}
                                <a :href="appUrl+'/public/'+orderDetails.orders[0].manual_payment_data.reciept" v-if="orderDetails.orders[0].manual_payment_data.reciept" target="_blank" rel="noopener noreferrer">
                                    ({{ $t('receipt') }})
                                </a>
                            </span>
                        </v-list-item-content>
                    </v-list-item>
                    <!-- show offline payment data -->

                    <v-list-item>
                        <v-list-item-content class="fw-700">{{ $t('delivery_type') }} :</v-list-item-content>
                        <v-list-item-content class="align-end text-capitalize">{{ orderDetails.orders[0].delivery_type.replaceAll('_',' ') }}</v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content class="fw-700 align-self-baseline">{{ $t('billing_address') }} :</v-list-item-content>
                        <v-list-item-content class="align-end">
                            <span class="d-block lh-1-6">{{ orderDetails.billing_address.address }}, {{ orderDetails.billing_address.postal_code }}</span>
                            <span class="d-block lh-1-6">{{ orderDetails.billing_address.city }}, {{ orderDetails.billing_address.state }}, {{ orderDetails.billing_address.country }}</span>
                            <span class="lh-1-6">{{ orderDetails.billing_address.phone }}</span>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-row>
        <v-sheet class="" color="white" elevation="0" v-for="(order, i) in orderDetails.orders" :key="i">
            <order-package :order-details="order" />
        </v-sheet>
    </div>
</template>

<script>
import OrderPackage from './OrderPackage'
import { mapGetters } from "vuex";
export default {
    components: { OrderPackage },
    computed:{
        ...mapGetters("app",["appUrl"]),
    },
    props: {
        orderDetails: { type: Object, default: () => {} }
    },
}
</script>