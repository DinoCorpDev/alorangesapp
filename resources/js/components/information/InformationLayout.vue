<template>
    <div class="user-layout">
        <v-container fluid>
            <v-row>
                <v-col lg="3" class="user-layout-sidebar d-lg-block">
                    <SideMenu2 class="d-none d-sm-block" />
                    <div class="d-sm-none btn-menu">
                        <CustomButton @click="drawer = !drawer" :dark="true" text="MENU" icon="la-ellipsis-v" />
                    </div>
                </v-col>
                <v-col cols="12" lg="9" class="user-layout-content">
                    <router-view />
                </v-col>
                <v-navigation-drawer v-model="drawer" class="drawerNot list-cont" absolute temporary right>
                    <v-list class="list-cont" center vertical v-model="tab">
                        <v-list-item class="data-user">
                            <div class="div-img">
                                <div>
                                    <v-avatar size="50">
                                        <img
                                            :src="previewAvatar || currentUser.avatar"
                                            @error="imageFallback($event)"
                                            class="border border-4"
                                        />
                                    </v-avatar>
                                </div>
                            </div>
                            <p class="black--text text-uppercase bold mt-3 name-font">INFORMACION</p>
                        </v-list-item>
                        <div class="divider"></div>

                        <v-list-item>
                            <CustomButton
                                block
                                class="mb-3 mt-3"
                                color="nero"
                                text="Terminos y condiciones"
                                :to="{ name: 'TerminosCondiciones' }"
                            />
                        </v-list-item>
                        <v-list-item>
                            <CustomButton
                                block
                                class="mb-3"
                                color="nero"
                                text="Poliza de garantia"
                                :to="{ name: 'PolizaGarantia' }"
                            />
                        </v-list-item>
                        <v-list-item>
                            <CustomButton
                                block
                                class="mb-3"
                                color="nero"
                                text="Pacto ambiental"
                                :to="{ name: 'PactoAmbiental' }"
                            />
                        </v-list-item>
                        <v-list-item>
                            <CustomButton
                                block
                                class="mb-3"
                                color="nero"
                                text="Logistica de envio"
                                :to="{ name: 'LogisticaEnvio' }"
                            />
                        </v-list-item>
                        <v-list-item>
                            <CustomButton
                                block
                                class="mb-3"
                                color="nero"
                                text="Privacidad y cookies"
                                :to="{ name: 'PrivacidadCokies' }"
                            />
                        </v-list-item>
                    </v-list>
                </v-navigation-drawer>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";

import CustomButton from "../../components/global/CustomButton.vue";
import SideMenu2 from "./SideMenu2";

export default {
    components: {
        SideMenu2,
        CustomButton
    },
    data: () => ({
        drawer: false,
        group: null
    }),

    watch: {
        group() {
            this.drawer = false;
        }
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("app", ["previewAvatar"])
    }
};
</script>

<style lang="scss" scoped>
.divider {
    background-color: #e4e4e4;
    width: 88%;
    height: 2px;
    margin-left: 15px;
}

.name-font {
    font-size: 12px;
}

.user-layout {
    &-sidebar {
        background-color: #f5f5f5;
    }

    &-content {
        background-color: #fafcfc;
    }
}

.data-user {
    display: flex;
    height: 35% !important;
    margin: 10px 0;
}

.list-cont {
    background: #f5f5f5 !important;
}

.v-tabs-slider {
    width: 0 !important;
    height: 0 !important;
}

.div-img {
    background-color: #dfdfdf;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    margin-right: 15px;
    justify-content: center;
    align-items: center;
    padding: 35px;
}

.btn-menu {
    justify-content: flex-end;
    display: flex;
}
</style>
