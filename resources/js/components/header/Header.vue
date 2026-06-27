<template>
    <header :class="['header-sticky ', { 'sticky-top': generalSettings.sticky_header == 1 }]">
        <TopBar :loading="loading" :data="data" />
        <LogoBar :loading="loading" :data="data" />
        <HeaderMenu :loading="loading" :data="data" class="d-none d-md-block" />
    </header>
</template>

<script>
import TopBar from "./TopBar";
import LogoBar from "./LogoBar";
import HeaderMenu from "./HeaderMenu";
import { mapGetters } from "vuex";

export default {
    data: () => ({
        loading: true,
        data: {}
    }),
    components: {
        TopBar,
        LogoBar,
        HeaderMenu
    },
    computed: {
        ...mapGetters("app", ["generalSettings"])
    },
    methods: {
        async getDetails() {
            const res = await this.call_api("get", `setting/header`);
            if (res.status === 200) {
                this.data = res.data;
                this.loading = false;
            }
        }
    },
    created() {
        this.getDetails();
    }
};
</script>
