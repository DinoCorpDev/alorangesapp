<template>
    <v-menu class="toggle-menu" eager offset-y min-width="280" :close-on-content-click="false">
        <template v-slot:activator="{ on, attrs }">
            <v-btn class="toggle-menu-button" v-bind="attrs" v-on="on" depressed :ripple="false" plain>
                <MenuIcon />
            </v-btn>
        </template>
        <v-list class="toggle-menu-nav" flat>
            <template v-if="!navElementExpanded">
                <v-list-item>
                    <v-list-item-title class="text-uppercase">
                        {{ $t("Menu") }}
                    </v-list-item-title>
                </v-list-item>
                <v-divider />
            </template>

            <template v-for="item in items">
                <v-list-group :key="`list-${item.key}`" no-action @click="setList">
                    <template v-slot:activator>
                        <v-list-item :ripple="false">
                            <v-list-item-icon>
                                <component :is="item.icon" />
                            </v-list-item-icon>
                            <v-list-item-title>
                                <template v-if="!navElementExpanded">
                                    {{ $t(item.title) }}: {{ $t(item.titleValue) }}
                                </template>
                                <template v-else>
                                    {{ $t(item.title) }}
                                </template>
                            </v-list-item-title>
                        </v-list-item>
                    </template>
                    <template v-slot:appendIcon>
                        <RightArrowIcon />
                    </template>
                    <v-item-group v-if="item.key == 'theme'">
                        <template v-for="item in themeItems">
                            <v-divider :key="`divider-${item.label}`" />
                            <v-list-item :key="item.label">
                                <CustomCheckbox
                                    :inputValue="item.code"
                                    :label="$t(item.label)"
                                    name="selectedTheme"
                                    type="radio"
                                    v-model="selectedTheme"
                                />
                            </v-list-item>
                        </template>
                    </v-item-group>
                    <v-item-group v-if="item.key == 'lang'">
                        <template v-for="lang in allLanguages">
                            <v-divider :key="`divider-${lang.name}`" />
                            <v-list-item :key="lang.name">
                                <CustomCheckbox
                                    :inputValue="lang.code"
                                    :label="$t(lang.name)"
                                    name="selectedLang"
                                    type="radio"
                                    v-model="selectedLang"
                                />
                            </v-list-item>
                        </template>
                    </v-item-group>
                    <v-item-group v-if="item.key == 'country'">
                        <template v-for="country in allCountries">
                            <v-divider :key="`divider-${country.name}`" />
                            <v-list-item :key="country.name">
                                <CustomCheckbox
                                    :inputValue="country.code"
                                    :label="$t(country.name)"
                                    name="selectedCountry"
                                    type="radio"
                                    v-model="selectedCountry"
                                />
                            </v-list-item>
                        </template>
                    </v-item-group>
                    <v-item-group v-if="item.key == 'currency'">
                        <template v-for="currency in allCurrencies">
                            <v-divider :key="`divider-${currency.code}`" />
                            <v-list-item :key="currency.code">
                                <CustomCheckbox
                                    :inputValue="currency.code"
                                    :label="$t(currency.code)"
                                    v-model="selectedCurrency"
                                    name="selectedCurrency"
                                    type="radio"
                                />
                            </v-list-item>
                        </template>
                    </v-item-group>
                </v-list-group>
                <v-divider :key="`divider-${item.key}`" v-if="!navElementExpanded" />
            </template>

            <template v-if="!navElementExpanded">
                <v-list-item :to="{ name: 'PactoAmbiental' }"> {{ $t("Information") }} </v-list-item>
                <v-list-item :to="{ name: 'Upgrade' }"> {{ $t("Requests") }} </v-list-item>
                <v-list-item :to="{ name: 'PactoAmbiental' }"> {{ $t("Contact") }} </v-list-item>
                <v-list-item class="v-list-item--caption text-uppercase"> © Aloranges 2024 </v-list-item>
            </template>

            <template v-else>
                <v-divider />
                <v-list-item class="v-list-item--caption">
                    {{ $t("This setting only applies to this browser") }}
                </v-list-item>
            </template>
        </v-list>
    </v-menu>
</template>

<script>
import { mapState, mapGetters, mapActions } from "vuex";

import CustomButton from "../global/CustomButton.vue";
import CustomCheckbox from "../global/CustomCheckbox.vue";

import MenuIcon from "../icons/Menu.vue";
import CrescentMoonIcon from "../icons/CrescentMoonIcon.vue";
import TranslateIcon from "../icons/TranslateIcon.vue";
import WorldGlobeIcon from "../icons/WorldGlobe.vue";
import CoinIcon from "../icons/CoinIcon.vue";
import TonIcon from "../icons/TonIcon.vue";
import RightArrowIcon from "../icons/RightArrowIcon.vue";

