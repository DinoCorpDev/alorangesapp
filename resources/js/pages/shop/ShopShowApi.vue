<template>
    <v-container class="pt-0" fluid>
        <v-row
            class="banner-principal"
            :style="{
                backgroundImage: `url(${effectiveBanner})`,
                backgroundSize: 'cover',
                backgroundRepeat: 'no-repeat',
                margin: '10px 0'
            }"
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
                    <span>Cargar todos</span>
                </v-tooltip>
                <div class="container-buttons">
                    <div v-for="filtro in resultadoFiltroBotones" :key="`button-${filtro.id}`">
                        <CustomButton
                            :text="filtro.text"
                            :color="activeButton === filtro.text ? 'orange-cart2' : 'nero3'"
                            type="button"
                            class="mt-4 mr-3"
                            width="45"
                            height="54"
                            @click="setActiveButton(filtro.text)"
                        />
                    </div>
                </div>
            </div>
        </v-container>
        <div v-if="loading" class="products-loading">
            <v-progress-circular indeterminate color="#f58634" size="42" width="4" />
            <div class="products-loading__text">Cargando productos...</div>
            <v-row class="mt-4">
                <v-col v-for="item in 6" :key="`product-loader-${item}`" cols="6" sm="4" md="2">
                    <v-skeleton-loader type="image, article" />
                </v-col>
            </v-row>
        </div>
        <v-alert v-else-if="!hasProductGroups" class="mt-4" type="info" text>
            No hay productos disponibles en esta categoria.
        </v-alert>
        <v-row v-else tag="section" class="mb-6">
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
        productsSeeder: {},
        resultadoFiltroBotones: [],
        activeButton: null,
        categoryData: {},
        loading: true,
        productsRequestId: 0,
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
        category: { type: String, default: "" },
        banner: { type: String, default: "" }
    },
    computed:{
        effectiveBanner() {
            return this.banner || this.categoryData.banner || this.categoryData.meta_image || this.getBannerByCategory;
        },
        getBannerByCategory() {
            switch (this.category) {
                case "Papeleria":
                    return "/public/assets/img/BannerShop.jpg";
                case "Aseo":
                    return "/public/assets/img/BannerRopa.jpg";
                case "Cafeteria":
                    return "/public/assets/img/banner2home.jpg";
                case "Tecnologia":
                    return "/public/assets/img/banner2home.jpg";
                case "Cartoneria":
                    return "/public/assets/img/banner2home.jpg";
                case "Seguridad industrial":
                    return "/public/assets/img/banner2home.jpg";
                default:
                    return "/public/assets/img/BannerShop.jpg";
            }
        },
        hasProductGroups() {
            return Object.keys(this.productsSeeder).length > 0;
        }
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
        CarouselSwiper,
    },
    mounted() {
        this.getCategoryData();
        this.getInitialProducts();
        this.updateBreadcrumb();
    },
    watch: {
        category() {
            this.getCategoryData();
            this.getInitialProducts();
            this.updateBreadcrumb();
        }
    },
    methods: {
        async getCategoryData() {
            if (!this.category) {
                this.categoryData = {};
                return;
            }

            try {
                const res = await Mixin.methods.call_api("get", `category/by-name/${encodeURIComponent(this.category)}`);
                if (res.data.success && res.data.data.length) {
                    this.categoryData = res.data.data[0];
                } else {
                    this.categoryData = {};
                }
            } catch (error) {
                console.error(error);
                this.categoryData = {};
            }
        },
        async getProducts() {
            const requestId = ++this.productsRequestId;
            this.loading = true;

            try {
                const res = await this.fetchCategoryProducts({ all: true });

                if (requestId !== this.productsRequestId) {
                    return;
                }

                if (res.data.success) {
                    this.isList = true;
                    this.resultadoFiltroBotones = res.data.letters || [];
                    this.setProducts(res.data.products.data || []);
                    this.activeButton = null;
                }
            } catch (error) {
                console.error(error);
                this.productsSeeder = {};
                this.resultadoFiltroBotones = [];
            } finally {
                if (requestId === this.productsRequestId) {
                    this.loading = false;
                }
            }
        },
        async getInitialProducts() {
            const requestId = ++this.productsRequestId;
            this.loading = true;

            try {
                const res = await this.fetchCategoryProducts({ all: true });

                if (requestId !== this.productsRequestId) {
                    return;
                }

                if (res.data.success) {
                    this.isList = false;
                    this.resultadoFiltroBotones = res.data.letters || [];
                    this.activeButton = null;
                    this.setProducts(res.data.products.data || []);
                }
            } catch (error) {
                console.error(error);
                this.productsSeeder = {};
                this.resultadoFiltroBotones = [];
            } finally {
                if (requestId === this.productsRequestId) {
                    this.loading = false;
                }
            }
        },
        fetchCategoryProducts({ letter = null, all = false } = {}) {
            const params = new URLSearchParams({
                mode: "shop_category",
                category_slug: this.category || ""
            });

            if (letter) {
                params.append("letter", letter);
            }

            if (all) {
                params.append("all", "1");
            }

            return Mixin.methods.call_api("get", `product/search?${params.toString()}`);
        },
        setProducts(products) {
            this.productsSeeder = products.reduce((acc, product) => {
                const primeraLetra = product.name.charAt(0).toUpperCase();

                if (!acc[primeraLetra]) {
                    acc[primeraLetra] = [];
                }

                acc[primeraLetra].push(product);
                return acc;
            }, {});
        },
        updateBreadcrumb() {
            const newItems = [
                { text: "Home", href: "/", disabled: false },
                { text: "Tienda", href: "/shop", disabled: false },
                { text: this.category, href: "", disabled: true }
            ];
            this.$store.dispatch("breadcrumb/setBreadcrumbItems", newItems);
        },

        async filter(value) {
            const requestId = ++this.productsRequestId;
            this.loading = true;

            try {
                const res = await this.fetchCategoryProducts({ letter: value });

                if (requestId !== this.productsRequestId) {
                    return;
                }

                if (res.data.success) {
                    this.resultadoFiltroBotones = res.data.letters || this.resultadoFiltroBotones;
                    this.activeButton = res.data.activeLetter || value;
                    this.setProducts(res.data.products.data || []);
                }
            } catch (error) {
                console.error(error);
                this.productsSeeder = {};
            } finally {
                if (requestId === this.productsRequestId) {
                    this.loading = false;
                }
            }
        },
        setActiveButton(value) {
            this.isList = true;
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

.products-loading {
    padding: 36px 0 24px;
    text-align: center;
}

.products-loading__text {
    color: #707780;
    font-size: 16px;
    font-weight: 500;
    margin-top: 12px;
}
</style>
