<template>
    <div>
        <div
            ref="categoryScroller"
            class="mt-3 shop-category-tabs"
            @wheel="scrollCategories"
        >
            <router-link
                v-for="tab in tabs"
                :key="`tab-${tab.text}`"
                :to="tab.to"
                class="shop-category-tab text-none"
                active-class="shop-category-tab--active"
            >
                <img :src="`${tab.icon}`" :class="`mr-2 ${tab.style}`">
                <span>{{ tab.text }}</span>
            </router-link>
        </div>

        <router-view />
    </div>
</template>

<script>
export default {
    data: () => ({
        tabs: [
            // { icon: "papeleria-24x24.png", text: "Papelería", routeName: "ShopPapeleria" },
            // { icon: "aseo-24x24.png", text: "Aseo", routeName: "ShopAseo" },
            // { icon: "cafeteria-24x24.png", text: "Cafetería", routeName: "ShopCafeteria" },
            // { icon: "tecnologia-24x24.png", text: "Tecnología", routeName: "ShopTecnologia" },
            // { icon: "cartoneria-24x24.png", text: "Empaques", routeName: "ShopCartoneria" },
            // { icon: "seguridad-industrial-24x24.png", text: "Seguridad Industrial", routeName: "ShopSeguridadIndustrial" }
        ]
    }),

    mounted() {
        this.getCategories();
    },
    methods: {
        getDefaultIcon(categoryName) {
            const icons = {
                'Papeleria': '/public/assets/img/papeleria-24x24.png',
                'Aseo': '/public/assets/img/aseo-24x24.png',
                'Cafeteria': '/public/assets/img/cafeteria-24x24.png',
                'Tecnología': '/public/assets/img/tecnologia-24x24.png',
                'Cartoneria': '/public/assets/img/cartoneria-24x24.png',
                'Seguridad industrial': '/public/assets/img/seguridad-industrial-24x24.png'
            };

            return icons[categoryName] || 'default-24x24.png';
        },
        getCategories() {
            this.call_api("get", "categories-home").then((res) => {
                if (res.data.success) {
                    this.tabs = res.data.data.map((category) => {
                        return {
                            text: category.name,
                            icon: this.getCategoryIcon(category),
                            routeName: "Shop" + this.formatearTexto(category.name),
                            to: this.getCategoryRoute(category),
                            style: this.hasCategoryImage(category) ? "tab-icon" : "static-tab-icon"
                        };
                    });

                    this.redirectToFirstCategory();
                }
            }).catch((err) => {
                console.log('Error En carga de información');
            });
        },
        redirectToFirstCategory() {
            if (this.tabs.length && this.$route.name === "Shop") {
                this.$router.replace(this.tabs[0].to);
            }
        },
        scrollCategories(event) {
            const scroller = this.$refs.categoryScroller;

            if (!scroller) {
                return;
            }

            if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
                event.preventDefault();
                scroller.scrollLeft += event.deltaY;
            }
        },
        formatearTexto(texto) {
            return texto
                .normalize('NFD') // separa las tildes
                .replace(/[\u0300-\u036f]/g, '') // elimina tildes
                .replace(/[^a-zA-Z0-9\s]/g, '') // elimina caracteres especiales
                .trim()
                .split(/\s+/)
                .map((palabra, index) => {
                    if (index === 0) {
                        return palabra.charAt(0).toUpperCase() + palabra.slice(1);
                    }
                    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
                })
                .join('');
        },
        getCategoryRoute(category) {
            const routeName = "Shop" + this.formatearTexto(category.name);

            if (this.isStaticCategory(category.name)) {
                return { name: routeName };
            }

            return {
                name: "ShopDynamicCategory",
                params: { categorySlug: category.slug || this.slugify(category.name) },
                query: { category: category.name }
            };
        },
        slugify(text) {
            return text
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        isStaticCategory(categoryName) {
            return [
                "ShopCartoneria",
                "ShopAseo",
                "ShopPapeleria",
                "ShopCafeteria",
                "ShopTecnologia",
                "ShopSeguridadIndustrial"
            ].includes("Shop" + this.formatearTexto(categoryName));
        },
        getCategoryIcon(category) {
            if (this.hasCategoryImage(category)) {
                return category.banner || category.meta_image;
            }

            if (this.isStaticCategory(category.name)) {
                return this.getDefaultIcon(category.name);
            }

            return "/public/assets/img/item-placeholder.png";
        },
        hasCategoryImage(category) {
            return !!(category.banner || category.meta_image);
        }
    }
};
</script>

<style lang="scss">
.v-application.theme--light {
    background: #fafcfc;
}
</style>

<style lang="scss" scoped>
.tab-icon {
    width: 92px;
    height: 62px;
    object-fit: cover;
    border-radius: 6px;
}

.shop-category-tabs {
    display: flex;
    justify-content: flex-start;
    gap: 16px;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 8px 4px 16px;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: #f58634 #e9ecef;
    -webkit-overflow-scrolling: touch;
}

.shop-category-tabs::-webkit-scrollbar {
    height: 8px;
}

.shop-category-tabs::-webkit-scrollbar-track {
    background: #e9ecef;
    border-radius: 10px;
}

.shop-category-tabs::-webkit-scrollbar-thumb {
    background: #f58634;
    border-radius: 10px;
}

.shop-category-tab {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #f4f5f7;
    min-width: 190px;
    min-height: 92px;
    flex: 0 0 auto;
    font-size: 18px;
    font-weight: 500;
    color: #707780 !important;
    padding: 12px 18px;
    border-radius: 8px;
    white-space: nowrap;
    text-decoration: none;
}

.shop-category-tab img {
    flex: 0 0 auto;
}

.static-tab-icon {
    width: 38px;
    height: 38px;
    object-fit: contain;
}

.shop-category-tab--active,
.shop-category-tab:hover {
    color: white !important;
    background-color: #f58634;
}

::v-deep {
    .main {
        &-carousel {
            height: 76.5vh !important;
            max-height: 786px;

            @media (min-width: 1601px) {
                height: 64vh !important;
            }

            &::v-deep {
                .v-carousel__item {
                    height: 100%;
                }
            }
        }
    }
}
</style>