export default {
    components: {
        CustomButton,
        CustomCheckbox,

        MenuIcon,
        CrescentMoonIcon,
        TranslateIcon,
        WorldGlobeIcon,
        CoinIcon,
        TonIcon,
        RightArrowIcon
    },
    data() {
        return {
            selectedCountry: null,
            selectedCurrency: null,
            selectedLang: null,
            selectedTheme: null,
            navElementExpanded: false
        };
    },
    mounted() {
        this.selectedCountry = this.userCountry;
        this.selectedCurrency = this.userCurrency;
        this.selectedLang = this.userLanguage;
        this.selectedTheme = this.userTheme;
        this.runColorMode();
    },
    computed: {
        ...mapState("app", [
            "allCountries",
            "allCurrencies",
            "allLanguages",
            "prefersDark",
            "themeItems",
            "userCountry",
            "userCurrency",
            "userLanguage",
            "userTheme"
        ]),
        ...mapGetters("app", ["userCountryObj", "userCurrencyObj", "userLanguageObj", "userThemeObj"]),
        items() {
            return [
                {
                    key: "theme",
                    icon: "CrescentMoonIcon",
                    title: "Appearance",
                    titleValue: this.userThemeObj.label
                },
                {
                    key: "lang",
                    icon: "TranslateIcon",
                    title: "Language",
                    titleValue: this.userLanguageObj.name
                },
                {
                    key: "country",
                    icon: "WorldGlobeIcon",
                    title: "Country",
                    titleValue: this.userCountryObj.name
                },
                {
                    key: "currency",
                    icon: "CoinIcon",
                    title: "Currency",
                    titleValue: this.userCurrencyObj.code
                },
                {
                    key: "measure",
                    icon: "TonIcon",
                    title: "Measure",
                    titleValue: "CGS"
                }
            ];
        }
    },
    watch: {
        selectedCountry() {
            this.setCountry(this.selectedCountry);
        },
        selectedCurrency() {
            this.setCurrency(this.selectedCurrency);
        },
        selectedLang() {
            if (this.$i18n.locale !== this.selectedLang) {
                this.setLanguage(this.selectedLang);
                window.location.reload();
            }
        },
        selectedTheme(newVal, oldVal) {
            this.setTheme(this.selectedTheme);

            // 2 is the index of the "device" theme
            // check if the user changed the theme from device to day or night
            if (oldVal != null && newVal === "device") {
                window.location.reload();
            } else {
                this.runColorMode();
            }
        }
    },
    methods: {
        ...mapActions("app", ["setCountry", "setCurrency", "setLanguage", "setTheme"]),
        runColorMode() {
            if (!window.matchMedia) return;

            if (this.userTheme == "day") {
                this.$vuetify.theme.dark = false;
            } else if (this.userTheme == "night") {
                this.$vuetify.theme.dark = true;
            } else if (this.userTheme == "device") {
                /* Set the theme to match the prefers-color-scheme media query */
                // this.$vuetify.theme.dark = this.prefersDark;
            }
        },
        setList() {
            setTimeout(() => {
                const listGroupEl = document.querySelectorAll(".toggle-menu-nav .v-list-group");
                const navElementExpanded = Array.from(listGroupEl).some(item =>
                    item.classList.contains("v-list-group--active")
                );

                if (navElementExpanded) {
                    Array.from(listGroupEl).forEach(item => {
                        if (!item.classList.contains("v-list-group--active")) {
                            item.classList.add("d-none");
                        }
                    });
                } else {
                    Array.from(listGroupEl).forEach(item => {
                        item.classList.remove("d-none");
                    });
                }

                this.navElementExpanded = navElementExpanded;
            }, 100);
        }
    }
};
</script>

<style lang="scss" scoped>
.toggle-menu {
    min-width: 280px;

    &-button {
        height: unset !important;
        min-width: unset !important;
        padding: 0 !important;

        &::v-deep {
            .v-btn__content {
                opacity: 1 !important;
            }
        }

        svg {
            &::v-deep {
                g:nth-child(1) {
                    stroke: #888989;

                    rect {
                        fill: #fafcfc;
                    }
                }

                g:nth-child(2) > rect {
                    fill: #888989;
                }
            }
        }

        &:is(.v-btn--active, :focus, :hover) {
            svg {
                &::v-deep {
                    g:nth-child(1) {
                        stroke: #444444;

                        rect {
                            fill: #444444;
                        }
                    }

                    g:nth-child(2) > rect {
                        fill: #fafcfc;
                    }
                }
            }
        }

        &.theme--dark {
            svg {
                &::v-deep {
                    g:nth-child(1) {
                        stroke: #ffffff;

                        rect {
                            fill: #000000;
                        }
                    }

                    g:nth-child(2) > rect {
                        fill: #ffffff;
                    }
                }
            }

            &:is(.v-btn--active, :focus, :hover) {
                svg {
                    &::v-deep {
                        g:nth-child(1) {
                            rect {
                                fill: #ffffff;
                            }
                        }

                        g:nth-child(2) > rect {
                            fill: #000000;
                        }
                    }
                }
            }
        }
    }

    &-nav {
        background: #fafcfc;
        margin-top: 10px;
        border-radius: 10px;
        padding: 0;

        &.theme--dark {
            background: #242526;
        }

        .v-divider {
            border-color: #707070 !important;
        }

        .v-list-group__header {
            .v-list-item {
                padding: 0;
            }
        }

        .v-list-item {
            .v-list-item {
                .v-list-item__icon {
                    margin: 14px 0;
                    margin-right: 12px;

                    svg {
                        width: 20px;
                        height: 20px;
                    }
                }
            }

            .v-list-item__title {
                font-family: "Roboto", sans-serif;
                font-size: var(--font-size-body1);
            }

            &[href] {
                text-transform: uppercase;
                font-size: var(--font-size-caption);
                font-weight: 700;
            }

            &--caption {
                font-size: var(--font-size-caption);
            }
        }

        &::v-deep {
            .v-list-group__header {
                &.v-list-item--active {
                    flex-direction: row-reverse;

                    .v-list-item {
                        .v-list-item__icon {
                            display: none;
                        }

                        .v-list-item__title {
                            text-transform: uppercase;
                            font-weight: 500;
                        }
                    }

                    .v-list-group__header__append-icon {
                        justify-content: flex-start;
                        min-width: 30px;

                        svg {
                            transform: rotate(-180deg);
                        }
                    }
                }

                .v-list-group__header__append-icon svg {
                    transition: 0.3s cubic-bezier(0.25, 0.8, 0.5, 1), visibility 0s;
                }
            }
        }
    }
}
</style>
