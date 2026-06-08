<template>
    <div>
        <v-row class="pt-2 pt-md-10 pl-0 pl-md-10 pr-2 pr-md-10 mb-4 mb-md-8">
            <v-col cols="6">
                <h2>{{ title }}</h2>
            </v-col>

            <v-col cols="6" class="d-none d-sm-block">
                <div class="wrapper-arrows">
                    <div class="wrap-arrow mr-3">
                        <div class="prev-button-orange" slot="button-prev"></div>
                    </div>
                    <div class="wrap-arrow">
                        <div class="next-button-orange" slot="button-next"></div>
                    </div>
                </div>
            </v-col>
        </v-row>
        <div class="image-container d-block d-sm-none">
            <v-row>
                <v-col class="image-container-wrap" cols="6" v-for="space in spaces" :key="`slide-space-${space.id}`">
                    <router-link :to="space.to || { name: space.routeName }" class="text-none">
                        <img
                            class="image-container-image"
                            :src="space.img || '/public/assets/img/AccesoriosOffMobile.png'"
                            :alt="space.title"
                        />
                        <h3 class="image-container-title">
                            {{ space.title }}
                        </h3>
                    </router-link>
                </v-col>
            </v-row>
        </div>
        <div class="d-none d-sm-block px-12">
            <swiper :options="swiperOptions">
                <swiper-slide v-for="space in spaces" :key="`slide-space-${space.id}`">
                    <SpaceItem :data="space" />
                </swiper-slide>
            </swiper>
        </div>
    </div>
</template>

<script>
import SpaceItem from "./SpaceItem.vue";

export default {
    name: "CarouselSpaces",
    components: {
        SpaceItem
    },
    props: {
        img: {
            type: String,
            default: "/public/assets/img/placeholder-rect.jpg"
        },
        title: {
            type: String,
            default: "H5"
        },
        spaces: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            swiperOptions: {
                slidesPerView: 3,
                spaceBetween: 12,
                // loop: true,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".next-button-orange",
                    prevEl: ".prev-button-orange"
                },
                breakpoints: {
                    960: {
                        slidesPerView: 4,
                        spaceBetween: 32
                    },
                    100: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    769: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    }
                }
            }
        };
    }
};
</script>

<style lang="scss" scoped>
.wrapper-arrows {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    margin-right: 20px;
}

.wrap-arrow {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 70px;
    height: 70px;
    background-color: #f58634;
    border-radius: 50%;

    @media (max-width: 600px) {
        width: 35px;
        height: 35px;
    }
}

.wrap-arrow:hover {
    background-color: white;
}

.wrap-arrow:active {
    background-color: white;
}

.next-button-orange {
    display: inline-block;
    width: 50px;
    height: 50px;
    background-color: white;
    mask-image: url("/public/assets/img/arrow-next.svg");
    mask-size: cover;
    transition: background-color 0.3s;
    @media (max-width: 600px) {
        width: 25px;
        height: 25px;
    }
}

.next-button-orange:hover {
    background-color: black;
}

.prev-button-orange {
    display: inline-block;
    width: 50px;
    height: 50px;
    background-color: white;
    mask-image: url("/public/assets/img/arrow-prev.svg");
    mask-size: cover;
    transition: background-color 0.3s;
    @media (max-width: 600px) {
        width: 25px;
        height: 25px;
    }
}

.prev-button-orange:hover {
    background-color: black;
}

.swiper-container {
    @media (max-width: 600px) {
        padding-bottom: 0 !important;
    }

    @media (max-width: 960px) {
        padding-bottom: 5rem;
    }

    .swiper-button-prev,
    .swiper-button-next {
        top: calc(100% - 25px);
        background-color: white;
        &::after {
            font-size: 16px;
        }
    }

    @media (min-width: 600px) {
        .swiper-button-prev {
            left: 20%;
        }

        .swiper-button-next {
            right: 20%;
        }
    }

    &::v-deep {
        .swiper-pagination-bullet {
            background: #18191a;
            opacity: 0.6;

            &-active {
                opacity: 1;
            }
        }
    }

    .swiper-pagination-bullets {
        width: 75%;
        left: 12.5%;
        display: flex;
        justify-content: space-around;
        bottom: 4%;

        @media (min-width: 600px) {
            width: 40%;
            left: 30%;
        }
    }

    @media (min-width: 960px) {
        .swiper-pagination,
        .swiper-button-prev,
        .swiper-button-next {
            display: none;
        }
    }
}

.image-container {
    &-wrap {
        display: flex;
        justify-content: center;
    }

    &-image {
        width: 175px;
        aspect-ratio: 1;
        border-radius: 100%;
    }

    &-title {
        font-size: 25px;
        text-align: center;

        @media (max-width: 600px) {
            font-weight: 600;
        }
    }
}
</style>
