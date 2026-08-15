<template>
    <div class="topbar">
        <!-- Banner promocional (se mantiene tal cual, solo cambia el estilo) -->
        <div v-if="topBannerVisible && !loading && data.top_banner && data.top_banner.img" class="topbar-banner">
            <dynamic-link :to="data.top_banner.link" append-class="text-reset d-block lh-0">
                <img :src="data.top_banner.img" class="topbar-banner-img" />
            </dynamic-link>
            <button type="button" class="topbar-banner-close" aria-label="Cerrar" @click="closeTopBanner">
                <i class="las la-times" />
            </button>
        </div>

        <div class="topbar-strip">
            <v-container class="py-0 px-3">
                <div class="topbar-inner">
                    <!-- Reclamo principal: es lo que mas vende, asi que va
                         destacado y es lo unico que nunca se oculta. -->
                    <span class="topbar-claim">
                        <i class="las la-truck" />
                        <span>Envíos a toda Colombia</span>
                    </span>

                    <div class="topbar-meta">
                        <a :href="'tel:' + $optional('data.helpline')" class="topbar-link">
                            <i class="la la-phone" />
                            <span>+57 3174420109</span>
                        </a>

                        <span class="topbar-sep" aria-hidden="true"></span>

                        <a :href="'mailto:ventas5@aloranges.com'" class="topbar-link topbar-link--email">
                            <i class="las la-envelope" />
                            <span>ventas5@aloranges.com</span>
                        </a>

                        <!-- Selector de idioma -->
                        <template v-if="data.show_language_switcher == 'on' && allLanguages.length > 1">
                            <span class="topbar-sep" aria-hidden="true"></span>
                            <v-menu offset-y :close-on-click="menuCloseOnClick" :elevation="2">
                                <template #activator="{ on, attrs }">
                                    <button type="button" class="topbar-link" v-bind="attrs" v-on="on">
                                        <span>{{ userLanguageObj.name }}</span>
                                        <i class="las la-angle-down topbar-caret" />
                                    </button>
                                </template>
                                <v-list dense>
                                    <v-list-item
                                        v-for="(language, i) in allLanguages"
                                        :key="i"
                                        class="c-pointer d-flex align-center"
                                        @click="switchLanguage(language.code)"
                                    >
                                        <img
                                            :src="static_asset(`/assets/img/flags/${language.flag}.png`)"
                                            class="me-2 topbar-flag"
                                        />
                                        <v-list-item-title class="fs-13">{{ language.name }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template>

                        <!-- Enlaces a las tiendas de apps -->
                        <template v-if="data.mobile_app_links && data.mobile_app_links.show_play_store == 'on'">
                            <span class="topbar-sep" aria-hidden="true"></span>
                            <a
                                :href="$optional('data.mobile_app_links?.play_store')"
                                target="_blank"
                                class="topbar-link"
                            >
                                <i class="lab la-android" />
                                <span>{{ $t("play_store") }}</span>
                            </a>
                        </template>
                        <template v-if="data.mobile_app_links && data.mobile_app_links.show_app_store == 'on'">
                            <span class="topbar-sep" aria-hidden="true"></span>
                            <a
                                :href="$optional('data.mobile_app_links?.app_store')"
                                target="_blank"
                                class="topbar-link"
                            >
                                <i class="lab la-apple" />
                                <span>{{ $t("app_store") }}</span>
                            </a>
                        </template>

                        <!-- Registro de vendedor -->
                        <template v-if="is_addon_activated('multi_vendor')">
                            <span class="topbar-sep" aria-hidden="true"></span>
                            <router-link :to="{ name: 'ShopRegistration' }" class="topbar-link topbar-link--accent">
                                {{ $t("be_a_seller") }}
                            </router-link>
                        </template>
                    </div>
                </div>
            </v-container>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    props: {
        // Ningun navbar pasa estas props hoy: `required: true` generaba un
        // [Vue warn] en cada pagina. Opcionales con defaults seguros.
        loading: { type: Boolean, default: true },
        data: {
            type: Object,
            // Vue exige factory para defaults de tipo Object (era otro warn).
            default: () => ({})
        }
    },
    data: () => ({
        topBannerVisible: false,
        topBanner: {
            image: Vue.helpers.asset("/uploads/img/topbar.jpg"),
            link: ""
        },
        currencies: [
            {
                name: "U.S. Dollar",
                sysmbol: "$",
                code: "USD"
            },
            {
                name: "Taka",
                sysmbol: "Tk",
                code: "BDT"
            }
        ],
        cselectedCurrency: {
            name: "U.S. Dollar",
            sysmbol: "$",
            code: "USD"
        },
        menuCloseOnClick: true
    }),
    computed: {
        ...mapGetters("app", ["generalSettings"]),
        ...mapGetters("wishlist", ["getTotalWishlisted"]),
        ...mapGetters("compareList", ["getTotalComparedList"]),
        ...mapGetters("app", ["userLanguageObj", "allLanguages", "allCurrencies"])
    },
    methods: {
        ...mapActions("app", ["fetchProductQuerries"]),
        ...mapActions("wishlist", ["fetchWislistProducts"]),
        ...mapActions("app", ["setLanguage"]),
        switchLanguage(locale) {
            if (this.$i18n.locale !== locale) {
                this.setLanguage(locale);
                window.location.reload();
            }
        },
        closeTopBanner() {
            this.topBannerVisible = false;
            this.setSession("shopTopBanner", "hidden");
        }
    },
    created() {
        if (this.checkSession("shopTopBanner") != "hidden") {
            this.topBannerVisible = true;
        }
        this.fetchWislistProducts();
        this.fetchProductQuerries();
        setInterval(() => {
            this.fetchProductQuerries();
        }, 8000);
    }
};
</script>

