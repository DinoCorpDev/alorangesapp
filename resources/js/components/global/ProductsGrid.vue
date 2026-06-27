<template>
    <v-row tag="section" class="products-grid">
        <v-col cols="12">
            <v-row dense class="mb-3">
                <v-col v-if="shopCard" cols="6" sm="8" md="4">
                    <ShopActionCard boxStyle="vertical" :href="shopCardLink" :to="shopCardTo" />
                </v-col>
                <v-col
                    v-for="product in shuffledProducts.slice(0, visibleProducts)"
                    :key="`product-col-${product.slug}`"
                    :cols="cols"
                    :sm="sm"
                    :md="md"
                    :lg="lg"
                    :xl="xl"
                >
                    <ProductBox :productDetails="product" :boxStyle="productStyle" />
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col cols="12" sm="8" md="4">
                    <CustomButton @click.stop="showMore()" block color="white" style="width: 100%;" text="Ver más"/>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import CustomButton from "../global/CustomButton.vue";
import ProductBox from "../product/ProductBox.vue";
import ShopActionCard from "../../components/shop/ShopActionCard.vue";

export default {
    props: {
        productStyle: { type: String, default: "one" },
        fitrable: { type: Boolean, default: false },
        shopCard: { type: Boolean, default: false },
        shopCardLink: { type: String, default: "#" },
        shopCardTo: { type: [String, Object] },
        products: { type: Array, default: () => [] },
        title: { type: String },
        cols: { type: String },
        sm: { type: String },
        md: { type: String },
        lg: { type: String },
        xl: { type: String },
        href: { type: String, default: "#" },
        to: { type: [String, Object] }
    },
    components: {
        CustomButton,
        ProductBox,
        ShopActionCard
    },
    data(){
        return {
            visibleProducts: 6,
            shuffledProducts: [] // Arreglo desordenado de productos
        };
    },
    mounted(){
        // Desordenar los productos cuando el componente se monta
        this.shuffleProducts();
        // console.log(this.shuffledProducts); // Mostrar los productos desordenados
    },
    watch: {
        products: {
            handler(newProducts) {
                // Si los productos cambian, desordenarlos
                this.shuffleProducts();
            },
            immediate: true // Llamar al handler inmediatamente si hay productos
        }
    },
    methods: {
        shuffleProducts(){
            // Crear una copia del array de productos y desordenarlo aleatoriamente
            this.shuffledProducts = [...this.products].sort(() => Math.random() - 0.5);
        },
        showMore(){
            this.visibleProducts += 6;
        }
    }
};
</script>

<style lang="scss" scoped>
.products-grid {
    &-bar {
        display: flex;
        justify-content: space-between;

        &-actions {
            display: flex;
            gap: 1rem;

            @media (min-width: 600px) {
                .v-btn {
                    min-width: 164px;
                }
            }
        }
    }

    .row--dense > .col,
    .row--dense > [class*="col-"] {
        padding: 5px;
    }
}
</style>
