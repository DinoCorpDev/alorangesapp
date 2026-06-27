<template>
    <v-container style="background-color: #fafcfc">
        <v-row class="main ma-6 bg-orange border-style-banner mb-10" align="center" tag="main">
            <v-carousel
                cycle
                height="300"
                hide-delimiter-background
                interval="4000"
                style="border-radius: 10px"
                :show-arrows="true"
                show-arrows-on-hover
            >
                <template v-slot:prev="{ on, attrs }">
                    <v-btn v-bind="attrs" v-on="on"
                        ><svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            role="img"
                            aria-hidden="true"
                            class="v-icon__svg"
                            style="font-size: 36px; height: 36px; width: 36px"
                        >
                            <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg
                    ></v-btn>
                </template>
                <template v-slot:next="{ on, attrs }">
                    <v-btn v-bind="attrs" v-on="on"
                        ><svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            role="img"
                            aria-hidden="true"
                            class="v-icon__svg"
                            style="font-size: 3px; height: 36px; width: 36px"
                        >
                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path></svg
                    ></v-btn>
                </template>
                <v-carousel-item
                    v-for="(item, i) in items"
                    :key="i"
                    :src="item.src"
                    reverse-transition="fade-transition"
                    transition="fade-transition"
                    eager
                    active-class="carousel-img"
                ></v-carousel-item>
            </v-carousel>
        </v-row>

        <v-row tag="section" class="preambulo mb-6">
            <v-col cols="12">
                <div class="rounded-section px-5">
                    <v-row>
                        <v-col cols="12" lg="12">
                            <v-row
                                :no-gutters="
                                    $vuetify.breakpoint.name == 'md' || $vuetify.breakpoint.name == 'lg' ? false : true
                                "
                            >
                                <v-col class="center-flex-start" cols="6" sm="6" md="3">
                                    <v-img src="/public/assets/img/Shipping.png" contain max-width="120" class="mb-4" />
                                    <h4>Domicilio gratis</h4>
                                    <p>En compras mínimas</p>
                                </v-col>
                                <v-col class="center-flex-center" cols="6" sm="6" md="3">
                                    <v-img src="/public/assets/img/Group13.png" contain max-width="120" class="mb-4" />
                                    <h4>Apoyo 24/7</h4>
                                    <p>Servicio técnico</p>
                                </v-col>
                                <v-col class="center-flex-center" cols="6" sm="6" md="3">
                                    <v-img src="/public/assets/img/Group12.png" contain max-width="120" class="mb-4" />
                                    <h4>Pago seguro</h4>
                                    <p>Múltiples bancos</p>
                                </v-col>
                                <v-col class="center-flex-end" cols="6" sm="6" md="3">
                                    <v-img src="/public/assets/img/Refresh.png" contain max-width="120" class="mb-4" />
                                    <h4>Devoluciones</h4>
                                    <p>Cambios de productos</p>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </div>
            </v-col>
        </v-row>

        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" class="v-icon__svg" style="font-size: 3px; height: 36px; width: 36px;"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path></svg> -->

        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" class="v-icon__svg" style="font-size: 36px; height: 36px; width: 36px;"><path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg> -->
        <v-row class="ma-10">
            <v-col cols="12" sm="6" class="pr-6">
                
                <a href="#" class="categories-items"
                    >
                    <div class="top-product"><v-img src="/public/assets/img/papeleria.png" contain max-width="350" /></div>
                    <div class="bottom-product">
                        <h4 class="pl-5"><strong>PAPELERÍA</strong></h4>
                    </div></a
                >
            </v-col>

            <v-col cols="12" sm="6" class="pl-6">
                
                <a href="#" class="categories-items"
                    >
                    <div class="top-product"><v-img src="/public/assets/img/aseo.png" contain max-width="350" /></div>
                    <div class="bottom-product">
                        <h4 class="pl-5"><strong>ASEO</strong></h4>
                    </div></a
                >
            </v-col>

            <v-col cols="12" sm="6" class="pr-6 pt-12">
                
                <a href="#" class="categories-items"
                    >
                    <div class="top-product"><v-img src="/public/assets/img/cafeteria.png" contain max-width="350" /></div>
                    <div class="bottom-product">
                        <h4><strong class="pl-5">CAFETERÍA</strong></h4>
                    </div></a
                >
            </v-col>

            <v-col cols="12" sm="6" class="pl-6 pt-12">
                
                <a href="#" class="categories-items"
                    >
                    <div class="top-product"><v-img src="/public/assets/img/botiquin.png" contain max-width="350" /></div>
                    <div class="bottom-product">
                        <h4 class="pl-5"><strong>BOTIQUÍN</strong></h4>
                    </div></a
                >
            </v-col>
        </v-row>
        <v-row>
            <v-col col="12" sm="12" md="12">
                <CarouselBrands></CarouselBrands>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { sliderSeeder } from "../seeders/products";

