<template>
    <div class="inicio d-flex justify-center">
        <div class="div_total">
            <div class="cuerpo" style="display: flex; align-items: end">
                <span class="text-uppercase bold title pr-1 pr-sm-5">TOTAL PAGO</span>
                <span class="text-uppercase price"> {{ formatearMoneda(getCartPrice) }} COP</span>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import CustomFavorite from "../../components/icons/CustomFavorite.vue";

export default {
    name: "Total",
    components: {
        CustomFavorite
    },
    computed: {
        ...mapGetters("cart", ["getCartPrice", "getCartCount"])
    },
    methods:{
        formatearMoneda(valor) {
            return valor.toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.title {
    font: normal normal 600 24px/30px Overpass;
}

.price {
    // font: normal normal normal 24px/24px Roboto;
    font-size: calc(12px + 6 * var(--screen-size));
}

@media (max-width: 600px) {
    .div_total {
        text-align: center;
    }
    .title {
        font: normal normal 600 10px/23px Overpass;
    }

    .price {
        font: normal normal normal 15px/24px Roboto;
        display: flex;
        flex-wrap: nowrap;
    }

    .iva {
        font: normal normal 600 10px/13px Overpass;
    }
}

.icono-cantidad{
    background: white; 
    display: flex; 
    align-items: center; 
    justify-content: center;
    
}
</style>
