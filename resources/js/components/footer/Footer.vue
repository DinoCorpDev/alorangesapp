<template>
    <v-footer class="auth-footer">
        <!-- El v-row colgaba directo del v-footer. Los v-row de Vuetify llevan
             margenes de -12px que deben compensarse con el padding de un
             contenedor; sin el, el footer sobresalia y provocaba 8px de scroll
             horizontal en TODAS las paginas del sitio. -->
        <v-container fluid class="pa-0">
            <v-row class="pt-5">
            <!-- <v-col md="1"/> -->
            <v-col cols="12" md="5" class="d-flex justify-start justify-md-center align-center">
                <div class="footer-intro pl-md-5 mr-md-16">
                    <LogoAlorange class="mb-1" />
                    <p class="footer-intro-text">
                        <b>¡Estamos aquí para ayudarte!</b><br />¿Tienes compras, cotizaciones, preguntas o inquietudes?
                        ¡No dudes en llamarnos o escribirnos! Nuestro equipo de agentes comerciales está listo para
                        atenderte de lunes a viernes, de 8:00 am a 6:00 pm.<br />¿Fuera de horario? ¡No hay problema!
                        Déjanos tu mensaje por Teléfono o WhatsApp
                        <a class="footer-phone-link" href="https://wa.me/573174420109" target="_blank">
                            +57 3174420109
                        </a>
                        o envíanos un correo a ventas5@aloranges.com, y te responderemos en un abrir y cerrar de
                        ojos.<br /><b>¡Tu satisfacción es nuestra misión!</b>
                    </p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/share/15iCZJt5Dq/?mibextid=wwXIfr" target="_blank">
                            <img class="redes" src="../icons/facebook.svg" alt="Facebook" />
                        </a>
                        <a href="https://wa.me/573174420109" target="_blank">
                            <img class="redes" src="../icons/whatsapp.svg" alt="whatsapp" />
                        </a>
                        <a href="https://www.instagram.com/aloranges.co?igsh=bnQzMGU0MTQycndo" target="_blank">
                            <img class="redes" src="../icons/instagram.svg" alt="instagram" />
                        </a>
                    </div>
                </div>
            </v-col>
            <v-col cols="12" md="4" class="list-footer align-start pb-0 pb-md-3 pl-md-11">
                <router-link to="/information/proteccionDatos">Política de Protección de Datos</router-link>
                <router-link to="/information/cambiosDevoluciones">Cambios y Devoluciones</router-link>
                <router-link to="/information/tiempoEnvios">Tiempo y costo de envío</router-link>
            </v-col>
            <v-col cols="12" md="3" class="list-footer align-start pt-0 pt-md-3 pl-md-16">
                <a @click="showAccount">Mi cuenta</a>
                <a @click="showModalRegister">Regístrate</a>
                <a @click="showModalRecuperarPass">¿Olvidó su clave?</a>
            </v-col>
            <!-- Este bloque estaba DUPLICADO en el DOM: una version
                 `d-none d-md-flex` y otra `d-flex d-md-none` con los mismos
                 textos y distintos tamanos. Se renderizaban las dos y habia
                 que mantener los cambios por partida doble. Ahora es uno solo
                 que se reordena con las props `order` de Vuetify. -->
            <v-col cols="12" class="footer-legal">
                <v-row align="center" class="footer-legal-row">
                    <v-col cols="12" order="1" md="auto" order-md="2" class="pb-0 pb-md-3">
                        <p class="footer-legal-text mb-0">
                            <b>Copyright © 2022 Aloranges.com.</b>
                            <br class="d-md-none" />
                            Todos los derechos reservados.
                        </p>
                    </v-col>
                    <v-col cols="6" order="2" md="auto" order-md="1" class="pt-0 pt-md-3 d-flex align-center">
                        <img
                            src="../icons/Logo_fondo_Emprender_blanco.png"
                            alt="Fondo Emprender"
                            class="footer-logo-emprender"
                        />
                    </v-col>
                    <v-col
                        cols="6"
                        order="3"
                        md="auto"
                        order-md="3"
                        class="pt-0 pt-md-3 d-flex align-center justify-end justify-md-start"
                    >
                        <p class="d-flex align-center mb-0">
                            Powered by
                            <img src="../icons/DinoLabs-logo.svg" alt="DinoLabs" class="footer-logo-dinolabs" />
                        </p>
                    </v-col>
                </v-row>
            </v-col>
            </v-row>
        </v-container>

        <ModalRegister v-model="showRegister" />
        <RecuperarPass v-model="showRecuperarPass" />
    </v-footer>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";
