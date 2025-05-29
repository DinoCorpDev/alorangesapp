<template>
    <v-container class="pt-0" fluid>
        <v-row
            class="banner-principal"
            style="
                background-image: url('/public/assets/img/BannerShop.jpg');
                background-size: cover;
                background-repeat: no-repeat;
                margin: 10px 0;
            "
        >
            <v-col cols="6" class="d-flex justify-center align-center">
                <div>
                    <!-- <h2 style="font-size: 85px; font-weight: 800; line-height: 30px">AZ</h2>
                    <h4 style="font-size: 35px; font-weight: 800; line-height: 97.52px">FABRIFOLDER</h4>
                    <h3 style="font-size: 37px; font-weight: 500; line-height: 40px">
                        Tenemos lo que necesitas <br />de la <b>A</b> a la <b>Z</b>
                    </h3> -->
                </div>
            </v-col>
            <v-col cols="6" class="d-flex justify-center align-center">
                <!-- <img src="/public/assets/img/Fabrifolder.png" style="height: 350px; width: auto" /> -->
            </v-col>
        </v-row>
        <v-container class="bg-surface-variant ma-0 px-0 pb-8 pt-0">
            <div align="start" class="d-flex" no-gutters>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <CustomButton
                            icon="las la-redo-alt"
                            color="orange-cart"
                            type="button"
                            class="mt-4 mr-2"
                            width="45"
                            height="54"
                            @click="getProducts"
                            v-bind="attrs"
                            v-on="on"
                        />
                    </template>
                    <span>Refrescar</span>
                </v-tooltip>
                <div class="container-buttons">
                    <div v-for="filtro in resultadoFiltroBotones" :key="`button-${filtro.id}`">
                        <CustomButton
                            :text="filtro.text"
                            :color="activeButton === filtro.id ? 'orange-cart2' : 'nero3'"
                            type="button"
                            class="mt-4 mr-3"
                            width="45"
                            height="54"
                            @click="setActiveButton(filtro.id, filtro.text)"
                        />
                    </div>
                </div>
            </div>
        </v-container>
        <v-row tag="section" class="mb-6">
            <v-col cols="12" v-for="(product, key) in productsSeeder" :key="product.id">
                <v-row class="mb-3">
                    <v-col cols="12" sm="12" md="12">
                        <div v-if="isList">
                            <v-row>
                                <v-col v-for="item in product" :key="`list-product-${item.id}`" cols="12" sm="4" md="2">
                                    <ProductBox boxStyle="two" :productDetails="item" />
                                </v-col>
                            </v-row>
                        </div>
                        <div v-else>
                            <CarouselSwiper
                                class="carousel-products"
                                :title="key + ' ' + product.length + ' Resultados'"
                                :options="swiperOptions"
                            >
                                <!-- <swiper
                                :slides-per-view="4"    
                                :space-between="30"     
                                :loop="true"            
                                :pagination="{ clickable: true }" 
                                :navigation="true"   
                            > -->
                                <swiper-slide v-for="item in product" :key="`carousel-products-slide-${item.id}`">
                                    <ProductBox boxStyle="two" :productDetails="item" />
                                </swiper-slide>
                                <!-- </swiper> -->
                            </CarouselSwiper>
                        </div>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Carousel from "../../components/global/Carousel";
import CarouselActions from "../../components/global/CarouselActions.vue";
import CarouselPortfolio from "../../components/global/CarouselPortfolio.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import CarouselSwiper from "../../components/global/CarouselSwiper.vue";
import ProductBox from "../../components/product/ProductBox.vue";
import ShopActionCard from "../../components/shop/ShopActionCard.vue";
import ContactDialog from "../../pages/shop/ContactDialog.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import Mixin from "../../utils/mixin";

export default {
    name: "ShopShowApi",
    data: () => ({
        productsSeeder: [],
        resultadoFiltroBotones: [],
        activeButton: null,
        swiperOptions: {
            slidesPerView: 2,
            centeredSlides: false,
            spaceBetween: 12,
            breakpoints: {
                600: {
                    slidesPerView: 4
                },
                960: {
                    slidesPerView: 6,
                    spaceBetween: 20
                }
            }
        },
        isList:false,
    }),
    props: {
        category: { type: String, default: "" }
    },
    components: {
        CustomInput,
        Carousel,
        CarouselActions,
        CarouselPortfolio,
        CustomButton,
        ProductBox,
        ShopActionCard,
        ContactDialog,
        CarouselSwiper
    },
    mounted() {
        this.getProducts();
        this.updateBreadcrumb();
    },
    methods: {
        async getProducts() {
            const res = await Mixin.methods.call_api("get", `product/search?category_slug=${this.category}`);
            if (res.data.success) {
                this.isList = false;
                // category_slug;
                // keyword;
                // sort_by;
                // brand_ids;
                // min_price;
                // max_price;
                this.productsSeeder = res.data.products.data;
                const primerasLetras = this.productsSeeder.map(item => item.name.charAt(0));
                primerasLetras.sort();
                let letrasFiltro = [...new Set(primerasLetras)];

                this.resultadoFiltroBotones = letrasFiltro.map((letra, index) => ({
                    id: index + 1,
                    text: letra
                }));

                let data = res.data.products.data;
                const clasificadosPorLetra = data.reduce((acc, product) => {
                    // Obtenemos la primera letra del nombre
                    const primeraLetra = product.name.charAt(0).toUpperCase();

                    // Si la letra no existe como clave en acc, inicializamos el array
                    if (!acc[primeraLetra]) {
                        acc[primeraLetra] = [];
                    }

                    // Agregamos el objeto al array correspondiente
                    acc[primeraLetra].push(product);
                    return acc;
                }, {});

                // this.productsSeeder = res.data.products.data.slice(0, 12);

                this.productsSeeder = clasificadosPorLetra;
                this.activeButton = null;
            }
        },
        updateBreadcrumb() {
            const newItems = [
                { text: "Home", href: "/", disabled: false },
                { text: "Tienda", href: "/", disabled: true }
            ];
            this.$store.dispatch("breadcrumb/setBreadcrumbItems", newItems);
        },

        async filter(value) {
            const res = await Mixin.methods.call_api(
                "get",
                `product/search?category_slug=${this.category}&&keyword=${value}`
            );

            let data = res.data.products.data;
            const clasificadosPorLetra = data.reduce((acc, product) => {
                const primeraLetra = product.name.charAt(0).toUpperCase();
                if (!acc[primeraLetra]) {
                    acc[primeraLetra] = [];
                }

                acc[primeraLetra].push(product);
                return acc;
            }, {});
            if (res.data.success) {
                this.productsSeeder = clasificadosPorLetra;
            }
        },
        setActiveButton(id, value) {
            this.isList = true;
            this.activeButton = id;
            this.filter(value);
        }
    }
};
</script>
<style scoped lang="scss">
.container-buttons {
    display: flex;
    overflow-x: auto;
    white-space: nowrap;
    width: 100%;
    &::-webkit-scrollbar {
        height: 0px;
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #b5b5b5;
        border-radius: 1px;
    }

    /* Handle */
    &::-webkit-scrollbar-thumb {
        background: rgba(#5a5a5a, 0.8);
        border-radius: 1px;
    }

    /* Handle on hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #5a5a5a;
    }
}
.banner-principal {
    height: 400px;
    @media (max-width: 600px) 
    {
        height: 130px;
    }
}
</style>
