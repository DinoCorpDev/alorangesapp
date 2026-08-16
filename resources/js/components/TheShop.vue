<template>
    <v-app class="d-flex flex-column">

        <Navbar v-if="$route.meta.hasHeader && $route.name == 'Home2'" />
        <NavbarAuth v-if="$route.meta.hasHeader && $route.name != 'Home2'" @toggleMenu="toggleMenu" />

        <v-main class="aiz-main-wrap" :style="mainOffsetStyle">
            <Breadcrumb />
            <v-navigation-drawer v-model="userNavDrawerActive" fixed temporary right style="z-index: 999">
                <SideMenu class="pa-3" />
            </v-navigation-drawer>
            <router-view
                :key="['ShopDetails', 'ShopCoupons', 'ShopProducts'].includes($route.name) ? null : $route.path"
            ></router-view>
        </v-main>

        <Footer v-if="$route.meta.hasFooter" :class="[{ 'd-none': routerLoading }]" />

        <WhatsAppButton />

        <AddToCartDialog />
        <LoginDialog v-if="!isAuthenticated" />
        <SnackBar />
    </v-app>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";

import AddToCartDialog from "./product/AddToCartDialog";
import Footer from "./footer/Footer";
import LoginDialog from "./auth/LoginDialog.vue";
import Navbar from "./header/Navbar.vue";
import NavbarAuth from "./header/NavbarAuth.vue";
import SnackBar from "./inc/SnackBar";
import Breadcrumb from "./header/Breadcrumb.vue";
import WhatsAppButton from "./global/WhatsAppButton.vue";
import SideMenu from "./user/SideMenu";

