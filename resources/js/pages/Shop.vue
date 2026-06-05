<template>
    <div>
        <v-tabs fixed-tabs :show-arrows="false" class="mt-3">
            <v-tab
                v-for="tab in tabs"
                :key="`tab-${tab.text}`"
                :ripple="false"
                :to="{ name: tab.routeName }"
                class="text-none"
                link
            >
                <img :src="`${tab.icon}`" :class="`mr-2 ${tab.style}`">
                <span>{{ tab.text }}</span>
            </v-tab>
        </v-tabs>

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
                            icon: category.meta_image || this.getDefaultIcon(category.name),
                            routeName: "Shop" + this.formatearTexto(category.name),
                            style: category.meta_image ? "tab-icon" : ""
                        };
                    });
                }
            }).catch((err) => {
                console.log('Error En carga de información');
            });
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
.theme--dark.v-tabs {
    &::v-deep {
        .v-tabs-bar {
            background-color: #000000;
        }

        .v-tabs-bar__content {
            border-bottom: 1px solid #242526;
        }
    }
}

.tab-icon {
    width: 70px;
    height: 46px;
    // object-fit: contain;
}

.v-tabs {
    &::v-deep {
        .v-tabs-slider {
            background-color: transparent;
        }
        .v-tabs-bar {
            background-color: #fafcfc;

            @media (max-width: 600px) {
                height: auto;
            }
        }

        .v-tabs-bar__content {
            display: flex;
            justify-content: space-between;
        }

        .v-tab {
            background-color: #f4f5f7;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: unset;
            color: #707780 !important;
            margin: 0 10px !important;
            border-radius: 8px;

            &--active,
            &:hover {
                color: white !important;
                background-color: #f58634;
            }

            &:before,
            .v-tabs-slider {
                background-color: transparent;
            }
        }

        .v-slide-group__prev,
        .v-slide-group__next {
            display: none !important;
        }
    }
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
