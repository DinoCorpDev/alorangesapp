<template>
    <div>
        <TopBar />
        <v-app-bar
            ref="layoutNavbar"
            class="layout-navbar-auth"
            :color="$vuetify.theme.dark ? '#000000' : '#FAFCFC'"
            elevation="0"
            prominent
            dense
            shrink-on-scroll
            fixed
        >
            <v-container class="navbar-container" fluid>
                <router-link :to="{ name: 'Home2' }" class="layout-navbar-auth-brand navbar-brand">
                    <LogoAloranges class="d-none d-md-flex" />
                    <LogoAlorange class="d-flex d-md-none" />
                </router-link>

                <!-- El buscador es la accion principal de una tienda, asi que
                     ocupa el espacio libre en escritorio y baja a una segunda
                     linea a ancho completo en movil (antes desaparecia y se
                     sustituia por un boton que llevaba a otra pantalla). -->
                <div class="navbar-search">
                    <SearchInput :showInput="false" :placeholder="'Escribe lo que buscas'" />
                </div>

                <!-- En movil con sesion iniciada el header se queda SOLO con el
                     boton de menu: dentro estan Carrito, Mi lista, Pedidos,
                     Perfil y Cerrar sesion, asi que repetirlos fuera sobra.
                     Sin sesion no hay menu, asi que los botones se mantienen
                     para no dejar la barra sin acciones. -->
                <div class="header-actions navbar-actions" :class="{ 'solo-menu': soloMenuEnMovil }">
                    <!-- Icono de linea, como los del topbar. El anterior era
                         una bolsa solida y rellena que desentonaba con el
                         resto del header. -->
                    <router-link
                        :to="{ name: 'Shop' }"
                        class="navbar-action accion-secundaria"
                        title="Tienda"
                        aria-label="Tienda"
                    >
                        <i class="las la-store navbar-action-icono" aria-hidden="true"></i>
                        <span class="navbar-action-label">Tienda</span>
                    </router-link>

                    <div class="layout-navbar-auth-nav">
                        <DoubleButton class="accion-secundaria" />
                        <div class="d-flex d-lg-none" v-if="userIsLoggedIn">
                            <button
                                type="button"
                                class="navbar-action"
                                aria-label="Abrir menú"
                                @click.stop="toggleMenu"
                            >
                                <i class="las la-bars navbar-action-icono" aria-hidden="true"></i>
                                <span class="navbar-action-label">Menú</span>
                            </button>
                        </div>
                        <div style="display: none">
                            <ToggleMenu />
                        </div>
                        <!-- MODAL LOGOUT START -->
                        <div class="d-none d-lg-flex" v-if="userIsLoggedIn">
                            <v-dialog transition="dialog-top-transition" max-width="600">
                                <template v-slot:activator="dialog">
                                    <button
                                        class="d-none d-sm-flex logout-icon"
                                        color="orange3"
                                        @click="dialog.value = true"
                                    >
                                        <Logout />
                                    </button>
                                </template>
                                <template v-slot:default="dialog">
                                    <!-- pa-10 (40px) fijo dejaba 144px utiles de texto en
                                         una pantalla de 320px. Ahora el padding escala. -->
                                    <v-card class="pa-5 pa-sm-10 logout-dialog">
                                        <v-card-text class="logout-dialog-body">
                                            <LogoAlorange />
                                            <!-- Antes: pa-12 (48px mas) y font-size: 35px inline,
                                                 ademas de `font-weight: 500px` (unidad invalida en
                                                 font-weight, la declaracion se descartaba). -->
                                            <h3 class="pa-4 pa-sm-8 logout-dialog-title">
                                                ¿Seguro que desea <br />
                                                cerrar sesión?
                                            </h3>
                                        </v-card-text>
                                        <v-card-actions class="justify-center">
                                            <CustomButton color="white" @click="dialog.value = false"
                                                >Cancelar</CustomButton
                                            >
                                            <CustomButton color="orange" @click="logout">Cerrar sesión</CustomButton>
                                        </v-card-actions>
                                        <v-btn class="logout-icon-esc" @click="dialog.value = false">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="21"
                                                height="20"
                                                viewBox="0 0 21 20"
                                                fill="none"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    d="M1.36853 0.868532C1.99337 0.243693 3.00643 0.243693 3.63127 0.868532L10.4999 7.73716L17.3685 0.868532C17.9934 0.243693 19.0064 0.243693 19.6313 0.868532C20.2561 1.49337 20.2561 2.50643 19.6313 3.13127L12.7626 9.9999L19.6313 16.8685C20.2561 17.4934 20.2561 18.5064 19.6313 19.1313C19.0064 19.7561 17.9934 19.7561 17.3685 19.1313L10.4999 12.2626L3.63127 19.1313C3.00643 19.7561 1.99337 19.7561 1.36853 19.1313C0.743693 18.5064 0.743693 17.4934 1.36853 16.8685L8.23716 9.9999L1.36853 3.13127C0.743693 2.50643 0.743693 1.49337 1.36853 0.868532Z"
                                                    fill="#25292E"
                                                />
                                            </svg>
                                        </v-btn>
                                    </v-card>
                                </template>
                            </v-dialog>
                        </div>
                        <!-- MODAL LOGOUT END -->
                    </div>
                </div>
            </v-container>
        </v-app-bar>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import CustomButton from "../global/CustomButton.vue";
