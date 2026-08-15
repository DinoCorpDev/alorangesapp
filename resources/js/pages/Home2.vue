<template>
    <v-container style="background-color: white">
        <!-- HERO. Antes era un unico h1 de 46-58px con una frase de 130
             caracteres que enumeraba todas las categorias: un muro de texto.
             Mismo mensaje, pero con jerarquia (antetitulo / titular corto /
             categoria rotando / apoyo) y con las categorias convertidas en
             accesos directos, que ademas es la accion que buscamos. -->
        <v-row class="hero home-section" align="center" tag="main">
            <!-- Manchas decorativas: dan profundidad al bloque, que antes era
                 un rectangulo naranja plano. -->
            <span class="hero-blob hero-blob--1" aria-hidden="true"></span>
            <span class="hero-blob hero-blob--2" aria-hidden="true"></span>

            <v-col class="hero-content" cols="12" sm="7" md="7">
                <span class="hero-badge">
                    <i class="las la-truck" aria-hidden="true"></i>
                    Envíos a toda Colombia
                </span>

                <h1 class="hero-title">
                    Tu proveedor de
                    <!-- La palabra rota sola y cambia al pasar por los chips.
                         El texto completo queda accesible para lectores de
                         pantalla y buscadores en el <span class="sr-only">. -->
                    <!-- Sin <transition>: su modo out-in depende de
                         requestAnimationFrame, que el navegador PAUSA en
                         pestanas de fondo. Si la home se abria en segundo
                         plano, la animacion de salida no terminaba nunca y la
                         palabra se quedaba congelada de forma permanente.
                         Con :key, Vue reemplaza el elemento y la animacion CSS
                         se dispara sola al insertarlo, sin depender de rAF. -->
                    <span class="hero-rotator" aria-hidden="true">
                        <span
                            class="hero-rotator-word"
                            :key="categoriaActiva.texto"
                            :style="{ color: categoriaActiva.color }"
                        >{{ categoriaActiva.texto }}</span>
                    </span>
                    <span class="sr-only">
                        suministros de papelería, aseo, cafetería, cartonería, tecnología y seguridad industrial
                    </span>
                    <!-- Sin <br>: .hero-rotator ya es display:block y el salto
                         extra abria un hueco de 65px bajo la palabra. -->
                    para tu empresa
                </h1>

                <p class="hero-sub">
                    Y mucho más, en un solo lugar: rápido, fácil y seguro.
                </p>

                <SearchInput class="search-menu" :showInput="true" :placeholder="'Escribe lo que buscas'" />

                <!-- El catalogo, a la vista. Antes las categorias eran una
                     tira de chips que se perdia, y las de verdad estaban a
                     1.200px de scroll. Ahora son tarjetas con la imagen real
                     de cada categoria, dentro de la primera pantalla. -->
                <nav class="hero-cats" aria-label="Categorías">
                    <router-link
                        v-for="(cat, i) in heroCategorias"
                        :key="cat.texto"
                        :to="cat.to"
                        class="hero-cat"
                        :class="{ 'is-active': i === heroIndex }"
                        @mouseenter.native="fijarCategoria(i)"
                        @focus.native="fijarCategoria(i)"
                        @mouseleave.native="reanudarRotacion"
                        @blur.native="reanudarRotacion"
                    >
                        <img
                            v-if="cat.imagen"
                            :src="cat.imagen"
                            :alt="cat.texto"
                            class="hero-cat-img"
                            loading="lazy"
                            @error="imageFallback($event)"
                        />
                        <span v-else class="hero-cat-img hero-cat-img--vacia" aria-hidden="true">
                            <i class="las la-box-open"></i>
                        </span>
                        <span class="hero-cat-nombre">{{ cat.texto }}</span>
                    </router-link>
                </nav>
            </v-col>

            <v-col class="pa-0 d-flex hero-media" cols="12" sm="5" md="5">
                <img
                    class="hero-banner-img"
                    src="/public/assets/img/bannerhomeimg-removebg-preview3.png"
                    alt="Suministros para empresas"
                />
            </v-col>
        </v-row>

        <v-row tag="section" class="preambulo home-section">
            <v-col cols="12" class="px-0">
                <div class="rounded-section px-5 py-8">
                    <v-row justify="center">
                        <!-- `px-0` en movil dejaba sin compensar los margenes
                             de -12px del v-row interior: eran los 4px de scroll
                             horizontal que arrastraba toda la home. -->
                        <v-col cols="12" lg="12" class="px-3">
                            <v-row
                                :no-gutters="
                                    $vuetify.breakpoint.name == 'md' || $vuetify.breakpoint.name == 'lg' ? false : true
                                "
                            >
                                <v-col class="center-flex" cols="6" sm="3">
                                    <v-img
                                        src="/public/assets/img/Group13.png"
                                        style="width: 60%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3 style="text-align: center">Domicilios gratis</h3>
                                    <p class="preambulo-text" style="text-align: center">En compras mínimas</p>
                                </v-col>
                                <v-col class="center-flex" cols="6" sm="3">
                                    <v-img
                                        src="/public/assets/img/Group14.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Apoyo 24/7</h3>
                                    <p class="preambulo-text">Servicio técnico</p>
                                </v-col>
                                <v-col class="center-flex" cols="6" sm="3">
                                    <v-img
                                        src="/public/assets/img/Group12.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Pago seguro</h3>
                                    <p class="preambulo-text">Múltiples bancos</p>
                                </v-col>
                                <v-col class="center-flex" cols="6" sm="3">
                                    <v-img
                                        src="/public/assets/img/Refresh.png"
                                        style="width: 40%; height: auto"
                                        contain
                                        class="mb-5"
                                    />
                                    <h3>Devoluciones</h3>
                                    <p class="preambulo-text" style="text-align: center">Cambios de productos</p>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </div>
            </v-col>
        </v-row>
        <v-row class="center-items home-section">
            <v-col cols="12" class="bg-green-ligth border-style-banner">
                <CarouselSpaces
                    title="Categorias"
                    img="/public/assets/img/home/icon-be-there.svg"
                    :spaces="itemsArray"
                />
            </v-col>
        </v-row>

        <v-row class="home-section">
            <v-col cols="12" class="d-flex justify-center">
                <PresentationBanner
                    id="be-there"
                    icon="/public/assets/img/home/icon-be-there.svg"
                    preamble="Escuchar, Comprender y Percibir al Usuario"
                    title="¡Facilitamos tus procesos con un toque de tecnología!"
                    image="/public/assets/img/banner2home.png"
                    style="max-width: 99%"
                >
                    <template v-slot:description>
                        En <b>Aloranges,</b> nos dedicamos a hacerte la vida más fácil. Somos expertos en satisfacer las
                        necesidades de tu empresa con
                        <b>suministros de papelería, cartonería, cafetería, aseo, tecnología y botiquín y mucho mas.</b>
                        ¡Y ahora, gracias a nuestra app, puedes adquirir todo lo que necesitas con solo un clic! Nos
                        adaptamos a la nueva era digital para que tus compras sean rápidas y sencillas. ¡Únete a la
                        diversión y simplifica tu día a día con Aloranges!<br />Somos tu proveedor de suministros
                        favorito, siempre estamos a solo un clic de distancia.<br />Estamos aquí para hacer tu vida más
                        fácil y divertida.<br /><b>¡Explora y disfruta!</b>
                    </template>
                </PresentationBanner>
            </v-col>
        </v-row>

        <!-- Este bloque usaba `width: 95%` inline, que no coincidia con el
             margen de ninguna otra seccion. Ahora comparte la misma caja. -->
        <div class="wrapper-app-banner home-section app-banner-caja">
                <v-row>
                    <v-col cols="12" class="d-flex d-sm-none justify-start">
                        <div class="ml-7 mt-7 mt-sm-0 d-flex flex-column justify-start align-start">
                            <h2 class="banner-title font-weight-bold mb-3">Descarga Nuestra <br />App Móvil</h2>
                        </div>
                    </v-col>
                    <v-col cols="12" sm="6" class="d-flex align-items-center" style="justify-content: center">
                        <img class="imgsize-cel" src="/public/assets/img/download-img.svg" />
                    </v-col>
                    <v-col cols="12" sm="6" class="justify-center d-none d-sm-flex">
                        <div class="ml-7 mt-7 mt-sm-0 d-flex flex-column justify-center align-start">
                            <h2 class="banner-title font-weight-bold mb-3">Descarga Nuestra <br />App Móvil</h2>
                            <div class="Wrapper-AppStore">
                                <a
                                    href="https://play.google.com/store/apps/details?id=com.aloranges&pcampaignid=web_share"
                                    target="_blank"
                                >
                                    <img class="imgsize-btn-dwn" src="public/assets/img/Grupo12233.png" />
                                </a>
                                <a href="https://apps.apple.com/co/app/aloranges/id6740248063" target="_blank">
                                    <img class="imgsize-btn-dwn ml-5" src="public/assets/img/Grupo12234.png" />
                                </a>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" class="d-flex d-sm-none justify-center align-center">
                        <div class="Wrapper-AppStore-responsive mb-7 mb-sm-0">
                            <v-row>
                                <v-col cols="6" class="pr-1 pl-10 pb-0">
                                    <a
                                        href="https://play.google.com/store/apps/details?id=com.aloranges&pcampaignid=web_share"
                                        target="_blank"
                                    >
                                        <img
                                            class="imgsize-btn-dwn"
                                            style="width: 100%; height: auto"
                                            src="public/assets/img/Grupo12233.png"
                                        />
                                    </a>
                                </v-col>
                                <v-col cols="6" class="pl-1 pr-10 pb-0">
                                    <a href="https://apps.apple.com/co/app/aloranges/id6740248063" target="_blank">
                                        <img
                                            class="imgsize-btn-dwn"
                                            style="width: 100%; height: auto"
                                            src="public/assets/img/Grupo12234.png"
                                        />
                                    </a>
                                </v-col>
                            </v-row>
                        </div>
                    </v-col>
                </v-row>
            </div>

        <v-row>
            <v-col cols="12" class="py-14">
                <CarouselBrands></CarouselBrands>
            </v-col>
        </v-row>
        <RecuperarPassCodigo v-model="showRecuperarPass" :email="this.$route.query.email" />
        <VerifyAccount v-model="showVerifyAccount" />
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { sliderSeeder } from "../seeders/products";

