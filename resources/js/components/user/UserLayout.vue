<template>
    <v-container class="user-layout" fluid>
        <v-row>
            <!-- Acceso al menu de la cuenta por debajo de lg.
                 El sidebar es `d-none d-lg-block`, asi que desaparece bajo
                 1264px. El boton que abria el drawer estaba comentado Y ademas
                 vivia DENTRO de ese mismo contenedor oculto, por lo que aunque
                 se descomentara seguiria sin verse: en movil y tablet no habia
                 forma de llegar a Pedidos, Perfil, Mi lista ni Cerrar sesion.
                 Ahora vive fuera del sidebar y es visible justo donde este no. -->
            <v-col cols="12" class="d-lg-none pb-0">
                <div class="user-layout-button">
                    <h6>{{ $t("Perfil") }}</h6>
                    <CustomButton @click.stop="showMenu" color="orange">
                        <BarsIcon />
                        <span>{{ $t("Menu") }}</span>
                    </CustomButton>
                </div>
            </v-col>
            <v-col lg="3" class="user-layout-sidebar d-none d-lg-block">
                <SideMenu />
            </v-col>
            <v-col cols="12" lg="9" class="user-layout-content">
                <v-container>
                    <v-row>
                        <v-col cols="12" lg="12">
                            <router-view />
                        </v-col>
                    </v-row>
                </v-container>
            </v-col>
        </v-row>
        <v-navigation-drawer v-model="userNavDrawerActive" fixed temporary right style="z-index: 999">
            <SideMenu class="pa-3" />
        </v-navigation-drawer>
    </v-container>
</template>

<script>
import { mapGetters, mapState } from "vuex";

import CustomButton from "../../components/global/CustomButton.vue";
import SideMenu from "./SideMenu";
import BarsIcon from "../icons/BarsIcon.vue";

export default {
    components: {
        CustomButton,
        SideMenu,
        BarsIcon
    },
    data() {
        return {
            userNavDrawerActive: false
        };
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("app", ["previewAvatar"])
    },
    methods:{
        showMenu(){
            this.userNavDrawerActive = !this.userNavDrawerActive;
        }
    }
};
</script>

<style lang="scss" scoped>
.user-layout {
    // min-height: 85vh;

    &-sidebar {
        // Solo se muestra desde lg, asi que no necesita los dos fondos que
        // tenia para movil/tablet.
        background-color: #f5f5f5;

        @include respond-up("lg") {
            &.col-lg-3 {
                flex: 0 0 22%;
                max-width: 22%;
            }
        }
    }

    &-button {
        display: flex;
        justify-content: space-between;
        align-items: center;
        // Permite que titulo y boton se reordenen en pantallas muy estrechas.
        flex-wrap: wrap;
        gap: 0.5rem;
        background-color: #f5f5f5;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;

        h6 {
            // Era blanco sobre el fondo gris oscuro del sidebar; fuera de el
            // habria quedado invisible.
            color: #25292e;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .v-btn {
            background-color: #161616;
            letter-spacing: 0;
            padding: 0 12px;

            &::v-deep {
                .v-btn__content {
                    display: flex;
                    gap: 0.5rem;
                }
            }

            svg {
                width: 24px;
                height: 24px;
            }
        }
    }

    &-content {
        background-color: #fafcfc;

        &.col-lg-9 {
            @include respond-up("lg") {
                flex: 0 0 78%;
                max-width: 78%;
            }
        }
    }

    &::v-deep {
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
}

.list-cont {
    background: #f5f5f5 !important;
}

.v-tabs-slider {
    width: 0 !important;
    height: 0 !important;
}
</style>
