<template>
    <div class="carousel-swiper">
        <h5 class="mb-3"><strong>{{ title || "H5" }}</strong></h5>
        <swiper :options="swiperOptions">
            <slot />
            <template>
                <!-- <div class="swiper-pagination" slot="pagination"></div> -->
                <div class="swiper-button-prev" slot="button-prev"></div>
                <div class="swiper-button-next" slot="button-next"></div>
            </template>
        </swiper>
    </div>
</template>

<script>
export default {
    name: "CarouselSwiper",
    props: {
        title: String,
        options: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        swiperOptions() {
            return {
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                },
                ...this.options
            };
        }
    }
};
</script>

<style lang="scss" scoped>
.swiper-container {
    padding-bottom: 5rem;

    .swiper-button-prev,
    .swiper-button-next {
        top: calc(100% - 38px);
        background: rgba(#000000, 0.5);
        width: 38px;
        height: 38px;
        border-radius: 5px;
        transition: all 0.1s ease-in-out;

        &:hover {
            background: rgba(#000000, 0.8);
        }

        &::after {
            color: #fff;
        }
    }

    .swiper-button-prev {
        right: 51% !important;
        border-radius: 100px;
        left: inherit;
        // @media (min-width: 600px) {
        //     left: 20%;
        // }

        // @media (min-width: 960px) {
        //     left: 37%;
        // }
    }

    .swiper-button-next {
        left: 51%;
        border-radius: 100px;
        // @media (min-width: 600px) {
        //     right: 20%;
        // }

        // @media (min-width: 960px) {
        //     right: 37%;
        // }
    }

    &::v-deep {
        .swiper-slide {
            height: auto;
        }
    }

    .swiper-pagination-bullets {
        bottom: 22px;
        width: 70%;
        right: 0;
        margin: auto;
        display: flex;
        justify-content: space-around;

        @media (min-width: 600px) {
            width: 30%;
        }

        @media (min-width: 960px) {
            width: 15%;
        }

        &::v-deep {
            .swiper-pagination-bullet {
                background: #18191a;
            }
        }
    }
}
</style>
