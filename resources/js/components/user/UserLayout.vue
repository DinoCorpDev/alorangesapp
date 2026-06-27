<template>
    <v-container class="user-layout" fluid>
        <v-row>
            <v-col lg="3" class="user-layout-sidebar d-none d-lg-block" style="background-color: #fafcfc">
                <SideMenu class="d-none d-sm-block" />
                <!-- <div class="user-layout-button d-sm-none">
                    <h6>Perfil</h6>
                    <CustomButton @click.stop="showMenu" color="orange">
                        <BarsIcon />
                        <span>{{ $t("Menu") }}</span>
                    </CustomButton>
                </div> -->
            </v-col>
            <v-col cols="12" lg="9" class="user-layout-content" style="background-color: #fafcfc">
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
        background-color: #7c7c7c;

        @media (min-width: 600px) {
            background-color: #f5f5f5;
        }

        @media (min-width: 1264px) {
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

        h6 {
            color: #ffffff;
            text-transform: uppercase;
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
            @media (min-width: 1264px) {
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
