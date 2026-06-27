<template>
    <v-container fluid class="product-details pa-4">
        <v-row tag="section" class="flex-column-reverse flex-lg-row mb-6">
            <v-col cols="12">
                <AddToCart
                    :is-loading="detailsLoading"
                    :product-details="collectionDetails"
                    :products="products"
                    type-detail="collection"
                />
            </v-col>
        </v-row>

        <ProductsGrid class="mb-6" :products="moreProducts" filtrable cols="6" sm="4" md="3" lg="2" />

        <ProductsGrid class="mb-6" :products="footerProducts" productStyle="two" filtrable cols="12" sm="6" md="3" />
    </v-container>
</template>

<script>
import AddToCart from "../components/product/AddToCart.vue";
import ProductsGrid from "../components/global/ProductsGrid.vue";

export default {
    metaInfo() {
        return {
            title: this.metaTitle
        };
    },
    data: () => ({
        metaTitle: "",
        detailsLoading: true,
        collectionDetails: {},
        moreProducts: [],
        footerProducts: [],
        relatedProducts: [{}, {}, {}, {}, {}],
        products: []
    }),
    components: {
        AddToCart,
        ProductsGrid
    },
    methods: {
        async getDetails() {
            const res = await this.call_api("get", `collection/details/${this.$route.params.slug}`);

            if (res.data.success) {
                this.collectionDetails = res.data.data;
                this.getMoreProducts(this.collectionDetails?.id);
                this.getMoreProducts2(this.collectionDetails?.id);
                this.getRelatedProducts(this.collectionDetails.id);
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });

                this.$router.push({ name: "404" });
            }

            this.detailsLoading = false;
        },
        async getRelatedProducts(id) {
            const res = await this.call_api("get", `product/related/${id}`);
            if (res.data.success) {
                this.relatedProducts = res.data.data;
                this.relatedLoading = false;
            }
        },
        async getMoreProducts(id) {
            const res = await this.call_api("get", `product/random/6/${id}`);
            if (res.data.success) {
                res.data?.data?.map(product => {
                    this.moreProducts.push({
                        id: product?.id,
                        name: product?.name,
                        val: product?.base_price + " " + product?.currency,
                        img: product?.thumbnail_image,
                        description: product?.description,
                        brand: product?.brandName,
                        ref: product?.reference,
                        slug: product?.slug
                    });
                });
            }
        },
        async getMoreProducts2(id) {
            const res = await this.call_api("get", `product/random/4/${id}`);
            if (res.data.success) {
                res.data?.data?.map(product => {
                    this.footerProducts.push({
                        id: product?.id,
                        name: product?.name,
                        val: product?.base_price + " " + product?.currency,
                        img: product?.thumbnail_image,
                        description: product?.description,
                        brand: product?.brandName,
                        ref: product?.reference,
                        slug: product?.slug
                    });
                });
            }
        }
    },
    async created() {
        this.getDetails();
    }
};
</script>

<style lang="scss" scoped>
.product-details {
    background-color: #fafcfc;
}
</style>