// import Carousel from "../components/global/Carousel.vue";
import CustomButton from "../components/global/CustomButton.vue";
import LayoutNavbar from "../components/header/Navbar.vue";
import CustomInput from "../components/global/CustomInput.vue";
import CarouselBrands from "../components/global/CarouselBrands.vue";

export default {
    components: {
        // Carousel,
        CustomButton,
        LayoutNavbar,
        CustomInput,
        CarouselBrands
    },
    data() {
        return {
            items: [
                {
                    src: "/public/assets/img/fondohome.png"
                },
                {
                    src: "/public/assets/img/fondohome.png"
                },
                {
                    src: "/public/assets/img/fondohome.png"
                },
                {
                    src: "/public/assets/img/fondohome.png"
                }
            ],

            selectedCode: null,
            sliderSeeder,
            sliderItems: [
                {
                    src: "/public/assets/img/home/banner-home.png",
                    type: "image"
                }
            ]
        };
    },
    computed: {
        ...mapGetters("app", ["userLanguageObj", "allLanguages"]),
    },
    mounted() {
        this.$vuetify.theme.dark = true;

        this.selectedCode = this.userLanguageObj.code;

        this.scrollToCenter();
        this.updateBreadcrumb();
    },

    methods: {
        ...mapActions("app", ["setLanguage"]),
        updateBreadcrumb() {
            const newItems = [
                { text: 'Home', href: '/', disabled: true },
            ];
            this.$store.dispatch('breadcrumb/setBreadcrumbItems', newItems);
        },
        agree() {
            if (this.$i18n.locale !== this.selectedCode) {
                this.setLanguage(this.selectedCode);
                window.location.reload();
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.home {
    &-main {
        &-description {
            font-size: var(--font-size-h5);
            color: black;
            font-weight: 100;
        }

        &-carousel {
            height: 80vh !important;
            max-height: 786px;

            &::v-deep {
                .v-carousel__item {
                    height: 100%;
                }
            }
        }
    }

    &-portfolio {
        &-wrap {
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 1rem;
            height: 100%;
            min-height: 560px;
            display: flex;
            align-items: center;
        }

        &-title {
            @media (max-width: 600px) {
                width: 60%;
            }
        }
    }
}

.temp {
    padding: 0 15%;
}

.theme--dark {
    .preambulo .rounded-section {
        background-color: #18191a;
    }
}

.v-select {
    text-transform: uppercase;
}

.border-style-banner {
    border-radius: 10px;
}

.bg-orange {
    background: #f58634;
}

.bg-green-ligth {
    background: #f3f9ec;
}
.center-flex-start {
    display: flex;
    flex-direction: column;
    align-items: center;
    // padding: 0 50px 0 50px;
    padding-left: 0 !important;
}

.center-flex-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    // padding: 0 50px 0 50px;
}

.center-flex-end {
    display: flex;
    flex-direction: column;
    align-items: center;
    // padding: 0 50px 0 50px;
    padding-right: 0 !important;
}
@media (min-width: 835px) {
    .container {
        max-width: 1920px;
    }
}

.div-map {
    width: 100%;
}

.map {
    border: 1px solid #59595a;
    border-radius: 400px;
    padding: 2% 15%;
}

.img-map {
    border-radius: 50%;
}

@media (max-width: 834px) {
    .div-map {
        width: 100%;
    }

    .map {
        border: 1px solid #59595a;
        border-radius: 50%;
        padding: 20% 0;
    }
}

.v-btn {
    width: 5px !important;
    border-radius: 100px;
    opacity: 0.7;
    &::v-deep {
        .v-btn__content {
            height: 51px !important    ;
        }
    }

    &.v-size--default {
        height: 51px !important;
    }
}

.v-input {
    border-radius: 500px !important;

    &::v-deep {
        .v-input__control,
        .v-input__slot {
            height: 51px !important;
        }
    }

    &:focus {
        .lupa {
            display: none;
        }
    }
}

.theme--light {
    .v-input {
        &::v-deep {
            .v-input__slot {
                background: #ffffff;

                &:hover {
                    background: #dfdfdf;
                }
            }
        }
    }
}

.boton-descarga {
    width: 250px !important;

    &::v-deep {
        .v-btn__content {
            padding-right: 50px !important;
        }
    }
}

.top-product {
    background-color: #c3e09c;
    display: flex;
    place-items: center;
    justify-content: center;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}
.bottom-product {
    background-color: #f5f5f5;
    height: 100px;
    display: flex;
    place-items: center;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    transition: ease 0.2s;
}
.categories-items:hover{
    & .bottom-product{
        background-color: #f58634;
        color: white;
        cursor: pointer;
    }
}

.carousel-img {
    width: 100%;
    display: flex;
    place-self: center;
    justify-content: center;
}
</style>
