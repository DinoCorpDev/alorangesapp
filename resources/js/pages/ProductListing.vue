<template>
    <v-container class="search mb-5" fluid>
        <div class="d-flex d-sm-none">
            <SearchInput  :showInput="true" :placeholder="'Escribe lo que buscas'" style="max-width: 500px; margin-bottom: 25px;  box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;" />
        </div>
        <v-row class="filter-bar-wrap mb-5">
            <v-col cols="12">
                <div class="filter-bar">
                    <h5 class="filter-bar-title mr-8">Filtro</h5>
                    <div class="filter-bar-actions">
                        <v-skeleton-loader type="actions" v-if="loading && rootCategories.length == 0" />
                        <template v-else>
                            <v-checkbox
                                on-icon="la-check"
                                class="filter-bar-button"
                                hide-details
                                v-for="(category, i) in rootCategories"
                                :input-value="handleCategoryChecked(category.id)"
                                :key="i"
                                :label="category.name"
                                :ripple="false"
                                @change="categoryChange(category.id)"
                            ></v-checkbox>
                        </template>
                    </div>
                </div>
            </v-col>
        </v-row>
        <v-row class="d-flex flex-wrap">
            <v-col cols="12">
                <h5 class="search-results" v-if="queryParam.keyword">
                    Resultados para {{ queryParam.keyword }}
                </h5>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2" v-if="loading">
                <v-skeleton-loader type="card" />
            </v-col>
            <template v-else>
                <template v-if="products.length > 0">
                    <v-col cols="6" sm="4" md="4" lg="2" v-for="(product, i) in products" :key="i">
                        <ProductBox :productDetails="product" />
                    </v-col>
                </template>
                <template v-else>
                    <v-col cols="12">
                        {{ $t("Producto no encontrado") }}
                    </v-col>
                </template>
            </template>
        </v-row>
    </v-container>
</template>

<script>
import ProductBox from "../components/product/ProductBox.vue";
import SearchInput from "../components/global/SearchInput.vue";
export default {
    components: {
        SearchInput,
        ProductBox
    },
    data() {
        return {
            loading: true,
            queryParam: {
                page: 1,
                categorySlug: null,
                brandIds: [],
                categoryIds: [],
                attributeValues: [],
                keyword: null,
                sortBy: "popular",
                minPrice: null,
                maxPrice: null
            },
            products: [],
            rootCategories: [],
            currentCategory: {}
        };
    },
    created() {
        this.isBrandRoute = this.$route.params.brandId ? true : this.isBrandRoute;
        this.queryParam.categorySlug = this.$route.params.categorySlug || this.queryParam.categorySlug;
        this.queryParam.keyword = this.$route.params.keyword;
        this.queryParam.brandIds = this.$route.params.brandId || this.queryParam.brandIds;

        const categoryIds = this.$route.query.categoryIds;
        this.queryParam.categoryIds = categoryIds
            ? categoryIds.length > 1
                ? categoryIds.map(cat => Number(cat))
                : [Number(categoryIds)]
            : this.queryParam.categoryIds;

        this.queryParam.page = this.$route.query.page || this.queryParam.page;
        this.queryParam.sortBy = this.$route.query.sortBy || this.queryParam.sortBy;
        this.queryParam.minPrice = this.$route.query.minPrice || this.queryParam.minPrice;
        this.queryParam.maxPrice = this.$route.query.maxPrice || this.queryParam.maxPrice;
        this.queryParam.attributeValues = this.$route.query.attributeValues || this.queryParam.attributeValues;

        if (this.queryParam.sortBy !== "popular") {
            let selectedSort = this.sortingOptions.find(sort => sort.value === this.queryParam.sortBy);
            this.sortingDefault = selectedSort;
        }

        this.getList({
            page: this.queryParam.page,
            categorySlug: this.queryParam.categorySlug,
            brandIds: this.queryParam.brandIds,
            categoryIds: this.queryParam.categoryIds,
            attributeValues: this.queryParam.attributeValues,
            keyword: this.queryParam.keyword,
            sortBy: this.queryParam.sortBy,
            minPrice: this.queryParam.minPrice,
            maxPrice: this.queryParam.maxPrice
        });
    },
    methods: {
        async getList(obj) {
            this.loading = true;
            let params = { ...this.queryParam, ...obj };

            let url = "product/search?";
            url += `&page=${this.queryParam.page}`;
            url += params.categorySlug ? `&category_slug=${params.categorySlug}` : "";
            url += params.brandIds ? `&brand_ids=${params.brandIds}` : "";
            url += params.categoryIds ? `&category_ids=${params.categoryIds}` : "";
            url += params.attributeValues ? `&attribute_values=${params.attributeValues}` : "";
            url += params.keyword ? `&keyword=${params.keyword}` : "";
            url += `&form=search`;
            url += params.sortBy ? `&sort_by=${params.sortBy}` : "";
            url += params.minPrice ? `&min_price=${params.minPrice}` : "";
            url += params.maxPrice ? `&max_price=${params.maxPrice}` : "";

            const res = await this.call_api("get", url);

            if (res.data.success) {
                this.loading = false;
                this.metaTitle = res.data.metaTitle;
                this.products = res.data.products.data;
                this.attributes = res.data.attributes.data;
                this.allBrands = res.data.allBrands.data;
                this.rootCategories = res.data.rootCategories.data;
                this.parentCategory = res.data.parentCategory ? res.data.parentCategory : {};
                this.currentCategory = res.data.currentCategory ? res.data.currentCategory : {};
                this.childCategories = res.data.childCategories ? res.data.childCategories.data : [];
                this.totalPages = res.data.totalPage;
                this.totalProducts = res.data.total;
                this.queryParam.page = res.data.currentPage;
            }
        },
        categoryChange(id) {
            if (this.queryParam.categoryIds.indexOf(id) > -1) {
                let index = this.queryParam.categoryIds.indexOf(id);
                this.queryParam.categoryIds.splice(index, 1);
            } else {
                this.queryParam.categoryIds.push(id);
            }

            this.$router
                .push({
                    query: {
                        ...this.$route.query,
                        categoryIds: this.queryParam.categoryIds
                    }
                })
                .catch(() => {});

            this.getList({});
        },
        handleCategoryChecked(id) {
            return this.queryParam.categoryIds.find(categoryId => categoryId == id);
        }
    }
};
</script>

<style lang="scss" scoped>
.search {
    background: #fafcfc;
}

.filter-bar {
    display: flex;
    align-items: center;

    &-wrap {
        border-bottom: 1px solid #e4e4e4;
    }

    &-title {
        font-size: 15px;
        letter-spacing: 0.15px;
        font-weight: 400;

        @media (min-width: 960px) {
            font-size: 20px;
        }
    }

    &-actions {
        overflow-y: auto;
        display: flex;
        gap: 1rem;
    }

    &-button {
        &::v-deep {
            .v-input--selection-controls__input {
                display: none;
            }

            label {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 38px !important;
                padding: 0 16px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 12px;
                line-height: 17px;
                color: #000000;
                white-space: nowrap;

                @media (min-width: 960px) {
                    font-size: 14px;
                }
            }

            &.v-input--is-label-active {
                label {
                    color: #ffffff;
                }
            }
        }

        margin-top: 0;
        background-color: #f5f5f5;
        border-radius: 5px;
        padding: 0;

        &:hover {
            background-color: #dfdfdf;
        }

        &.v-input--is-label-active {
            background-color: #000000;
        }
    }
}

.search-results {
    font-size: 24px;
    font-weight: 400;
    line-height: 30px;
}
</style>
