<template>
    <div v-if="!isAuthenticated">
        <v-dialog v-model="showLogin" width="1000" @click:outside="hideLogin">
            <!-- <LoginForm /> -->
            <LoginPage />
        </v-dialog>
    </div>
</template>

<script>
import LoginForm from "./LoginForm.vue";
import LoginPage from "../../pages/auth/Login.vue";
import { mapGetters, mapMutations } from "vuex";
export default {
    data: () => ({}),
    components: { LoginForm, LoginPage },
    computed: {
        ...mapGetters("auth", {
            showLogin: "showLoginDialog",
            isAuthenticated: "isAuthenticated"
        })
    },
    methods: {
        ...mapMutations("auth", ["showLoginDialog"]),
        hideLogin() {
            this.showLoginDialog(false);
        }
    }
};
</script>

<style lang="scss">
.v-overlay--active {
    .v-overlay__scrim {
        backdrop-filter: blur(30px);
        background-color: rgba(0, 0, 0, 0.15) !important;
        opacity: 1 !important;
    }
}
</style>
