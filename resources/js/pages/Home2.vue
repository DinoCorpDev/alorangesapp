<template>
    <v-container style="background-color: white">
        <v-row class="main mx-2 my-2 mx-sm-6 bg-orange-ligth border-style-banner" align="center" tag="main">
            <v-col class="pa-6 pa-sm-8" cols="12" sm="7" md="7">
                <h1 class="home-main-title mb-8 mb-sm-8">
                    ¡Tu proveedor de suministros de papelería, aseo, cafetería, cartonería, tecnología, seguridad industrial y mucho mas para tu empresa!
                </h1>
                <!-- <h1 class="home-main-title mb-6 mb-sm-8">¡Todo en un solo lugar, rápido, fácil y seguro!</h1> -->
                <!-- <p class="home-main-description mb-6 d-none d-sm-block">
                    Tu proveedor de suministros favoritos de <b>P</b>apelería, <b>A</b>seo, <b>C</b>afetería, <b>B</b>otiquín, <b>T</b>ecnología,
                    <b>C</b>artonería, <b>S</b>eguridad industrial y mucho más está a solo un clic de distancia. </br>Estamos aquí para
                        hacer tu vida más fácil y divertida.</br><b>¡Explora y disfruta!</b>
                </p> -->
                <SearchInput class="search-menu" :showInput="true" :placeholder="'Escribe lo que buscas'" />
            </v-col>
            <v-col class="pa-0 d-flex" cols="12" sm="5" md="5">
                <img src="/public/assets/img/bannerhomeimg-removebg-preview3.png" style="width: 100%; height: auto" />
            </v-col>
        </v-row>

        <v-row tag="section" class="preambulo mb-6">
            <v-col cols="12" class="px-0">
                <div class="rounded-section px-5 py-8">
                    <v-row justify="center">
                        <v-col cols="12" lg="12" class="px-0 px-sm-3">
                            <v-row
                                :no-gutters="
                                    $vuetify.breakpoint.name == 'md' || $vuetify.breakpoint.name == 'lg' ? false : true
                                "
                            >
                                <v-col class="center-flex" cols="3">
                                    <v-img
                                        src="/public/assets/img/Group13.png"
                                        style="width: 60%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3 style="text-align: center">Domicilios gratis</h3>
                                    <p class="preambulo-text" style="text-align: center">En compras mínimas</p>
                                </v-col>
                                <v-col class="center-flex" cols="3">
                                    <v-img
                                        src="/public/assets/img/Group14.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Apoyo 24/7</h3>
                                    <p class="preambulo-text">Servicio técnico</p>
                                </v-col>
                                <v-col class="center-flex" cols="3">
                                    <v-img
                                        src="/public/assets/img/Group12.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Pago seguro</h3>
                                    <p class="preambulo-text">Múltiples bancos</p>
                                </v-col>
                                <v-col class="center-flex" cols="3">
                                    <v-img
                                        src="/public/assets/img/Refresh.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Devoluciones</h3>
                                    <p class="preambulo-text" style="text-align: center">Cambios de productos</p>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </div>
            </v-col>
        </v-row>
        <v-row class="center-items ma-2 ma-sm-6">
            <v-col col="12" sm="12" md="12" class="bg-green-ligth border-style-banner">
                <CarouselSpaces
                    title="Categorias"
                    img="/public/assets/img/home/icon-be-there.svg"
                    :spaces="itemsArray"
                />
            </v-col>
        </v-row>

        <v-row class="mb-6 mt-5">
            <v-col cols="12" class="d-flex justify-center">
                <PresentationBanner
                    id="be-there"
                    icon="/public/assets/img/home/icon-be-there.svg"
                    preamble="Escuchar, Comprender y Percibir al Usuario"
                    title="¡Facilitamos tus procesos con un toque de tecnología!"
                    image="/public/assets/img/banner2home.png"
                    style="max-width: 99%"
                >
                    <template v-slot:description>
                        En <b>Aloranges,</b> nos dedicamos a hacerte la vida más fácil. Somos expertos en satisfacer las
                        necesidades de tu empresa con <b>suministros de papelería, cartonería, cafetería, aseo, tecnología y
                        botiquín y mucho mas.</b>
                        ¡Y ahora, gracias a nuestra app, puedes adquirir todo lo que necesitas con solo un clic! Nos
                        adaptamos a la nueva era digital para que tus compras sean rápidas y sencillas.
                        ¡Únete a la diversión y simplifica tu día a día con Aloranges!
                        <br>
                        Somos tu proveedor de suministros favorito, siempre estamos a solo un clic de distancia. <br>Estamos aquí para
                        hacer tu vida más fácil y divertida.<br><b>¡Explora y disfruta!</b>
                    </template>
                </PresentationBanner>
            </v-col>
        </v-row>

        <div class="wrapper-app-banner mb-6 center-items" style="display: flex; justify-content: center">
            <div style="width: 95%; border-radius: 24px; background-color: #f4f9ec">
                <v-row>
                    <v-col cols="12" class="d-flex d-sm-none justify-start">
                        <div class="ml-7 mt-7 mt-sm-0 d-flex flex-column justify-start align-start">
                            <h2 class="banner-title font-weight-bold mb-3">Descarga Nuestra <br />App Móvil</h2>
                            <CustomButton class="mb-5" color="orange" :width="200">Descargar App</CustomButton>
                        </div>
                    </v-col>
                    <v-col cols="12" sm="6" class="d-flex align-items-center" style="justify-content: center">
                        <img class="imgsize-cel" src="/public/assets/img/download-img.svg" />
                    </v-col>
                    <v-col cols="12" sm="6" class="justify-center d-none d-sm-flex">
                        <div class="ml-7 mt-7 mt-sm-0 d-flex flex-column justify-center align-start">
                            <h2 class="banner-title font-weight-bold mb-3">Descarga Nuestra <br />App Móvil</h2>
                            <CustomButton class="mb-5" color="orange" :width="200">Descargar App</CustomButton>
                            <div class="Wrapper-AppStore">
                                <a href="#"><img class="imgsize-btn-dwn" src="public/assets/img/Grupo12233.png" /></a>
                                <a href="#"
                                    ><img class="imgsize-btn-dwn ml-5" src="public/assets/img/Grupo12234.png"
                                /></a>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" class="d-flex d-sm-none justify-center align-center">
                        <div class="Wrapper-AppStore-responsive mb-7 mb-sm-0">
                            <v-row>
                                <v-col cols="6" class="pr-1 pl-10 pb-0">
                                    <a href="#"
                                        ><img
                                            class="imgsize-btn-dwn"
                                            style="width: 100%; height: auto"
                                            src="public/assets/img/Grupo12233.png"
                                    /></a>
                                </v-col>
                                <v-col cols="6" class="pl-1 pr-10 pb-0">
                                    <a href="#"
                                        ><img
                                            class="imgsize-btn-dwn"
                                            style="width: 100%; height: auto"
                                            src="public/assets/img/Grupo12234.png"
                                    /></a>
                                </v-col>
                            </v-row>
                        </div>
                    </v-col>
                </v-row>
            </div>
        </div>

        <v-row>
            <v-col col="12" sm="12" md="12" class="py-14">
                <CarouselBrands></CarouselBrands>
            </v-col>
        </v-row>
        <RecuperarPassCodigo v-model="showRecuperarPass" :email="this.$route.query.email" />
        <VerifyAccount v-model="showVerifyAccount" />
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { sliderSeeder } from "../seeders/products";

