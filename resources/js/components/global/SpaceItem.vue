<template>
    <router-link :to="data.to || { name: data.routeName }" class="space-item py-4 text-none d-block">
        <v-img
            class="space-item-image mb-4"
            :src="data.img || '/public/assets/img/item-placeholder.png'"
            aspect-ratio="1"
        />
        <h3 class="space-item-title">
            {{ data.title }}
        </h3>
    </router-link>
</template>

<script>
import CustomButton from "./CustomButton.vue";

export default {
    name: "ProductItem",
    props: {
        data: {
            type: Object,
            default: () => ({
                id: "0",
                name: "ESPACIO",
                img: "/public/assets/img/item-placeholder.png"
            })
        }
    },
    computed: {
        aspectRatio() {
            switch (this.$vuetify.breakpoint.name) {
                case "xs":
                    return "1.2";
                case "sm":
                    return "1.9";
                case "md":
                    return "2.2";
                case "lg":
                    return "2.2";
                case "xl":
                    return "0.8";
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.space-item {
    &-image {
        /* Recorte circular: la imagen trae el fondo del lienzo pegado */
        border-radius: 50%;
        overflow: hidden;
        max-width: 190px;
        margin-left: auto;
        margin-right: auto;
    }

    &-title {
        /* 35px fijos partian nombres largos como "Seguridad industrial" */
        font-size: clamp(16px, 1.8vw, 26px);
        line-height: 1.25;
        font-weight: 600;
        text-align: center;
        margin: 0;
        overflow-wrap: anywhere;
    }
}
</style>
