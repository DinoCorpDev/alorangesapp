<template>
    <ShopShowApi category="Cartoneria" :banner="categoryImage"/>
</template>

<script>
import ShopShowApi from "./ShopShowApi.vue";

export default {
    name: "ShopCartoneria",
    data() {
        return {
            categoryData: {}
        };
    },
    components: {
        ShopShowApi
    },
    computed: {
        categoryImage() {
            return this.categoryData.banner || this.categoryData.meta_image || "";
        }
    },
    methods: {
        async getCategories() {
            try {
                const res = await this.call_api(
                    "get",
                    "category/by-name/Cartoneria"
                );

                if (res.data.success && res.data.data.length) {
                    this.categoryData = res.data.data[0];
                }
            } catch (error) {
                console.error(error);
            }
        }
    },

    mounted() {
        this.getCategories();
    }
};
</script>
