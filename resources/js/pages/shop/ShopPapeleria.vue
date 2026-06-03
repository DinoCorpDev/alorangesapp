<template>
    <ShopShowApi category="Papeleria" :banner="categoryData.banner"/>
</template>

<script>
import ShopShowApi from "./ShopShowApi.vue";

export default {
    name: "ShopPapeleria",
    data() {
        return {
            categoryData: {}
        };
    },
    components: {
        ShopShowApi
    },
    methods: {
        async getCategories() {
            try {
                const res = await this.call_api(
                    "get",
                    "category/by-name/Papeleria"
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