import BannerCategoryProduct from "../components/global/BannerCategoryProduct.vue";
import Carousel from "../components/global/Carousel.vue";
import CarouselActions from "../components/global/CarouselActions.vue";
import CustomButton from "../components/global/CustomButton.vue";
import LayoutNavbar from "../components/header/Navbar.vue";
import PolygonElement from "../components/global/PolygonElement.vue";
import PortfolioCard from "../components/global/PortfolioCard.vue";
import PresentationBanner from "../components/global/PresentationBanner.vue";
import SelectCustom from "../components/global/SelectCustom.vue";
import CustomInput from "../components/global/CustomInput.vue";
import CarouselSpaces from "../components/global/CarouselSpaces.vue";
import CarouselBrands from "../components/global/CarouselBrands.vue";
import RecuperarPassCodigo from "../components/auth/RecuperarPassCodigo.vue";
import VerifyAccount from "../components/auth/VerifyAccount.vue";
import SearchInput from "../components/global/SearchInput.vue";

export default {
    components: {
        BannerCategoryProduct,
        Carousel,
        CarouselActions,
        CustomButton,
        LayoutNavbar,
        PolygonElement,
        PortfolioCard,
        PresentationBanner,
        SelectCustom,
        CustomInput,
        CarouselSpaces,
        CarouselBrands,
        RecuperarPassCodigo,
        VerifyAccount,
        SearchInput
    },
    data() {
        return {
            showRecuperarPass: false,
            showVerifyAccount: false,
            itemsArray: [
                {
                    title: "Papeleria",
                    img: "/public/assets/img/Grupo12238.png",
                    description: "Papeleria"
                },
                {
                    title: "Cartonería",
                    img: "/public/assets/img/Group 6.png",
                    description: "Cartoneria"
                },
                {
                    title: "Cafeteria",
                    img: "/public/assets/img/Grupo12240.png",
                    description: "Cafeteria"
                },
                {
                    title: "Aseo",
                    img: "/public/assets/img/Grupo12239.png",
                    description: "Aseo"
                },

                {
                    title: "Tecnología",
                    img: "/public/assets/img/Group 5.png",
                    description: "Tecnologia"
                },
                {
                    title: "Seguridad ind",
                    img: "/public/assets/img/Grupo12241.png",
                    description: "Seguridad ind"
                }
            ],
            selectedCode: null,
            sliderSeeder,
            sliderItems: [
                {
                    src: "/public/assets/img/home/banner-home.png",
                    type: "image"
                }
            ],
            productsSeeder: [
                {
                    id: "1",
                    name: "'I'",
                    img: "/public/assets/img/home/i.png",
                    description: "Racionalización de procesos de fabricación"
                },
                {
                    id: "2",
                    name: "'DOVELA'",
                    description: "Cuidadosa selección de materiales",
                    img: "/public/assets/img/home/dovela1.png"
                },
                {
                    id: "3",
                    name: "'INTEGRACIÓN'",
                    description: "Ciclo de vida adaptable",
                    img: "/public/assets/img/home/dovela.png"
                }
            ]
        };
    },
    computed: {
        ...mapGetters("app", ["userLanguageObj", "allLanguages"])
    },
    created() {
        if (this.$route.query.modal === "Password") {
            this.showRecuperarPass = true;
        }
        if (
            this.$route.query.modal == "VerifyAccount" ||
            this.$route.query.modal == "verifyaccount" ||
            this.$route.query.modal == "VerifyAccount"
        ) {
            this.showVerifyAccount = true;
        }
    },
    mounted() {
        this.$vuetify.theme.dark = true;

        this.selectedCode = this.userLanguageObj.code;

        this.scrollToCenter();
        this.updateBreadcrumb();
        // if(this.$route.query.modal == 'Password'){
        //     this.showRecuperarPass = true;
        // }
    },
    methods: {
        ...mapActions("app", ["setLanguage"]),
        agree() {
            if (this.$i18n.locale !== this.selectedCode) {
                this.setLanguage(this.selectedCode);
                window.location.reload();
            }
        },
        updateBreadcrumb() {
            const newItems = [{ text: "disabled", href: "/", disabled: true }];
            this.$store.dispatch("breadcrumb/setBreadcrumbItems", newItems);
        }
    }
};
</script>