import DoubleButton from "./DoubleButton.vue";
import Cart from "../icons/CartIcon.vue";
import BurgerMenu from "../icons/BurgerMenu.vue";
import Search from "../icons/IconSearch.vue";
import Logout from "../icons/Logout.vue";
import LogoAloranges from "./LogoAloranges.vue";
import LogoAlorange from "./LogoAlorange.vue";
import SearchInput from "../global/SearchInput.vue";
import ToggleMenu from "./ToggleMenu.vue";
import SideMenu from "../user/SideMenu";
import TopBar from "./TopBar.vue";

export default {
    name: "LayoutNavbarAuth",
    components: {
        CustomButton,
        DoubleButton,
        LogoAloranges,
        LogoAlorange,
        Search,
        BurgerMenu,
        Logout,
        SideMenu,
        SearchInput,
        Cart,
        ToggleMenu,
        TopBar
    },
    data() {
        return {
            logoLarge: false,
            scrollThreshold: 70,
            dialogResposive: false,
            userNavDrawerActive: false
        };
    },
    computed: {
        ...mapGetters("auth", ["userShortName"]),
        ...mapGetters("auth", ["userIsLoggedIn"]),
        /**
         * En la tienda y con sesion iniciada, el header movil se queda solo
         * con el boton de menu. Se limita a las rutas de tienda a proposito:
         * el menu lateral NO incluye "Tienda", asi que ocultarla en el resto
         * de paginas dejaria sin ese acceso. Aqui no molesta porque ya se
         * esta dentro de la tienda.
         */
        soloMenuEnMovil() {
            if (!this.userIsLoggedIn) return false;
            return (this.$route.path || "").startsWith("/shop");
        },
        breadcrumbItems() {
            return this.$store.getters["breadcrumb/breadcrumbItems"];
        }
    },
    mounted() {
        window.addEventListener("resize", this.handleScroll);
        window.addEventListener("scroll", this.handleScroll, { passive: true });
    },
    methods: {
        handleScroll() {
            const currentScroll = this.$refs.layoutNavbar.currentScroll;
            const windowWidth = window.innerWidth;

            this.logoLarge = windowWidth < 960 ? false : currentScroll >= this.scrollThreshold / 2;
        },
        async logout() {
            const res = await this.call_api("get", "auth/logout");
            this["auth/logout"]();
            this.resetCart();
            this.resetWishlist();
            this.$router.push({ name: "Home2" }).catch(() => {
                console.log("Error while redirecting to home");
            });
        },
        toggleMenu() {
            this.$emit("toggleMenu");
        }
    }
};
</script>

<style lang="scss" scoped>
// --- Barra principal ---------------------------------------------------
// Una sola fila flexible: marca | buscador | acciones. En movil el buscador
// pasa a una segunda linea completa gracias a `flex-wrap` + `order`, sin
// duplicar nada en el DOM.
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

.navbar-search {
    order: 3;
    flex: 1 1 100%;
    min-width: 0;

    @include respond-up("sm") {
        order: 2;
        // Crece con el espacio libre en vez de quedarse en 500px fijos.
        flex: 1 1 auto;
        max-width: 560px;
    }
}

.header-actions {
    order: 2;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-left: auto;

    @include respond-up("sm") {
        order: 3;
        gap: 0.5rem;
    }
}