<style lang="scss" scoped>
// Franja superior oscura. Antes era blanca sobre un navbar tambien blanco, sin
// separacion visual y ocupando 64px solo para datos de contacto. Ahora
// contrasta con el navbar, pesa menos y libera altura para el contenido.
.topbar {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    /* MISMA capa que el navbar (10): topbar y navbar son una sola pieza y
       deben taparse/destaparse juntos.
       Antes 1100, por encima del drawer lateral (999): al abrir el menu de
       usuario el contenido y el navbar quedaban bajo el velo, pero la franja
       oscura del topbar seguia flotando encima de todo, incluido el propio
       menu. */
    z-index: 10;
    // Altura libre: TheShop mide el alto real y desplaza el contenido, asi que
    // el banner promocional ya no queda recortado como con la altura fija.
    height: auto;
    background-color: #25292e;
}

.topbar-strip {
    color: rgba(255, 255, 255, 0.82);
    font-size: 13px;
}

.topbar-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 2px 16px;
    min-height: 40px;
    padding: 4px 0;
}

.topbar-claim {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    color: #ffffff;
    white-space: nowrap;

    i {
        color: #f58634;
        font-size: 16px;
    }
}

.topbar-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 2px 10px;
}

.topbar-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: rgba(255, 255, 255, 0.82);
    white-space: nowrap;
    background: none;
    border: 0;
    /* Area tactil de 34px sin engordar la barra: el relleno vertical es
       clicable aunque el texto ocupe menos. Antes eran 22px. */
    padding: 8px 0;
    min-height: 34px;
    font-size: 13px;
    transition: color 0.15s ease;

    &:hover,
    &:focus-visible {
        color: #ffffff;
    }

    i {
        font-size: 15px;
    }

    &--accent {
        color: #f58634;
        font-weight: 600;

        &:hover {
            color: lighten(#f58634, 10%);
        }
    }
}

.topbar-caret {
    font-size: 11px !important;
    opacity: 0.7;
}

.topbar-flag {
    height: 12px;
    width: auto;
}

.topbar-sep {
    width: 1px;
    height: 14px;
    background-color: rgba(255, 255, 255, 0.22);
}

// Banner promocional
.topbar-banner {
    position: relative;
}

.topbar-banner-img {
    display: block;
    width: 100%;
    height: auto;
    // Antes: `h-50px` + object-fit cover, que recortaba el banner en movil.
    max-height: 90px;
    object-fit: contain;
}

.topbar-banner-close {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    background-color: rgba(0, 0, 0, 0.45);
    transition: background-color 0.15s ease;

    &:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }
}

// En movil el reclamo de envios se centra y los datos secundarios se reparten
// debajo. Nada de scroll horizontal para leer un telefono.
@include respond-down("md") {
    .topbar-inner {
        justify-content: center;
        gap: 2px 12px;
    }

    .topbar-meta {
        justify-content: center;
    }

    .topbar-link {
        font-size: 12px;
    }

    // El correo es el dato mas largo y el menos urgente en movil.
    .topbar-link--email {
        display: none;
    }
}

@include respond-down("sm") {
    .topbar-sep {
        display: none;
    }
}
</style>
