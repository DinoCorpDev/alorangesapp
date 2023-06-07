<template>
    <v-stepper class="order-steps mb-4 mb-sm-0" v-if="!is_empty_obj(orderDetails)" elevation="0" :value="getCurrentStatus()" alt-labels >
        <v-stepper-header>
            <template v-for="(step, i) in steps" >
                <v-stepper-step :key="`${i}-step`" :step="i+1" :complete="getStepStatus(step.status)" complete-icon="la-check" error-icon="la-times" :rules="[getCancelStatus]" color="green">
                    {{ getLevel(step.level) }}
                </v-stepper-step>
                <v-divider v-if="i !== steps.length - 1" :key="i" :class="[{'complete':getStepStatus(step.status) }]"></v-divider>
            </template>
        </v-stepper-header>
    </v-stepper>
</template>

<script>
export default {
    props: {
        orderDetails: { type: Object, default:  {} }
    },
    data: () => ({
        
    }),
    computed:{
        steps(){
            return [
                {
                    level: this.$i18n.t('order_placed'),
                    status: 'order_placed'
                },
                {
                    level: this.$i18n.t('confirmed'),
                    status: 'confirmed'
                },
                {
                    level: this.$i18n.t('processed'),
                    status: 'processed'
                },
                {
                    level: this.$i18n.t('shipped'),
                    status: 'shipped'
                },
                {
                    level: this.$i18n.t('delivered'),
                    status: 'delivered'
                }
            ]
        }
    },
    methods: {
        getLevel(level){
            return this.orderDetails.delivery_status == 'cancelled' ? this.$i18n.t('cancelled') : level
        },
        getCancelStatus(){
            return this.orderDetails.delivery_status == 'cancelled' ? false : true
        },
        getCurrentStatus(){
            return this.steps.findIndex( step => step.status == this.orderDetails.delivery_status) + 2
        },
        getStepStatus (status) {
            let activeIndex = this.steps.findIndex( step => step.status == this.orderDetails.delivery_status)
            let currentIndex = this.steps.findIndex( step => step.status == status)
            return currentIndex <= activeIndex
        }
    }
}
</script>