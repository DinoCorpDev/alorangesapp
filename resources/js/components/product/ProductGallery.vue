<template>
    <div class="product-gallery" v-show="!isLoading">
                <Carousel
                    :slides="galleryImages"
                    :showArrows="galleryImages.length > 1"
                    :hideDelimiters="galleryImages.length <= 1"
                />
    </div>
</template>

<script>
import Carousel from "../../components/global/Carousel.vue";

export default {
    props: {
        isLoading: { type: Boolean, default: true },
        galleryImages: { type: Array, required: true, default: () => [] },
        galleryVideos: { type: Array, required: true, default: () => [] },
        dataSheet: { type: String, required: true, default: "" }
    },
    data: () => ({
        currentTab: 0
    }),
    components: {
        Carousel
    }
};
</script>

<style lang="scss" scoped>
// Se eliminaron ~60 lineas de CSS de `.v-tabs`: el template solo renderiza
// <Carousel>, no hay ningun v-tabs, asi que nada de aquello se aplicaba.
.product-gallery {
    position: relative;

    // Antes: `height: 100%` seguido de `height: 82vh` (ganaba el segundo).
    // En movil la galeria se comia el 82% de la pantalla y empujaba precio,
    // cantidad y "Agregar a Compras" por debajo del pliegue.
    //
    // La altura debe seguir siendo DEFINIDA (no `auto`): el <v-carousel>
    // interno usa height="100%" y con un padre auto colapsaria. Con min() se
    // mantiene definida pero escala con el ancho del viewport y queda acotada.
    height: min(90vw, 360px);

    @include respond-up("sm") {
        height: min(55vw, 440px);
    }

    @include respond-up("md") {
        height: min(38vw, 520px);
    }

    &::v-deep .carousel-item-image {
        width: 100%;
        height: 100%;
        // `cover` recortaba la foto del producto. En una ficha hay que ver el
        // articulo completo. Se limita a la galeria: el mismo Carousel se
        // reutiliza en banners, donde `cover` si es lo correcto.
        object-fit: contain;
    }
}
</style>
