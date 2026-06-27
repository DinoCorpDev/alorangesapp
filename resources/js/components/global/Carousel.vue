<template>
    <v-carousel :class="`carousel-${type}`" :hide-delimiters="hideDelimiters" :show-arrows="showArrows" height="100%">
        <template v-slot:prev="{ on, attrs }">
            <v-btn small color="#000000" v-bind="attrs" v-on="on">
                <i class="las la-angle-left"></i>
            </v-btn>
        </template>
        <template v-slot:next="{ on, attrs }">
            <v-btn small color="#000000" v-bind="attrs" v-on="on">
                <i class="las la-angle-right"></i>
            </v-btn>
        </template>
        <template v-if="slides.length > 0">
            <div  class="d-flex align-center justify-center">
                <v-carousel-item v-for="(slide, i) in slides" :key="i" >
                    <div style="width: 100%; display: flex; justify-content: center;">
                        <img v-if="slide.type === 'image'" class="carousel-item-image" :src="slide.src" />
                    </div>
                    <video v-if="slide.type === 'video'" class="carousel-item-video" controls>
                        <source :src="slide.src" type="video/mp4" />
                    </video>
                    <v-responsive v-if="slide.type === 'iframe'" :aspect-ratio="aspectRatio">
                        <iframe
                            :src="`${slide.src}?controls=0&autoplay=1&fs=0&modestbranding&rel=0&showinfo=0&disablekb=0`"
                            frameborder="0"
                            allowfullscreen
                        ></iframe>
                    </v-responsive>
                    <v-row class="carousel-item-content" v-if="type == 'description'">
                        <v-col cols="12" sm="7" md="5" lg="4">
                            <div class="mb-4">
                                <h4 class="carousel-item-title mb-3">{{ title }}</h4>
                                <p class="carousel-item-description body1 mb-8">{{ description }}</p>
                                <div class="carousel-item-hashtags">
                                    <span v-for="(hashtag, i) in hashtags" :key="i">
                                        # {{ hashtag }}
                                        <span v-if="i < hashtags.length - 1">•</span>
                                    </span>
                                </div>
                            </div>
                            <v-row class="carousel-item-actions">
                                <v-col cols="6">
                                    <custom-button block color="white" text="Ir a planes" />
                                </v-col>
                                <v-col cols="6">
                                    <custom-button block color="white" text="Ver más" />
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-carousel-item>
            </div>
        </template>
        <v-carousel-item v-else>
            <img class="carousel-item-image" :src="itemPlaceholderUrl" />
        </v-carousel-item>
    </v-carousel>
</template>

<script>
import CustomButton from "./CustomButton.vue";

export default {
    name: "Carousel",
    props: {
        showArrows: {
            type: Boolean,
            default: true
        },
        hideDelimiters: {
            type: Boolean,
            default: false
        },
        slides: {
            type: Array,
            default: () => []
        },
        type: {
            type: String,
            default: "single"
        },
        title: {
            type: String,
            default: ""
        },
        description: {
            type: String,
            default: ""
        },
        hashtags: {
            type: Array,
            default: () => ["Instalaciones", "Mantenimiento", "Planes"]
        }
    },
    components: {
        CustomButton
    },
    data() {
        return {
            itemPlaceholderUrl: "/public/assets/img/carousel-item-placeholder.png"
        };
    },
    computed: {
        aspectRatio() {
            if (this.type === "single") {
                switch (this.$vuetify.breakpoint.name) {
                    case "xs":
                        return "0.8";
                    case "sm":
                        return "1.6";
                    case "md":
                        return "1.4";
                    case "lg":
                        return "1.4";
                    case "xl":
                        return "1.4";
                }
            }

            if (this.type === "banner") {
                switch (this.$vuetify.breakpoint.name) {
                    case "xs":
                        return "1";
                    case "sm":
                        return "1.6";
                    case "md":
                        return "2.1";
                    case "lg":
                        return "2.1";
                    case "xl":
                        return "2.1";
                }
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.v-carousel {
    .v-window-item {
        display: flex;
        align-items: center;

        video,
        iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
        }
    }
}

.carousel {
    &-description {
        &::v-deep {
            .v-window__prev {
                @media (min-width: 960px) {
                    left: 60%;
                }
            }

            .v-carousel__item {
                &::before {
                    content: "";
                    background: linear-gradient(0deg, #000000 0%, #00000000 90%, #00000000 100%);
                    height: 100%;
                    width: 100%;
                    opacity: 50%;
                    position: absolute;
                }
            }

            .v-carousel__controls {
                @media (min-width: 960px) {
                    width: 40%;
                    right: 0 !important;
                }
            }
        }
    }

    &-item {
        &-image {
            max-width: 100%;
            width: 100%;
            height: 100%;
            @media (min-width: 800px) {
                max-width: 100%;
                width: 70%;
                height: auto;
            }
            
            object-fit: cover;
            object-position: center;

            &.placeholder {
                object-fit: contain;
            }
        }

        &-content {
            width: 100%;
            color: #ffffff;
            position: absolute;
            left: 0.75rem;
            bottom: 4.5rem;

            @media (min-width: 600px) {
                left: 1.1rem;
            }

            @media (min-width: 960px) {
                bottom: 1.1rem;
            }
        }

        &-hashtags {
            font-size: var(--font-size-caption);
            line-height: 16px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
    }
}

.v-carousel {
    border-radius: 10px;
    background-color: #f4f5f7;
    border: 1px solidrgb(194, 194, 194)7;
    display: flex;
    align-items: center;
    justify-content: center;

    &::v-deep {
        .v-window__next,
        .v-window__prev {
            top: calc(100% - 52px);
            z-index: 2;
            background: transparent;
            border-radius: 0;

            .v-btn {
                font-size: 20px;
                height: 38px;
                min-width: 38px;
                padding: 0px;
                border-radius: 5px;
                opacity: 0.5;
            }
        }

        .v-window__container {
            height: 100% !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .v-carousel__controls {
            background: transparent;

            .v-item-group {
                width: 50%;
                display: flex;
                justify-content: space-around;
            }

            .v-carousel__controls__item {
                height: 8px;
                width: 8px;
                color: #000000;

                &.v-item--active {
                    color: #18191a;

                    &:before {
                        opacity: 1;
                    }
                }

                &:before {
                    opacity: 0.1;
                }
            }
        }
    }
}
.v-responsive__content{
    &::v-deep {
        display: flex;
        align-items: center;
        justify-content: center;
    }
}
</style>
