<template>
    <div class="payment" :class="{'active' : active == true}">
        <div class="div-icon">
            <v-img max-width="50px" min-height="50px" :src="img"></v-img>
        </div>
        <p class="text-caption text-uppercase mb-0">{{ text }}</p>
    </div>
</template>

<script>
export default {
    props: {
        img: {
            type: String,
            default: "/public/assets/img/carousel-item-placeholder.png"
        },
        active:{
            type: Boolean,
            defaul: false
        },
        text: String
    }
};
</script>

<style lang="scss" scoped>
.payment {
    background-color: #fafafa;
    border-radius: 10px;
    text-align: center;
    padding-top: 10px;
}
.active{
    background-color: #ffefdf;
}
.div-icon {
    display: flex;
    justify-content: center;
}
</style>
