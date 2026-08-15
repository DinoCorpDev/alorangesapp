<template>
    <v-card color="#F5F5F5" elevation="0" height="100%">
        <div class="border-bottom">
            <div class="div-container">
                <div class="col1">
                    <div class="div-img">
                        <div>
                            <v-avatar size="100" class="avatar-res">
                                <img
                                    :src="previewAvatar || currentUser.avatar || $helpers.imagePlaceholder()"
                                    @error="imageFallback($event)"
                                    class="border border-4"
                                />
                            </v-avatar>
                        </div>
                    </div>
                </div>
                <div class="col2">
                    <h6 class="black--text text-uppercase bold">INFORMACION</h6>
                </div>
                <div class="d-none d-lg-block divider my-4"></div>
                <UserMenu2 />
                <div class="d-none d-lg-block divider my-4"></div>
            </div>
        </div>
    </v-card>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import UserMenu2 from "./UserMenu2";
import CustomButton from "../../components/global/CustomButton.vue";

export default {
    components: {
        UserMenu2,
        CustomButton
    },
    data: () => ({}),
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("app", ["previewAvatar"])
    }
};
</script>

<style lang="scss" scoped>
.div-img {
    background-color: #dfdfdf;
    border-radius: 50%;
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
}

@media (max-width: 1025px) {
    .div-img {
        width: 60px;
        height: 60px;
    }

    .avatar-res {
        width: 60px !important;
        height: 60px !important;
    }
}

.div-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.col1,
.col2 {
    max-width: 300px;
    padding: 5px 10px 5px 10px;
}

.col3 {
    width: 100%;
    padding: 5px 10px 5px 10px;
}

.divider {
    background-color: #dfdfdf;
    width: 100%;
    height: 2px;
    margin: 10px;
}
</style>