import BannerCategoryProduct from "../components/global/BannerCategoryProduct.vue";
import Carousel from "../components/global/Carousel.vue";
import CarouselActions from "../components/global/CarouselActions.vue";
import CustomButton from "../components/global/CustomButton.vue";
import LayoutNavbar from "../components/header/Navbar.vue";
import PolygonElement from "../components/global/PolygonElement.vue";
import PortfolioCard from "../components/global/PortfolioCard.vue";
import PresentationBanner from "../components/global/PresentationBanner.vue";
import SelectCustom from "../components/global/SelectCustom.vue";
import CustomInput from "../components/global/CustomInput.vue";
import CarouselSpaces from "../components/global/CarouselSpaces.vue";
import CarouselBrands from "../components/global/CarouselBrands.vue";
import RecuperarPassCodigo from "../components/auth/RecuperarPassCodigo.vue";
import VerifyAccount from "../components/auth/VerifyAccount.vue";
import SearchInput from "../components/global/SearchInput.vue";

export default {
    components: {
        BannerCategoryProduct,
        Carousel,
        CarouselActions,
        CustomButton,
        LayoutNavbar,
        PolygonElement,
        PortfolioCard,
        PresentationBanner,
        SelectCustom,
        CustomInput,
        CarouselSpaces,
        CarouselBrands,
        RecuperarPassCodigo,
        VerifyAccount,
        SearchInput
    },
    data() {
        return {
            showRecuperarPass: false,
            showVerifyAccount: false,
            // --- HERO: categorias que rotan en el titular ---
            heroIndex: 0,
            heroTimer: null,
            coloresHero: ["#f58634", "#2e9e5b", "#8a5a2b", "#d4351c", "#1f6feb", "#c2701c"],
            // Respaldo mientras la API responde
            heroCategoriasRespaldo: [
                { texto: "papelería", to: { name: "ShopPapeleria" }, color: "#f58634" },
                { texto: "aseo", to: { name: "ShopAseo" }, color: "#2e9e5b" },
                { texto: "cafetería", to: { name: "ShopCafeteria" }, color: "#8a5a2b" },
                { texto: "tecnología", to: { name: "ShopTecnologia" }, color: "#1f6feb" }
            ],
            itemsArray: [
            ],
            selectedCode: null,
            sliderSeeder,
            sliderItems: [
                {
                    src: "/public/assets/img/home/banner-home.png",
                    type: "image"
                }
            ],
            productsSeeder: [
                {
                    id: "1",
                    name: "'I'",
                    img: "/public/assets/img/home/i.png",
                    description: "Racionalización de procesos de fabricación"
                },
                {
                    id: "2",
                    name: "'DOVELA'",
                    description: "Cuidadosa selección de materiales",
                    img: "/public/assets/img/home/dovela1.png"
                },
                {
                    id: "3",
                    name: "'INTEGRACIÓN'",
                    description: "Ciclo de vida adaptable",
                    img: "/public/assets/img/home/dovela.png"
                }
            ]
        };
    },
    computed: {
        ...mapGetters("app", ["userLanguageObj", "allLanguages"]),
        // Categorias del hero: las reales del catalogo en cuanto llegan de la
        // API (con su imagen y enlace), y la lista estatica solo como respaldo
        // mientras cargan, para que el titular no arranque vacio.
        heroCategorias() {
            if (this.itemsArray && this.itemsArray.length) {
                return this.itemsArray.map((c, i) => ({
                    texto: c.title,
                    imagen: c.img,
                    to: c.to,
                    color: this.coloresHero[i % this.coloresHero.length]
                }));
            }
            return this.heroCategoriasRespaldo;
        },
        categoriaActiva() {
            return this.heroCategorias[this.heroIndex % this.heroCategorias.length];
        }
    },
    created() {
        if (this.$route.query.modal === "Password") {
            this.showRecuperarPass = true;
        }
        if (
            this.$route.query.modal == "VerifyAccount" ||
            this.$route.query.modal == "verifyaccount" ||
            this.$route.query.modal == "VerifyAccount"
        ) {
            this.showVerifyAccount = true;
        }
    },
    mounted() {
        this.getCategories();
        this.$vuetify.theme.dark = true;

        this.selectedCode = this.userLanguageObj.code;

        this.scrollToCenter();
        this.updateBreadcrumb();
        this.iniciarRotacion();
        // if(this.$route.query.modal == 'Password'){
        //     this.showRecuperarPass = true;
        // }
    },
    beforeDestroy() {
        this.detenerRotacion();
    },
    methods: {
        ...mapActions("app", ["setLanguage"]),
        // --- HERO ---
        iniciarRotacion() {
            // Respeta a quien haya pedido menos animacion en su sistema: para
            // esas personas el titular se queda quieto en la primera categoria.
            const sinMovimiento =
                window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
            if (sinMovimiento) return;

            this.detenerRotacion();
            this.heroTimer = setInterval(() => {
                this.heroIndex = (this.heroIndex + 1) % this.heroCategorias.length;
            }, 2200);
        },
        detenerRotacion() {
            if (this.heroTimer) clearInterval(this.heroTimer);
            this.heroTimer = null;
        },
        // Al posarse sobre un chip, el titular muestra esa categoria y la
        // rotacion se detiene: el usuario toma el control.
        fijarCategoria(i) {
            this.detenerRotacion();
            this.heroIndex = i;
        },
        reanudarRotacion() {
            this.iniciarRotacion();
        },
        agree() {
            if (this.$i18n.locale !== this.selectedCode) {
                this.setLanguage(this.selectedCode);
                window.location.reload();
            }
        },
        updateBreadcrumb() {
            const newItems = [{ text: "disabled", href: "/", disabled: true }];
            this.$store.dispatch("breadcrumb/setBreadcrumbItems", newItems);
        },
        getCategories() {
            this.call_api("get", "categories-home").then((res) => {
                if (res.data.success) {
                    this.itemsArray = res.data.data.map((category) => {
                        return {
                            title: category.name,
                            img: category.meta_image,
                            // `Shop + nombre` daba nombres de ruta que no
                            // siempre existen: "Botiquin" no tiene ruta propia
                            // y su enlace quedaba roto. La ruta dinamica
                            // /shop/:categorySlug sirve para todas.
                            // ShopShowApi espera el NOMBRE, no el slug.
                            to: {
                                name: "ShopDynamicCategory",
                                params: { categorySlug: category.name }
                            }
                        };
                    });
                }
            }).catch((err) => {
                console.log(err);
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
// El buscador del hero ocupa el ancho de su columna.
// Antes esta regla iba sin acotar y su `!important` pisaba el max-width del
// SearchInput en TODA la aplicacion, incluido el del header.
.hero .search-menu {
    width: 100%;
    max-width: 100% !important;
}

.center-items {
    display: flex;
    flex-direction: column;
    align-content: center;
    align-items: center;
}
.wrapper-app-banner {
    padding: 24px 0;
    background-position: center;
    background-size: contain;
    display: flex;
    flex-direction: row;
    align-items: center;
}

/* Caja del banner de la app: mismos bordes y fondo que antes iban inline. */
.app-banner-caja {
    border-radius: 24px;
    background-color: #f4f9ec;
}
/* .hero-banner-img se define ahora en el bloque scoped, junto al resto del
   hero, para que no se filtre al resto de la aplicacion. */
.imgsize-cel {
    width: 95%;
    max-width: 420px;
    height: auto;
}
.imgsize-btn-dwn {
    width: 100%;
    max-width: 220px;
    height: auto;
}
.title-banner-dwn {
    font-size: 25px;
    font-weight: 700;
}
</style>

<style lang="scss" scoped>
/* =========================================================
   RITMO DE LA PAGINA
   Cada seccion traia su propio margen (mx-2, ma-2, width:95%, o
   ninguno), asi que unas quedaban a 335px y otras a 375px: no habia
   un margen izquierdo comun y la pagina se veia descuadrada al bajar.
   Esta clase les da a todas la misma caja y la misma separacion.
   ========================================================= */
.home-section {
    margin-left: 8px;
    margin-right: 8px;
    margin-bottom: 32px;

    @include respond-up("sm") {
        margin-left: 24px;
        margin-right: 24px;
        margin-bottom: 48px;
    }
}

/* =========================================================
   HERO
   ========================================================= */
.hero {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    /* Respira respecto al header con el MISMO ritmo que separa las secciones
       entre si (32/48px): antes nacia pegado (0px, y el margen -12px del
       v-row hasta lo metia debajo del header). El v-container ya aporta 12px
       de padding, por eso aqui van 20/36 y no 32/48. */
    margin-top: 20px;

    @include respond-up("sm") {
        margin-top: 36px;
    }
    /* Antes era un plano #f5cea6. El degradado da profundidad sin recargar. */
    background: linear-gradient(135deg, #fdf0e2 0%, #f8d9b6 55%, #f5cea6 100%);
    padding: clamp(20px, 4vw, 44px) clamp(16px, 3vw, 36px);
}

/* Manchas suaves de fondo, puramente decorativas. */
.hero-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(42px);
    opacity: 0.5;
    pointer-events: none;

    &--1 {
        width: 320px;
        height: 320px;
        top: -110px;
        right: -70px;
        background: rgba(245, 134, 52, 0.45);
    }

    &--2 {
        width: 260px;
        height: 260px;
        bottom: -120px;
        left: -80px;
        background: rgba(255, 255, 255, 0.75);
    }
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.75);
    color: #7a3e0d;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    padding: 6px 12px;
    border-radius: 999px;
    margin-bottom: 14px;

    i {
        font-size: 15px;
        color: #f58634;
    }
}

.hero-title {
    /* Escala fluida acotada: antes 46px en movil (con una frase larguisima)
       y 58px ya desde 600px, dentro de una columna de ~350px. */
    font-size: clamp(28px, 5.2vw, 54px);
    line-height: 1.12;
    font-weight: 800;
    color: #25292e;
    letter-spacing: -0.5px;
    margin-bottom: 10px;
    text-align: center;

    @include respond-up("sm") {
        text-align: left;
    }
}

/* Palabra que cambia. Reserva su propio renglon para que el titular no
   "salte" cada vez que entra una palabra mas larga o mas corta. */
.hero-rotator {
    display: block;
    /* Ajustado al alto real de la palabra. Antes 1.15em dejaba un hueco
       visible entre la palabra que rota y "para tu empresa". */
    min-height: 1em;
    line-height: 1;
    margin: 2px 0;
}

.hero-rotator-word {
    display: inline-block;
    font-weight: 800;
    line-height: 1.05;
    background: rgba(255, 255, 255, 0.55);
    border-radius: 10px;
    /* El relleno vertical era el que separaba de "para tu empresa" */
    padding: 0 10px 2px;
    /* La animacion se dispara al insertarse el elemento (cambia el :key).
       No hay estado que pueda quedarse a medias. */
    animation: hero-word-in 0.4s ease both;
}

@keyframes hero-word-in {
    from {
        opacity: 0;
        transform: translateY(0.45em);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-sub {
    font-size: clamp(14px, 1.6vw, 19px);
    line-height: 1.5;
    color: #4a4f55;
    margin-bottom: 18px;
    text-align: center;

    @include respond-up("sm") {
        text-align: left;
    }
}

/* Accesos directos a categoria */
/* Rejilla de categorias: 2 columnas en movil, mas a medida que hay sitio.
   Es el acceso al catalogo, asi que se ve entera sin desplazar. */
.hero-cats {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    margin-top: 18px;

    @include respond-up("sm") {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    @include respond-up("md") {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }
}

.hero-cat {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 56px;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.82);
    color: #25292e;
    text-decoration: none;
    font-weight: 700;
    transition: transform 0.18s ease, background-color 0.18s ease, box-shadow 0.18s ease;

    &:hover,
    &:focus-visible,
    &.is-active {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.13);
    }

    /* Desde md la tarjeta se apila: imagen arriba, nombre debajo */
    @include respond-up("md") {
        flex-direction: column;
        gap: 6px;
        text-align: center;
        padding: 12px 8px;
    }
}

.hero-cat-img {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    /* Recorte circular. Las imagenes de categoria son un circulo de color
       inscrito en un lienzo CUADRADO con fondo pegado (negro en Papeleria,
       blanco en otras): mostradas tal cual se veian las esquinas feas.
       cover + 50% recorta exactamente el circulo y esconde el lienzo.
       Sin fondo propio: el circulo de la imagen ya trae su color. */
    object-fit: cover;
    border-radius: 50%;

    @include respond-up("md") {
        width: 56px;
        height: 56px;
    }
}

.hero-cat-img--vacia {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #c3985f;
    font-size: 20px;
    /* Solo el marcador sin imagen necesita fondo propio */
    background: rgba(255, 255, 255, 0.7);
}

.hero-cat-nombre {
    font-size: 12.5px;
    line-height: 1.25;
    /* Nombres como "Seguridad industrial" no caben en una linea a 2 columnas */
    overflow-wrap: anywhere;

    @include respond-up("md") {
        font-size: 13px;
    }
}


.hero-media {
    position: relative;
    z-index: 1;
    align-items: center;
    justify-content: center;

    /* En movil la imagen es solo decorativa y se llevaba 179px, empujando las
       categorias fuera de la primera pantalla. El cliente pide ver el catalogo
       de entrada, asi que ahi manda el contenido util. */
    @include respond-down("sm") {
        display: none !important;
    }
}

.hero-banner-img {
    width: 100%;
    /* En movil la imagen se llevaba 271px y empujaba el hero a ocupar el 100%
       de la pantalla, dejando el resto de la home fuera de vista. */
    max-width: 200px;
    height: auto;
    margin: 0 auto;
    display: block;
    animation: hero-flotar 6s ease-in-out infinite;

    @include respond-up("sm") {
        max-width: 320px;
    }

    @include respond-up("md") {
        max-width: 420px;
    }
}

@keyframes hero-flotar {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-12px);
    }
}

/* Texto solo para lectores de pantalla: mantiene el mensaje completo
   (todas las categorias) accesible aunque visualmente roten. */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Sin animaciones para quien las haya desactivado en su sistema. */
@media (prefers-reduced-motion: reduce) {
    .hero-banner-img {
        animation: none;
    }
    .hero-rotator-word {
        animation: none;
    }
    .hero-chip {
        transition: none;
    }
}

.home {
    &-main {
        &-description {
            color: black;
            font-weight: 400;
            font-size: 18px;
            line-height: 24px;
            text-align: center;
            @media (min-width: 600px) {
                font-size: 28px;
                line-height: 34px;
                text-align: left;
            }
        }
        &-title {
            font-size: 46px;
            line-height: 54px;
            font-weight: 600;
            text-align: center;
            max-width: 100%;
            @media (min-width: 600px) {
                line-height: 62px;
                font-size: 58px;
                text-align: left;
            }
        }
        &-carousel {
            height: 80vh !important;
            max-height: 786px;

            &::v-deep {
                .v-carousel__item {
                    height: 100%;
                }
            }
        }
    }

    &-portfolio {
        &-wrap {
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 1rem;
            height: 100%;
            min-height: 560px;
            display: flex;
            align-items: center;
        }

        &-title {
            @media (max-width: 600px) {
                width: 60%;
            }
        }
    }
}

.temp {
    padding: 0 15%;
}
h3 {
    font-size: 16px;
    font-weight: 700;
    text-align: center;
    @media (min-width: 600px) {
        font-size: 35px;
        font-weight: 500;
        text-align: left;
    }
}
.preambulo-text {
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    @media (min-width: 600px) {
        font-size: 22px;
        text-align: left;
    }
}
.theme--dark {
    .preambulo .rounded-section {
        background-color: #18191a;
    }
}

.imgsize-cel {
    width: 95%;
    max-width: 400px;
    height: auto;
}

.Wrapper-AppStore {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.Wrapper-AppStore-responsive {
    display: flex;
    flex-direction: column;
    width: 100%;
}

// .banner-title estaba definida DOS veces en este mismo archivo con valores
// distintos (28px aqui, 35px mas abajo). Ganaba siempre la segunda, asi que el
// movil nunca renderizaba los 28px previstos. Se unifica en una sola regla,
// con escala fluida acotada.
.banner-title {
    font-size: clamp(26px, 4.5vw, 52px);
    font-weight: 700;
    line-height: 1.15;
    text-align: center;

    @include respond-up("sm") {
        text-align: left;
    }
}

.v-select {
    text-transform: uppercase;
}

.border-style-banner {
    border-radius: 20px;
}

.bg-orange {
    background: #f58634;
}
.bg-orange-ligth {
    background: #f5cea6;
}
.bg-green-ligth {
    background: #f3f9ec;
}
.center-flex {
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media (min-width: 835px) {
    .container {
        max-width: 1920px;
    }
}

.div-map {
    width: 100%;
}

.map {
    border: 1px solid #59595a;
    border-radius: 400px;
    padding: 2% 15%;
}

.img-map {
    border-radius: 50%;
}

@media (max-width: 834px) {
    .div-map {
        width: 100%;
    }

    .map {
        border: 1px solid #59595a;
        border-radius: 50%;
        padding: 20% 0;
    }
}
.Wrapper-AppStore {
    display: flex;
}
.Wrapper-AppStore-responsive {
    display: flex;
    flex-direction: column;
}
.wrapper-dwn-app {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
// (definicion duplicada de .banner-title eliminada: ver la regla unificada
//  mas arriba en este mismo bloque)
</style>
