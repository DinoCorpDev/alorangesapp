<template>
    <ShopShowApi
        category="Aseo"
        :banner="categoryData.banner"
    />
</template>

<script>
import ShopShowApi from "./ShopShowApi.vue";

export default {
    name: "ShopAseo",

    components: {
        ShopShowApi
    },

    data() {
        return {
            categoryData: {}
        };
    },

    methods: {
        async getCategories() {
            try {
                const res = await this.call_api(
                    "get",
                    "category/by-name/Aseo"
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