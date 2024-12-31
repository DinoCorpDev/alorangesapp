<template>
    <v-footer class="auth-footer">
        <v-row class="pt-5">
            <!-- <v-col md="1"/> -->
            <v-col cols="12" md="5" class="d-flex justify-start justify-md-center align-center">
                <div style="max-width: 480px" class="pl-md-5 mr-md-16">
                    <LogoAlorange class="mb-1" />
                    <p style="font-size: 15px">
                        <b>¡Estamos aquí para ayudarte!</b><br>¿Tienes compras, cotizaciones, preguntas o inquietudes? ¡No dudes
                        en llamarnos o escribirnos! Nuestro equipo de agentes comerciales está listo para atenderte de
                        lunes a viernes, de 8:00 am a 6:00 pm.<br>¿Fuera de horario? ¡No hay problema! Déjanos tu mensaje
                        por WhatsApp o envíanos un correo a ventas5@aloranges.com, y te responderemos en un abrir y
                        cerrar de ojos.<br><b>¡Tu satisfacción es nuestra misión!</b>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: flex-start">
                        <a href="#"><img src="../icons/facebook.svg" alt="Facebook" style="width: 50px" /></a>
                        <a href="#"><img src="../icons/whatsapp.svg" alt="whatsapp" style="width: 50px" /></a>
                        <a href="#"><img src="../icons/instagram.svg" alt="instagram" style="width: 50px" /></a>
                    </div>
                </div>
            </v-col>
            <v-col cols="12" md="4" class="list-footer align-start pb-0 pb-md-3 pl-md-11">
                <router-link to="/information/proteccionDatos">Política de Protección de Datos</router-link>
                <router-link to="/information/cambiosDevoluciones">Cambios y Devoluciones</router-link>
                <router-link to="/information/tiempoEnvios">Tiempo y costo de envío</router-link>
            </v-col>
            <v-col cols="12" md="3" class="list-footer align-start pt-0 pt-md-3 pl-md-16">
                <router-link to="#">Mi cuenta</router-link>
                <router-link to="#">Regístrate</router-link>
                <router-link to="#">¿Olvidó su clave?</router-link>
            </v-col>
            <v-col cols="12" class="d-none d-md-flex align-center" style="justify-content: space-around" >
                <img src="../icons/Logo_fondo_Emprender_blanco.png" alt="" style="max-width: 266px; height: auto" />
                <p style="font-size: 13px; margin-bottom: 0">
                    <b>Copyright © 2022 Aloranges.com.</b> Todos los derechos reservados.
                </p>
                <p class="d-flex align-center mb-0">
                    Powered by <img src="../icons/DinoLabs-logo.svg" style="max-width: 60px" />
                </p>
            </v-col>
            <v-col cols="12" class="d-flex d-md-none pa-0">
                <v-row class="pt-5">
                    <v-col cols="12" class="pl-6 pb-0">
                        <p style="font-size: 17px; margin-bottom: 0">
                            <b>Copyright © 2022 Aloranges.com.</b><br />Todos los derechos reservados.
                        </p>
                    </v-col>
                    <v-col cols="6" class="pt-0 d-flex align-center">
                        <img
                            src="../icons/Logo_fondo_Emprender_blanco.png"
                            alt=""
                            style="max-width: 180px; height: auto"
                        />
                    </v-col>
                    <v-col cols="6" class="d-flex align-center justify-end pr-5 pt-0">
                        <p class="mb-0 align-center justify-center" style="display: flex">
                            Powered by <img src="../icons/DinoLabs-logo.svg" style="max-width: 60px" />
                        </p>
                    </v-col>
                </v-row>
            </v-col>
            <v-col md="1" />
        </v-row>
    </v-footer>
</template>

<script>
import { mapState } from "vuex";
import WorldGlobeIcon from "../icons/WorldGlobe.vue";
import LogoAlorange from "../icons/LogoAlorange.vue";
import DinoLabs from "../icons/DinoLabs.vue";

export default {
    name: "FooterCustom",
    components: {
        WorldGlobeIcon,
        LogoAlorange,
        DinoLabs
    },
    computed: {
        ...mapState("app", ["authFooterLinks"])
    }
};
</script>

<style lang="scss" scoped>
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

    @media (max-width: 599px) {
        flex-direction: column;
    }

    // &.theme--dark {
    //     background-color: #18191a;
    // }

    &-copyright,
    &-location,
    &-link a {
        font-size: var(--font-size-body1);
        font-weight: 500;
        letter-spacing: 0;
        line-height: 1;
        text-transform: uppercase;

        @media (min-width: 960px) {
            font-weight: 600;
        }
    }

    &-location {
        display: flex;
        align-items: center;
    }

    &-copyright-wrap,
    &-links {
        gap: 1.5rem;

        @media (max-width: 599px) {
            width: 100%;
        }

        @media (min-width: 960px) {
            gap: 3rem;
        }

        @media (min-width: 1264px) {
            gap: 5rem;
        }
    }

    &-copyright-wrap {
        display: flex;
        align-items: center;

        @media (max-width: 599px) {
            justify-content: space-between;
            margin-bottom: 1rem;
        }
    }

    &-links {
        display: flex;
        padding: 0;
        list-style: none;

        @media (max-width: 599px) {
            justify-content: space-between;
        }
    }
}
</style>
