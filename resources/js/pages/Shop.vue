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
                <img :src="`/public/assets/img/${tab.icon}`" class="mr-2">
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
            { icon: "papeleria-24x24.png", text: "Papelería", routeName: "ShopPapeleria" },
            { icon: "aseo-24x24.png", text: "Aseo", routeName: "ShopAseo" },
            { icon: "cafeteria-24x24.png", text: "Cafetería", routeName: "ShopCafeteria" },
            { icon: "tecnologia-24x24.png", text: "Tecnología", routeName: "ShopTecnologia" },
            { icon: "cartoneria-24x24.png", text: "Empaques", routeName: "ShopCartoneria" },
            { icon: "seguridad-industrial-24x24.png", text: "Seguridad Industrial", routeName: "ShopSeguridadIndustrial" }
        ]
    }),
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
