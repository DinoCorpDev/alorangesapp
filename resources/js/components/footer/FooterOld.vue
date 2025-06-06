<template>
    <footer class="grey darken-4 pt-8 white--text pb-14 pb-md-10 pb-lg-0">
        <v-container class="">
            <div class="border-bottom pb-7 border-gray-800 mb-6">
                <img :src="data.footer_logo" class="mw-100" />
            </div>
            <v-row>
                <v-col class="" cols="12" sm="8" md="6" lg="3">
                    <div class="primary--text fs-14 fw-700 mb-7">
                        {{ $t("get_your_special_offers_coupons__more") }}
                    </div>
                    <v-form lazy-validation v-on:submit.prevent="subscribe()">
                        <v-text-field
                            :placeholder="$t('your_email_address')"
                            type="email"
                            class="white mb-2"
                            v-model="subscribeForm.email"
                            :error-messages="emailErrors"
                            hide-details="auto"
                            required
                            outlined
                        ></v-text-field>
                        <v-btn
                            class="px-12 mb-4"
                            elevation="0"
                            type="submit"
                            color="primary"
                            @click="subscribe"
                            :loading="subscribeFormLoading"
                            :disabled="subscribeFormLoading"
                            outlined
                            >{{ $t("subscribe") }}</v-btn
                        >
                    </v-form>
                </v-col>
                <v-col
                    class="mb-5 d-none d-lg-block"
                    lg="2"
                    cols="12"
                    offset-lg="1"
                >
                    <h4 class="primary--text mb-4">
                        {{ $optional("data.footer_link_one?.title") }}
                    </h4>
                    <ul class="list-unstyled ps-0 fs-13">
                        <li
                            v-for="(link, label, i) in $optional( 'data.footer_link_one?.menu' )"
                            :key="i"
                            class="py-2"
                        >
                            <dynamic-link :to="link" append-class="text-reset" >{{ label }}</dynamic-link >
                        </li>
                    </ul>
                </v-col>
                <v-col class="mb-5 d-none d-lg-block" lg="2" cols="12">
                    <h4 class="primary--text mb-4">
                        {{ $optional("data.footer_link_two?.title") }}
                    </h4>
                    <ul class="list-unstyled ps-0 fs-13">
                        <li
                            v-for="(link, label, i) in $optional( 'data.footer_link_two?.menu')"
                            :key="i"
                            class="py-2"
                        >
                            <dynamic-link :to="link" append-class="text-reset" >{{ label }}</dynamic-link>
                        </li>
                    </ul>
                </v-col>
                <v-col class="mb-5 d-none d-lg-block" lg="2" cols="12">
                    <h4 class="primary--text mb-4">{{ $t("contact_us") }}</h4>
                    <ul class="list-unstyled ps-0 fs-13">
                        <li class="py-2 mb-2">
                            <div class="opacity-50">
                                <i class="las la-home me-3 mb-2"></i>
                                {{ $t("address") }}
                            </div>
                            <div>
                                {{ $optional( "data.contact_info?.contact_address" ) }}
                            </div>
                        </li>
                        <li class="py-2 mb-2">
                            <div class="opacity-50">
                                <i class="las la-envelope me-3 mb-2"></i>
                                {{ $t("email") }}
                            </div>
                            <div>
                                {{ $optional( "data.contact_info?.contact_email" ) }}
                            </div>
                        </li>
                        <li class="py-2 mb-2">
                            <div class="opacity-50">
                                <i class="las la-phone me-3 mb-2"></i>
                                {{ $t("phone") }}
                            </div>
                            <div>
                                {{ $optional( "data.contact_info?.contact_phone")}}
                            </div>
                        </li>
                    </ul>
                </v-col>
                <v-col class="mb-5 d-none d-lg-block" lg="2" cols="12">
                    <h4 class="primary--text mb-4">{{ $t("mobile_apps") }}</h4>
                    <a
                        :href="$optional('data.mobile_app_links?.play_store')"
                        target="_blank"
                        class="d-inline-block pt-2"
                    >
                        <img
                            :src="static_asset('/assets/img/play_store.png')"
                            class="mw-100"
                            height="40"
                        />
                    </a>
                    <a
                        :href="$optional('data.mobile_app_links?.app_store')"
                        target="_blank"
                        class="d-inline-block pt-2"
                    >
                        <img
                            :src="static_asset('/assets/img/app_store.png')"
                            class="mw-100"
                            height="40"
                        />
                    </a>
                    <template v-if="is_addon_activated('multi_vendor')">
                        <h4 class="primary--text my-4">{{ $t("seller_zone") }}</h4>
                        <ul class="list-unstyled ps-0 fs-13">
                            <li class="py-2">
                                <router-link :to="{ name: 'ShopRegistration' }" class="text-reset" >{{ $t('be_a_seller') }}</router-link>
                            </li>
                            <li class="py-2">
                                <a :href="$store.getters['app/appUrl']+'/login'" class="text-reset" target="_blank">{{ $t('login_to_seller_panel') }}</a>
                            </li>
                        </ul>
                    </template>
                </v-col>
                <v-col cols="12" class="d-lg-none mb-5">
                    <v-expansion-panels flat accordion>
                        <v-expansion-panel class=" transparent white--text border-bottom border-gray-800">
                            <v-expansion-panel-header
                                class="px-0"
                                expand-icon="la-angle-down white--text fs-14"
                            >
                                <h4 class="primary--text mb-0">
                                    {{ $optional("data.footer_link_one?.title") }}
                                </h4>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content class="">
                                <ul class="list-unstyled ps-0 fs-13">
                                    <li
                                        v-for="(link, label, i) in $optional( 'data.footer_link_one?.menu')"
                                        :key="i"
                                        class="py-2"
                                    >
                                        <dynamic-link :to="link" append-class="text-reset" >{{ label }}</dynamic-link>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <v-expansion-panel class=" transparent white--text border-bottom border-gray-800 " >
                            <v-expansion-panel-header class="px-0" expand-icon="la-angle-down white--text fs-14" >
                                <h4 class="primary--text mb-0">
                                    {{ $optional("data.footer_link_two?.title") }}
                                </h4>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content class="">
                                <ul class="list-unstyled ps-0 fs-13">
                                    <li
                                        v-for="(link, label, i) in $optional( 'data.footer_link_two?.menu' )"
                                        :key="i"
                                        class="py-2"
                                    >
                                        <dynamic-link :to="link" append-class="text-reset">{{ label }}</dynamic-link>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <v-expansion-panel class=" transparent white--text border-bottom border-gray-800 " >
                            <v-expansion-panel-header class="px-0" expand-icon="la-angle-down white--text fs-14" >
                                <h4 class="primary--text mb-0">
                                    {{ $t("contact_us") }}
                                </h4>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content class="">
                                <ul class="list-unstyled ps-0 fs-13">
                                    <li class="py-2 mb-2">
                                        <div class="opacity-50">
                                            <i class="las la-home me-3 mb-2" ></i>
                                            {{ $t("address") }}
                                        </div>
                                        <div> {{ $optional( "data.contact_info?.contact_address" ) }} </div>
                                    </li>
                                    <li class="py-2 mb-2">
                                        <div class="opacity-50">
                                            <i class=" las la-envelope me-3 mb-2 " ></i>
                                            {{ $t("email") }}
                                        </div>
                                        <div> {{ $optional( "data.contact_info?.contact_email" ) }} </div>
                                    </li>
                                    <li class="py-2 mb-2">
                                        <div class="opacity-50">
                                            <i class="las la-phone me-3 mb-2" ></i>
                                            {{ $t("phone") }}
                                        </div>
                                        <div> {{ $optional( "data.contact_info?.contact_phone" ) }} </div>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <v-expansion-panel class="transparent white--text">
                            <v-expansion-panel-header class="px-0" expand-icon="la-angle-down white--text fs-14" >
                                <h4 class="primary--text mb-0"> {{ $t("mobile_apps") }} </h4>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content class="">
                                <a :href="$optional('data.mobile_app_links?.play_store')" target="_blank" class="d-inline-block pt-2" >
                                    <img :src="static_asset('/assets/img/play_store.png')" class="mw-100" height="40" />
                                </a>
                                <a :href="$optional('data.mobile_app_links?.app_store')" target="_blank" class="d-inline-block pt-2" >
                                    <img :src="static_asset('/assets/img/app_store.png')" class="mw-100" height="40"/>
                                </a>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <v-expansion-panel class="transparent white--text border-top border-gray-800" v-if="is_addon_activated('multi_vendor')">
                            <v-expansion-panel-header class="px-0" expand-icon="la-angle-down white--text fs-14" >
                                <h4 class="primary--text mb-0"> {{ $t("seller_zone") }} </h4>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content class="">
                                <ul class="list-unstyled ps-0 fs-13">
                                    <li class="py-2">
                                        <router-link :to="{ name: 'ShopRegistration' }" class="text-reset" >{{ $t('be_a_seller') }}</router-link>
                                    </li>
                                    <li class="py-2">
                                        <a :href="$store.getters['app/appUrl']+'/login'" class="text-reset" target="_blank">{{ $t('login_to_seller_panel') }}</a>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>
            </v-row>
            <div class="py-3 border-top border-bottom border-gray-800">
                <ul class="list-unstyled d-flex flex-wrap ps-0">
                    <li
                        v-for="(link, label, i) in $optional('data.footer_menu')"
                        :key="i"
                        :class="[ 'py-2 pe-4 pe-md-7', { 'ps-md-3 ps-md-7': i !== 0 }]"
                    >
                        <dynamic-link :to="link" append-class="text-reset">{{ label }}</dynamic-link>
                    </li>
                </ul>
            </div>
            <v-row class="py-5">
                <v-col md="6" cols="12" :version="data.current_version">
                    <div  v-html="$optional('data.copyright_text')" class="lh-1 fs-13" ></div>
                </v-col>
                <v-col md="6" cols="12">
                    <ul class=" list-unstyled d-flex justify-start justify-md-end mt-2 ps-0 " >
                        <li
                            v-for="(link, label, i) in $optional( 'data.social_link' )"
                            :key="i"
                            :class="['social-icon', { 'ms-2': i != 0 }]"
                        >
                            <a :href="link" :class="label" target="_blank">
                                <i :class="{ lab: true, ['la-' + label]: true, }" ></i>
                            </a>
                        </li>
                    </ul>
                </v-col>
            </v-row>
        </v-container>
    </footer>
</template>

<script>
import { required, email } from "vuelidate/lib/validators";
export default {
    data: () => ({
        loading: true,
        data: {},
        subscribeForm: {
            email: "",
        },
        subscribeFormLoading: false,
    }),
    validations: {
        subscribeForm: {
            email: {
                required,
                email,
            },
        },
    },
    computed: {
        emailErrors() {
            const errors = [];
            if (!this.$v.subscribeForm.email.$dirty) return errors;
            !this.$v.subscribeForm.email.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.subscribeForm.email.email &&
                errors.push(
                    this.$i18n.t("Este campo es requerido_a_valid_email")
                );
            return errors;
        },
    },
    methods: {
        async getDetails() {
            const res = await this.call_api("get", `setting/footer`);
            if (res.status === 200) {
                this.data = res.data;
                this.loading = false;
            }
        },
        async subscribe() {
            this.$v.subscribeForm.$touch();
            if (this.$v.subscribeForm.$anyError) {
                return;
            }
            this.subscribeFormLoading = true;
            const res = await this.call_api(
                "post",
                "subscribe",
                this.subscribeForm
            );

            this.snack({ message: res.data.message });
            this.subscribeFormLoading = false;
        },
    },
    created() {
        this.getDetails();
    },
};
</script>
