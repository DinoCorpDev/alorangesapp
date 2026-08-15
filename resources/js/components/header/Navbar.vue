<template>
    <div>
        <TopBar />
        <!-- Sin `prominent` ni `shrink-on-scroll`: esos props hacen que Vuetify
             escriba `height: 128px` INLINE en el header y en su toolbar, y esa
             altura fija recortaba el contenido. El buscador, que en movil baja
             a una segunda linea, quedaba FUERA del header flotando sobre la
             pagina. El encogido del logo al hacer scroll no depende de ellos:
             lo controla `logoLarge` desde handleScroll. -->
        <v-app-bar
            ref="layoutNavbar"
            class="layout-navbar"
            :color="$vuetify.theme.dark ? '#000000' : '#FAFCFC'"
            elevation="0"
            fixed
        >
            <v-container class="logo-container navbar-container" fluid>
                <router-link :to="{ name: 'Home2' }" class="layout-navbar-brand navbar-brand">
                    <LogoAloranges :large="logoLarge" class="d-none d-md-flex" />
                    <LogoAlorange class="d-flex d-md-none" />
                </router-link>

                <!-- El home no tenia buscador: para llegar al catalogo habia
                     que entrar antes a "Tienda". Ahora comparte el mismo
                     buscador que el resto del sitio. -->
                <div class="navbar-search">
                    <SearchInput :showInput="false" :placeholder="'Escribe lo que buscas'" />
                </div>

                <div class="layout-navbar-nav navbar-actions">
                    <router-link :to="{ name: 'Shop' }" class="navbar-action" title="Tienda" aria-label="Tienda">
                        <i class="las la-store navbar-action-icono" aria-hidden="true"></i>
                        <span class="navbar-action-label">Tienda</span>
                    </router-link>

                    <!-- DoubleButton siempre, no solo con sesion iniciada.
                         Antes, sin sesion, el home mostraba un boton de texto
                         "Iniciar Sesion" de 107px y SE QUEDABA SIN CARRITO,
                         mientras el resto del sitio si lo tenia: dos headers
                         distintos segun la pagina. -->
                    <DoubleButton />
                    <div style="display: none">
                        <ToggleMenu />
                    </div>
                </div>
            </v-container>
        </v-app-bar>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";

import CustomButton from "../global/CustomButton.vue";
import DoubleButton from "./DoubleButton.vue";
import LogoAloranges from "./LogoAloranges.vue";
// El mismo logo que usa NavbarAuth. Antes el home importaba
// `icons/LogoAlorange`, que declara 290x87 con el MISMO viewBox (155x33): la
// marca se veia mas grande en el home que en el resto del sitio y el header
// cambiaba de altura al navegar.
import LogoAlorange from "./LogoAlorange.vue";
import ToggleMenu from "./ToggleMenu.vue";
import Cart from "../icons/CartIcon.vue";
import TopBar from "./TopBar.vue";
import SearchInput from "../global/SearchInput.vue";

export default {
    name: "LayoutNavbar",
    components: {
        CustomButton,
        DoubleButton,
        LogoAlorange,
        LogoAloranges,
        Cart,
        ToggleMenu,
        TopBar,
        SearchInput
    },
    data() {
        return {
            logoLarge: false,
            scrollThreshold: 10
        };
    },
    computed: {
        ...mapGetters("auth", ["userIsLoggedIn"])
    },
    mounted() {
        window.addEventListener("resize", this.handleScroll);
        window.addEventListener("scroll", this.handleScroll, { passive: true });
    },
    methods: {
        ...mapMutations("auth", ["showLoginDialog"]),
        handleScroll() {
            const currentScroll = this.$refs.layoutNavbar.currentScroll;
            const windowWidth = window.innerWidth;

            this.logoLarge = windowWidth < 960 ? false : currentScroll >= this.scrollThreshold;
        }
    }
};
</script>