// Accion del header: icono arriba, etiqueta debajo. Objetivo tactil de 44px,
// que es el minimo recomendado y que varios botones no alcanzaban.
/* Mismo lenguaje que el buscador (pildora con borde) y el boton de cuenta
   (circulo con borde): TODAS las acciones del header son circulos de 40px.
   Antes "Tienda" y el burger eran iconos sueltos con etiqueta de 11px, un
   tercer estilo que no casaba con ninguno de los otros dos. */
.navbar-action {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 56px;
    padding: 2px 4px;
    border: 0;
    background: transparent;
    color: #3d4248;
    text-decoration: none;
    line-height: 1;
    cursor: pointer;
    transition: color 0.18s ease, transform 0.15s ease;

    /* El circulo rodea solo al icono; la etiqueta va debajo */
    .navbar-action-icono {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1.5px solid #e3e7eb;
        border-radius: 50%;
        background: #ffffff;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    &:hover,
    &:focus-visible {
        color: #f58634;

        .navbar-action-icono {
            border-color: #f58634;
            box-shadow: 0 4px 12px rgba(245, 134, 52, 0.22);
        }
    }

    &:active {
        transform: scale(0.95);
    }
}

/* Etiqueta bajo el icono: al cliente le gusta ver el nombre de la accion */
.navbar-action-label {
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
}

/* En movil, cuando hay menu, el header se queda solo con el.
   Tienda / Cuenta / Carrito viven dentro del menu, asi que fuera sobran. */
@include respond-down("sm") {
    .navbar-actions.solo-menu .accion-secundaria {
        display: none !important;
    }
}

.navbar-action-icono {
    /* Icono de fuente (line-awesome), no SVG: se controla con font-size */
    font-size: 21px;
    line-height: 1;
}

.layout-navbar-auth-nav {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

// El dialogo de cierre de sesion: los tamanos iban inline y no bajaban de
// 35px de titulo + 88px de padding acumulado, imposible en movil.
.logout-dialog-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.logout-dialog-title {
    font-size: var(--font-size-h5);
    font-weight: 500;
    line-height: 1.25;
    text-align: center;
    color: black;

    @include respond-up("sm") {
        font-size: var(--font-size-h4);
    }
}
.logout-icon {
    border: none !important;
    background-color: transparent !important;
    padding: 0 !important;
    min-width: 20px !important;
    box-shadow: none !important;
    display: flex;
    justify-content: center;
    align-items: center;
    &:hover {
        background-color: transparent !important;
    }
    &:focus {
        background-color: transparent !important;
    }
}
.logout-icon-esc {
    border: none !important;
    background-color: transparent !important;
    padding: 0 !important;
    box-shadow: none !important;
    display: flex;
    justify-content: center;
    align-items: center;
    // Iba inline; se mantiene la posicion pero sin estilo en el template.
    position: absolute;
    top: 15px;
    right: 15px;
}
.layout-navbar-auth {
    position: fixed;
    // Mismo anclaje dinamico que Navbar: antes era 64px fijo.
    top: var(--topbar-height, 64px);
    left: 0;
    right: 0;
    width: 100%;
    min-height: 64px;
    // Altura libre: el buscador baja a una segunda linea en movil y antes un
    // `max-height: 60px` lo habria dejado recortado. TheShop mide el alto real.
    height: auto !important;
    z-index: 10;
    // Sombra mas sutil que la anterior (0 4px 6px muy marcada) + linea fina.
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06), 0 2px 8px rgba(0, 0, 0, 0.06) !important;

    &::v-deep {
        .v-toolbar__content {
            min-height: 64px;
            height: auto !important;
            padding: 0;
        }
    }

    &-brand {
        text-decoration: none;

        @media (min-width: 960px) {
            min-width: 160px;
        }

        &::v-deep {
            .logo-idovela {
                width: 60px;
                height: 38px;

                @media (min-width: 960px) {
                    width: 90px;
                    height: 55px;
                }

                @media (min-width: 1264px) {
                    width: 117px;
                    height: 72px;
                }
            }
        }
    }

    &-nav {
        display: flex;
        gap: 0.65rem;

        @media (min-width: 960px) {
            gap: 1rem;
        }
    }
}
::v-deep {
    .v-overlay--active {
        .v-overlay__scrim {
            backdrop-filter: blur(30px);
            background-color: rgba(0, 0, 0, 0.15) !important;
            opacity: 1 !important;
        }
    }
}

.v-navigation-drawer {
    box-shadow: none;
}
</style>
