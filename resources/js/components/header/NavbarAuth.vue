<template>
    <div>
        <v-app-bar
            ref="layoutNavbar"
            class="layout-navbar-auth"
            :color="$vuetify.theme.dark ? '#000000' : '#FAFCFC'"
            elevation="0"
            prominent
            dense
            shrink-on-scroll
            :fixed="headerFixed"
        >
            <v-container class="pa-0 fill-height d-flex justify-space-between" fluid>
                <router-link :to="{ name: 'Home2' }" class="layout-navbar-auth-brand">
                    <LogoAloranges class="d-none d-md-flex" />
                    <LogoAlorange class="d-flex d-md-none" />
                </router-link>

                <SearchInput
                    class="d-none d-sm-flex"
                    :showInput="false"
                    :placeholder="'Escribe lo que buscas'"
                    style="max-width: 500px"
                />

                <div class="header-actions">
                    <CustomButton class="d-flex d-sm-none" color="orange3" :to="{ name: 'Search' }">
                        <Search class="cart-icon ml-sm-2" style="margin-bottom: 4px" />
                    </CustomButton>
                    <CustomButton color="orange3" :to="{ name: 'Shop' }">
                        <span class="d-none d-sm-flex">Tienda</span
                        ><Cart class="cart-icon ml-sm-2" style="margin-bottom: 4px" />
                    </CustomButton>
                    <div class="layout-navbar-auth-nav">
                        <DoubleButton />
                        <div class="d-flex d-lg-none" v-if="userIsLoggedIn">
                            <CustomButton v-if="userIsLoggedIn" @click.stop="toggleMenu" color="orange3">
                                <BurgerMenu />
                            </CustomButton>
                        </div>
                        <div style="display: none;">
                            <ToggleMenu  />
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
                                    <v-card class="pa-10" style="position: relative">
                                        <v-card-text
                                            style="
                                                display: flex;
                                                flex-direction: column;
                                                justify-content: center;
                                                align-items: center;
                                            "
                                        >
                                        <LogoAlorange />
                                        <h3
                                                class="pa-12"
                                                style="
                                                    font-size: 35px;
                                                    font-weight: 500px;
                                                    line-height: 42px;
                                                    text-align: center;
                                                    color: black;
                                                "
                                            >
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
                                        <v-btn
                                            class="logout-icon-esc"
                                            style="position: absolute; top: 15px; right: 15px"
                                            @click="dialog.value = false"
                                        >
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
import { mapGetters, mapState } from "vuex";

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
        ToggleMenu
    },
    data() {
        return {
            headerFixed: false,
            logoLarge: false,
            scrollThreshold: 70,
            dialogResposive: false,
            userNavDrawerActive: false
        };
    },
    computed: {
        ...mapGetters("auth", ["userShortName"]),
        ...mapGetters("auth", ["userIsLoggedIn"]),
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

            this.headerFixed = currentScroll >= this.scrollThreshold;
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
        toggleMenu(){
            this.$emit('toggleMenu');
        }
    }
};
</script>

<style lang="scss" scoped>
.header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
}

.container {
    gap: 0.65rem;

    @media (min-width: 960px) {
        gap: 1rem;
    }

    @media (max-width: 600px) {
        flex-wrap: nowrap;
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
}
.layout-navbar-auth {
    min-height: 60px;
    z-index: 10;
    box-shadow: rgba(0, 0, 0, 0.16) 0px 4px 6px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px !important;
    @media (max-width: 960px) {
        max-height: 60px;
    }

    &::v-deep {
        .v-toolbar__content {
            min-height: 60px;
            padding: 0 12px;

            @media (max-width: 960px) {
                max-height: 60px;
            }
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