<style lang="scss" scoped>
// Mismo patron que NavbarAuth: marca | buscador | acciones, con el buscador
// bajando a una segunda linea en movil via flex-wrap + order.
.navbar-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem 0.75rem;
    padding: 8px 12px;

    @include respond-up("md") {
        gap: 1rem;
        padding: 10px 16px;
    }
}

.navbar-brand {
    order: 1;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

/* (se elimino .navbar-brand-logo-sm: forzaba el logo del home a 175px de
   ancho, mas grande que en el resto del sitio) */

.navbar-search {
    order: 3;
    flex: 1 1 100%;
    min-width: 0;

    @include respond-up("sm") {
        order: 2;
        flex: 1 1 auto;
        max-width: 560px;
    }
}

.navbar-actions {
    order: 2;
    margin-left: auto;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;

    @include respond-up("sm") {
        order: 3;
        gap: 0.5rem;
    }
}

/* Mismo lenguaje que el buscador (pildora con borde) y el boton de cuenta
   (circulo con borde): todas las acciones del header son circulos de 40px. */
.navbar-action {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border: 1.5px solid #e3e7eb;
    border-radius: 50%;
    background: #ffffff;
    color: #3d4248;
    text-decoration: none;
    line-height: 1;
    cursor: pointer;
    transition: border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease,
        transform 0.15s ease;

    &:hover,
    &:focus-visible {
        border-color: #f58634;
        color: #f58634;
        box-shadow: 0 4px 12px rgba(245, 134, 52, 0.22);
    }

    &:active {
        transform: scale(0.95);
    }
}

.navbar-action-label {
    display: none;
}

.navbar-action-icono {
    /* Icono de fuente (line-awesome), no SVG: se controla con font-size */
    font-size: 21px;
    line-height: 1;
}

.layout-navbar {
    position: fixed;
    // Se ancla al alto real del topbar (lo publica TheShop). Antes era un
    // `64px` fijo que se solapaba con el topbar al mostrarse el banner.
    top: var(--topbar-height, 64px);
    left: 0;
    right: 0;
    width: 100%;
    min-height: 64px;
    // Altura libre para que quepa el buscador en su segunda linea.
    height: auto !important;
    z-index: 10;
    background-color: white !important;
    // Sombra mas sutil, igual que NavbarAuth.
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06), 0 2px 8px rgba(0, 0, 0, 0.06) !important;

    // Antes: `max-width: 600px` y `min-width: 600px`, que aplicaban las dos
    // a la vez en el pixel 600 con valores contradictorios (max-height 60 vs
    // min-height 96).
    &::v-deep .v-toolbar__content {
        height: auto !important;
        min-height: 64px;
        padding: 0;
    }

    &.v-app-bar--is-scrolled {
        .logo-container {
            @include respond-up("sm") {
                padding-top: 0.5rem;
            }

            &::v-deep {
                .logo-idovela-large {
                    height: 40px;
                }
            }
        }
    }

    // Aqui habia un bloque residual del diseno antiguo que declaraba
    // `min-height: 60px` y `max-height: 60px` sobre .v-toolbar__content. Al ir
    // DESPUES en el fichero, pisaba al `height: auto` de arriba y dejaba el
    // toolbar clavado en 60px: el buscador, que en movil baja a una segunda
    // linea, se salia del header y quedaba flotando sobre la pagina.

    &-brand {
        text-decoration: none;

        &::v-deep {
            .logo-idovela {
                width: 60px;
                height: 38px;

                @include respond-up("md") {
                    width: 90px;
                    height: 55px;
                }

                @include respond-up("lg") {
                    width: 117px;
                    height: 72px;
                }
            }
        }
    }

    &-nav {
        display: flex;
        // Sin wrap, el logo + "Tienda" + login se comprimian en pantallas
        // estrechas en lugar de reordenarse.
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;

        @include respond-up("sm") {
            gap: 1rem;
        }
    }
}
</style>
