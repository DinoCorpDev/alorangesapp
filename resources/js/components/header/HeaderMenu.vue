<template>
    <div>
        <v-container class="py-0">
            <div class="d-flex align-center justify-center py-2">
                <template v-if="loading">
                    <v-skeleton-loader type="text" class="w-100px px-3 mt-2"></v-skeleton-loader>
                    <v-skeleton-loader type="text" class="w-100px px-3 mt-2"></v-skeleton-loader>
                    <v-skeleton-loader type="text" class="w-100px px-3 mt-2"></v-skeleton-loader>
                    <v-skeleton-loader type="text" class="w-100px px-3 mt-2"></v-skeleton-loader>
                    <v-skeleton-loader type="text" class="w-100px px-3 mt-2"></v-skeleton-loader>
                </template>
                <template v-else>
                    <v-list class="d-flex py-0">
                        <!-- <v-list-item class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'Home' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("home") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'AllCategories' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("all_categories") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'AllBrands' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("all_brands") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="is_addon_activated('multi_vendor')" class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'AllShops' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("all_shops") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'AllBlogs' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("all_blogs") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item class="flex-grow-0 flex-fill">
                            <v-list-item-title>
                                <router-link
                                    :to="{ name: 'AllOffers' }"
                                    class="text-reset fs-13 fw-700 opacity-80"
                                >
                                    {{ $t("offers") }}
                                </router-link>
                            </v-list-item-title>
                        </v-list-item> -->
                        <v-list-item
                            v-for="(link, label, i) in $optional('data.header_menu')"
                            :key="i"
                            class="flex-grow-0 flex-fill"
                        >
                            <v-list-item-title>
                                <dynamic-link :to="link" append-class="text-reset fs-13 fw-700 opacity-80">
                                    {{ label }}
                                </dynamic-link>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </template>
            </div>
            <v-divider class=""></v-divider>
        </v-container>
    </div>
</template>

<script>
export default {
    props: {
        loading: { type: Boolean, required: true, default: true },
        data: {
            type: Object,
            default: {}
        }
    }
};
</script>
