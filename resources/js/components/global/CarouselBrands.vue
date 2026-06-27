<template>
    <v-sheet class="mx-auto">
        <v-slide-group multiple show-arrows>
            <v-slide-item v-for="(n, i) in images" :key="i" class="margin-img">
                <v-img :src="n.src" width="80" />
            </v-slide-item>
        </v-slide-group>
    </v-sheet>
</template>

<script>
export default {
    data: () => ({
        model: null,
        images: [
            {
                alt: "",
                src: "public/assets/img/paperMate.png"
            },
            {
                alt: "",
                src: "public/assets/img/Sharpie.png"
            },
            {
                alt: "",
                src: "public/assets/img/Pelikan.png"
            },
            {
                alt: "",
                src: "public/assets/img/triton.png"
            }
        ]
    })
};
</script>

<style lang="scss" scoped>
.margin-img {
    margin-right: 20px;
}
</style>