export default {
    metaInfo() {
        return {
            title: this.appMetaTitle
        };
    },
    data() {
        return {
            userNavDrawerActive: false,
            // Alto real del header (topbar + navbar), medido en vivo.
            // Arranca en 160 solo como valor razonable para el primer
            // pintado; se corrige en cuanto el header esta en el DOM.
            headerHeight: 160,
            headerObserver: null,
            headerSyncTimers: [],
            headerResizeRaf: null,
            headerWatchdog: null
        };
    },
    components: {
        AddToCartDialog,
        Footer,
        WhatsAppButton,
        LoginDialog,
        Breadcrumb,
        Navbar,
        NavbarAuth,
        SideMenu,
        SnackBar
    },
    watch: {
        $route(to, from) {
            window.scrollTo(0, 0); // Esto forzará el scroll al tope en cada cambio de ruta
            // Al cambiar de ruta se intercambia Navbar <-> NavbarAuth, que
            // tienen alturas distintas: hay que volver a medir y observar.
            this.$nextTick(this.scheduleHeaderSync);
        }
    },
    computed: {
        // El header es `position: fixed`, asi que el contenido necesita un
        // offset igual a su altura REAL. Antes era un 160px fijo que dejaba
        // 36px de hueco en movil y tapaba 32px de contenido en la home.
        //
        // Va como estilo inline a proposito: Vuetify escribe el `padding`
        // del v-main inline, por lo que una regla CSS nunca ganaria.
        mainOffsetStyle() {
            return {
                marginTop: this.$route.meta.hasHeader ? `${this.headerHeight}px` : "0px"
            };
        },
        ...mapGetters("auth", ["isAuthenticated"]),
        ...mapGetters("cart", ["getTempUserId"]),
        ...mapGetters("app", ["appMetaTitle", "userLanguageObj", "routerLoading", "maintenanceMode"])
    },
    methods: {
        ...mapActions("auth", ["getUser", "checkSocialLoginStatus"]),
        ...mapActions("wishlist", ["fetchWislistProducts", "fetchWislistServices", "fetchWislistBrands"]),
        ...mapActions("cart", ["fetchCartProducts"]),
        ...mapMutations("auth", ["setSociaLoginStatus"]),
        changeRTL() {
            if (this.userLanguageObj.rtl == 1) {
                this.$vuetify.rtl = true;
            } else {
                this.$vuetify.rtl = false;
            }
        },
        toggleMenu() {
            this.userNavDrawerActive = !this.userNavDrawerActive;
        },
        /**
         * Retira la pantalla de carga inicial que pinta el Blade.
         * Se difumina primero y se elimina del DOM despues, para que no
         * quede capturando pulsaciones por encima de la pagina.
         *
         * Se mantiene un minimo en pantalla: con el bundle ya en cache la app
         * monta en decimas de segundo y el logo apenas se llegaba a ver, lo
         * que producia un parpadeo mas molesto que no mostrar nada.
         */
        retirarSplash() {
            const splash = document.getElementById("app-splash");
            if (!splash) return;

            const MINIMO_VISIBLE = 1400;
            // performance.now() cuenta desde que empezo a cargar la pagina,
            // asi que en una carga lenta el minimo ya esta cumplido y no
            // se anade ninguna espera.
            const restante = Math.max(0, MINIMO_VISIBLE - performance.now());

            setTimeout(() => {
                splash.classList.add("is-oculto");
                setTimeout(() => splash.remove(), 500);
            }, restante);
        },
        // Suma la altura de las piezas fijas del header. Cubre de una vez
        // todas las variables que antes se intentaban acertar a mano:
        // ruta (Navbar vs NavbarAuth), breakpoint, banner del topbar
        // visible o no, y el logo que cambia de tamano al hacer scroll.
        measureHeader() {
            const parts = document.querySelectorAll(
                ".topbar, header.layout-navbar, header.layout-navbar-auth"
            );
            let total = 0;
            let topbar = 0;
            parts.forEach(el => {
                if (getComputedStyle(el).position !== "fixed") return;
                const h = el.getBoundingClientRect().height;
                total += h;
                if (el.classList.contains("topbar")) topbar = h;
            });

            const rounded = Math.round(total);
            if (rounded > 0 && rounded !== this.headerHeight) {
                this.headerHeight = rounded;
                // Se expone tambien como custom property para que cualquier
                // componente pueda anclarse al header sin recalcularlo.
                document.documentElement.style.setProperty("--app-header-height", `${rounded}px`);
            }

            // Los navbars se anclan justo debajo del topbar. Antes lo hacian
            // con un `top: 64px` fijo, que se rompia en cuanto el topbar
            // crecia (banner superior visible) o cambiaba de alto.
            if (topbar > 0) {
                document.documentElement.style.setProperty(
                    "--topbar-height",
                    `${Math.round(topbar)}px`
                );
            }
        },
        observeHeader() {
            if (this.headerObserver) this.headerObserver.disconnect();
            this.measureHeader();

            if (typeof ResizeObserver === "undefined") return;

            this.headerObserver = new ResizeObserver(() => this.measureHeader());
            document
                .querySelectorAll(".topbar, header.layout-navbar, header.layout-navbar-auth")
                .forEach(el => this.headerObserver.observe(el));
        },
        // Red de seguridad. El ResizeObserver es la via rapida, pero no se
        // puede depender solo de el: hay entornos donde no llega a dispararse
        // (comprobado aqui: no emitia ni la notificacion inicial) y ademas Vue
        // reemplaza los nodos del header, dejandolo observando elementos
        // muertos. Cuando eso pasa, el contenido queda desplazado y no se
        // recupera solo.
        //
        // Coste real: dos getBoundingClientRect cada 250ms. Despreciable, y
        // el navegador lo detiene cuando la pestana no esta visible.
        startHeaderWatchdog() {
            this.stopHeaderWatchdog();
            this.headerWatchdog = setInterval(this.measureHeader, 250);
        },
        stopHeaderWatchdog() {
            if (this.headerWatchdog) clearInterval(this.headerWatchdog);
            this.headerWatchdog = null;
        },
        // Al cambiar el ancho, el header se reordena (el buscador baja a una
        // segunda linea) y su alto cambia DESPUES del evento `resize`. Medir
        // solo en el evento dejaba el offset con el valor anterior.
        onWindowResize() {
            this.measureHeader();
            this.scheduleHeaderSync();
        },
        // Reengancha el observador y vuelve a medir varias veces durante la
        // ventana en la que el header se asienta.
        //
        // Hace falta porque: (a) al arrancar, Vuetify recalcula la app-bar y
        // cargan logo y fuentes; (b) Vue REEMPLAZA los nodos del header al
        // cambiar de ruta, dejando al ResizeObserver observando elementos ya
        // desconectados que nunca vuelven a notificar. Sin esto la medicion se
        // quedaba congelada en un valor intermedio.
        scheduleHeaderSync() {
            this.headerSyncTimers.forEach(clearTimeout);
            this.headerSyncTimers = [0, 100, 300, 700, 1500, 2500].map(ms =>
                setTimeout(this.observeHeader, ms)
            );
        },
        async getTempCartData() {
            if (this.isAuthenticated && this.getTempUserId) {
                const res = await this.call_api("post", "temp-id-cart-update", {
                    temp_user_id: this.getTempUserId
                });
                this.fetchCartProducts();
            }
        },
        async getCartData() {
            if (this.isAuthenticated) {
                this.fetchCartProducts();
                this.fetchWislistProducts();
                this.fetchWislistServices();
                this.fetchWislistBrands();
            }
        }
    },
    async created() {
        this.changeRTL();
        await this.getUser();
        setTimeout(() => {
            this.checkSocialLoginStatus();
            this.getTempCartData();
            this.getCartData();
        }, 200);
    },
    mounted() {
        this.retirarSplash();
        this.scheduleHeaderSync();
        this.startHeaderWatchdog();
        window.addEventListener("resize", this.onWindowResize, { passive: true });
        // El logo del navbar crece/encoge con el scroll y cambia la altura.
        window.addEventListener("scroll", this.measureHeader, { passive: true });
        // Logo y fuentes pueden cambiar el alto del header al terminar de cargar.
        window.addEventListener("load", this.observeHeader);
    },
    beforeDestroy() {
        if (this.headerObserver) this.headerObserver.disconnect();
        if (this.headerResizeRaf) cancelAnimationFrame(this.headerResizeRaf);
        this.stopHeaderWatchdog();
        this.headerSyncTimers.forEach(clearTimeout);
        window.removeEventListener("resize", this.onWindowResize);
        window.removeEventListener("scroll", this.measureHeader);
        window.removeEventListener("load", this.observeHeader);
    }
};
</script>

<style lang="scss" scoped>
.absolute-full {
    background: #fff;
    z-index: 10000;
}

/* --- Barra de progreso de navegacion ---
   Va pegada al borde superior, por encima del header pero por debajo de los
   modales y del menu lateral (999). */
// El offset del header lo aplica `mainOffsetStyle` como margin-top inline,
// calculado a partir de la altura real medida.
//
// Aqui habia `padding-top: 160px` y un `@media (max-width: 960px)` con
// `padding-top: 124px` que NUNCA llegaron a aplicarse: Vuetify escribe el
// padding del v-main inline y gana siempre. Es decir, el ajuste responsive
// del header llevaba tiempo siendo codigo muerto.
</style>
