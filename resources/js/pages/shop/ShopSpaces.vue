<template>
    <v-container fluid>
        <contact-dialog :show="contactDialogShow" @close="contactDialogClosed" />

        <v-row tag="main" class="main">
            <v-col cols="12">
                <Carousel
                    class="main-carousel"
                    :slides="sliderItems"
                    type="description"
                    title="Diseño de espacios"
                    description="Desde el diseño hasta la instalación, nos encargamos de todo para que te sientas confortable en ese espacio habitable ideal."
                />
            </v-col>
        </v-row>

        <v-row tag="section">
            <v-col cols="12" md="6">
                <ShopActionCard img="/public/assets/img/shop-spaces/icon-msg.png" @click="contactDialogOpen()" />
            </v-col>
            <v-col cols="12" md="6">
                <ShopActionCard img="/public/assets/img/shop-spaces/icon-house.png" @click="contactDialogOpen()" />
            </v-col>
        </v-row>

        <v-row tag="section">
            <v-col cols="12">
                <CarouselPortfolio :items="itemsPortfolio" />
            </v-col>
        </v-row>

        <v-row tag="section" class="mb-6">
            <v-col cols="12">
                <h5>H5</h5>
                <v-row class="mb-3">
                    <v-col
                        v-for="product in productsSeeder.slice(0, 4)"
                        :key="`product-col-${product.id}`"
                        cols="12"
                        sm="6"
                        md="3"
                    >
                        <ProductBox boxStyle="two" :productDetails="product" />
                    </v-col>
                </v-row>
                <v-row justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <CustomButton block color="black" text="Ver más" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row tag="section" class="mb-6">
            <v-col cols="12">
                <CarouselActions :items="itemsActions" />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { spacesSeeder } from "../../seeders/spaces";
import { productsSeeder, sliderSeeder } from "../../seeders/products";

import Carousel from "../../components/global/Carousel";
import CarouselActions from "../../components/global/CarouselActions.vue";
import CarouselPortfolio from "../../components/global/CarouselPortfolio.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import ProductBox from "../../components/product/ProductBox.vue";
import ShopActionCard from "../../components/shop/ShopActionCard.vue";
import ContactDialog from "../../pages/shop/ContactDialog.vue";

export default {
    name: "ShopSpaces",
    data: () => ({
        spacesSeeder,
        productsSeeder,
        sliderSeeder,
        sliderItems: [
            {
                src: "/public/assets/img/shop-spaces/banner-main.png",
                type: "image"
            }
        ],
        itemsPortfolio: [
            {
                title: "Cocina en Linea",
                img: "/public/assets/img/shop-spaces/cocinalinea.png",
                icon: "/public/assets/img/home/portfolio-design-icon.svg",
                description: "Proyecta tus espacias a la mediada que deseas."
            },
            {
                title: "Cocina en U",
                img: "/public/assets/img/shop-spaces/cocinau.png",
                icon: "/public/assets/img/home/portfolio-services-icon.svg",
                description: "Contrata a profesionales para la instalación y recalibración de tus espacios habitables."
            },
            {
                title: "ESP",
                icon: "/public/assets/img/home/portfolio-esp-icon.svg",
                description:
                    "Explora un catalogo de marcas aliadas y descubre el electrodoméstico que buscas para dar funcionalidad a tu hogar."
            }
        ],
        itemsActions: [
            { img: "/public/assets/img/shop-spaces/action-a.png" },
            { img: "/public/assets/img/shop-spaces/action-b.png" },
            { img: "/public/assets/img/shop-spaces/action-c.png" }
        ],
        contactDialogShow: false
    }),
    components: {
        Carousel,
        CarouselActions,
        CarouselPortfolio,
        CustomButton,
        ProductBox,
        ShopActionCard,
        ContactDialog
    },
    methods: {
        contactDialogOpen() {
            this.contactDialogShow = true;
        },
        contactDialogClosed() {
            this.contactDialogShow = false;
        }
    }
};
</script>

<style lang="scss" scoped>
.main {
    &-carousel {
        height: 77vh !important;
        max-height: 786px;

        &::v-deep {
            .v-carousel__item {
                height: 100%;
            }
        }
    }
}
</style>