import WorldGlobeIcon from "../icons/WorldGlobe.vue";
import LogoAlorange from "../icons/LogoAlorange.vue";
import DinoLabs from "../icons/DinoLabs.vue";
import ModalRegister from "../../components/user/ModalRegister.vue";
import RecuperarPass from "../../components/auth/RecuperarPass.vue";

export default {
    name: "FooterCustom",
    components: {
        WorldGlobeIcon,
        LogoAlorange,
        DinoLabs,
        ModalRegister,
        RecuperarPass
    },
    data() {
        return {
            showRegister: false,
            showRecuperarPass: false
        };
    },
    computed: {
        ...mapState("app", ["authFooterLinks"]),
        ...mapGetters("auth", ["userIsLoggedIn"])
    },
    methods: {
        ...mapMutations("auth", ["showLoginDialog"]),

        showAccount() {
            if (this.userIsLoggedIn) {
                this.$router.push({ name: "Cart" });
            } else {
                this.showLoginDialog(true);
            }
        },
        showModalRegister() {
            this.showRegister = true;
        },
        showModalRecuperarPass() {
            this.showRecuperarPass = true;
        }
    }
};
</script>

<style lang="scss" scoped>
.redes {
    width: 50px;

    &:hover {
        opacity: 0.5;
    }
}

p {
    color: white;
    font-weight: 100;
}

.list-footer {
    display: flex;
    flex-direction: column;
    justify-content: center;

    a {
        margin-bottom: 15px;
        font-size: 17px;
        color: white;
        font-weight: 100;

        &:hover {
            text-decoration: underline;
        }
    }
}

.auth-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background-color: #3a3f43;
}

// Se eliminaron ~70 lineas de CSS (`&-copyright`, `&-location`, `&-link a`,
// `&-copyright-wrap`, `&-links`) cuyas clases no existen en el template: sus
// media queries no llegaban a ejecutarse nunca. Tambien sobraba el
// `@media (max-width: 599px) { flex-direction: column }` sobre .auth-footer,
// que no tenia efecto porque su unico hijo directo es un contenedor.

.footer-intro {
    max-width: 480px;
}

.footer-intro-text {
    font-size: var(--font-size-body1);
}

.footer-phone-link {
    font-weight: 700;
    color: white;
    font-size: var(--font-size-body1);
    text-decoration: underline;
    white-space: nowrap;
}

.footer-social {
    display: flex;
    gap: 10px;
    justify-content: flex-start;
    // Permite que los iconos bajen de linea si se anaden mas redes.
    flex-wrap: wrap;
}

.footer-legal-row {
    // En movil se apila; desde md se reparte en una sola fila.
    @include respond-up("md") {
        justify-content: space-around;
        flex-wrap: nowrap;
    }
}

.footer-legal-text {
    font-size: var(--font-size-body1);
}

.footer-logo-emprender {
    // Antes: 180px en el bloque movil y 266px en el de escritorio, en dos
    // nodos distintos del DOM. Ahora es una sola imagen que escala.
    max-width: 180px;
    width: 100%;
    height: auto;

    @include respond-up("md") {
        max-width: 266px;
    }
}

.footer-logo-dinolabs {
    max-width: 60px;
    height: auto;
}
</style>
