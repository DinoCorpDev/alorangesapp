<template>
    <div class="space-item py-4">
        <v-img
            class="space-item-image mb-4"
            :src="data.img || '/public/assets/img/item-placeholder.png'"
            aspect-ratio="1"
        />
        <h3 class="space-item-title">
            {{ data.title }}
        </h3>
    </div>
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
        border-radius: 100%;
    }

    &-title {
        font-size: 35px;
        text-align: center;
    }
}
</style>