<style lang="scss">
.search-menu {
    @media (max-width: 700px) {
        max-width: 100% !important;
    }

    @media (min-width: 700px) {
        max-width: 90% !important;
    }
}
.center-items {
    display: flex;
    flex-direction: column;
    align-content: center;
    align-items: center;
}
.wrapper-app-banner {
    // background-image: url("/public/assets/img/bg10400.png");
    background-position: center;
    background-size: contain;
    display: flex;
    flex-direction: row;
    align-items: center;
}
.imgsize-cel {
    width: 75%;
    height: auto;
}
.imgsize-btn-dwn {
    width: 200px;
    height: auto;
}
.title-banner-dwn {
    font-size: 25px;
    font-weight: 700;
}
</style>

<style lang="scss" scoped>
.home {
    &-main {
        &-description {
            color: black;
            font-weight: 400;
            font-size: 18px;
            line-height: 22px;
            @media (min-width: 600px) {
                font-size: 31px;
                line-height: 37px;
            }
        }
        &-title {
            font-size: 30px;
            line-height: 36px;
            font-weight: 600;
            @media (min-width: 600px) {
                line-height: 57px;
                font-size: 53px;
            }
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
h3 {
    font-size: 12px;
    font-weight: 700;
    @media (min-width: 600px) {
        font-size: 35px;
        font-weight: 500;
    }
}
.preambulo-text {
    font-size: 10px;
    font-weight: 400;
    @media (min-width: 600px) {
        font-size: 25px;
    }
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
    border-radius: 20px;
}

.bg-orange {
    background: #f58634;
}
.bg-orange-ligth {
    background: #f5cea6;
}
.bg-green-ligth {
    background: #f3f9ec;
}
.center-flex {
    display: flex;
    flex-direction: column;
    align-items: center;
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
.Wrapper-AppStore {
    display: flex;
}
.Wrapper-AppStore-responsive {
    display: flex;
    flex-direction: column;
}
.wrapper-dwn-app {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.banner-title {
    // font-family: 'Montserrat', Courier, monospace;
    font-size: 35px;
    @media (min-width: 600px) {
        font-size: 58px;
    }
}
</style>
