<template>
    <v-row
        class="presentation-banner rounded-section"
        :class="orientation === 'left' ? 'panel-left' : 'panel-rigth'"
        no-gutters
    >
        <v-col cols="12" md="5">
            <div class="panel pa-2 pl-md-5">
                <div>
                    <!-- <h5 class="presentation-banner-preamble mb-2 mb-md-5">{{ preamble }}</h5> -->
                    <h2 class="presentation-banner-title">{{ title }}</h2>
                    <v-divider class="mb-5" />
                    <p class="presentation-banner-description body1">
                        <slot name="description"> Description </slot>
                    </p>
                </div>
                <div>
                    <v-row v-if="actions" class="presentation-banner-actions">
                        <v-col cols="6" md="12" class="pb-md-0">
                            <custom-button block color="nero" text="Conoce Más" />
                        </v-col>
                        <v-col cols="6" md="12">
                            <custom-button block color="grey" text="Compartir" />
                        </v-col>
                    </v-row>
                </div>
            </div>
        </v-col>
        <v-col cols="12" md="7">
            <carousel v-if="slidesCarousel.length > 0" type="banner" :slides="slidesCarousel" />
            <img v-else-if="image" :src="image" class="presentation-banner-image" />
            <img
                v-else
                src="/public/assets/img/carousel-item-placeholder.png"
                class="presentation-banner-image placeholder"
            />
        </v-col>
    </v-row>
</template>

<script>
import Carousel from "./Carousel";
import CustomButton from "./CustomButton.vue";

export default {
    name: "PresentationBanner",
    props: {
        icon: {
            type: String,
            default: "/public/assets/img/idovela-logo.png"
        },
        preamble: { type: String, default: "Preamble" },
        title: { type: String, default: "Title" },
        orientation: { type: String, default: "left" },
        slidesCarousel: {
            type: Array,
            default: () => []
        },
        image: { type: String },
        actions: { type: Boolean, default: false }
    },
    data() {
        return {
            presetationBannerLogoUrl: "/public/assets/img/presentation-banner-logo.png"
        };
    },
    components: {
        Carousel,
        CustomButton
    }
};
</script>

<style lang="scss" scoped>
.theme--dark {
    .presentation-banner {
        background-color: #18191a;
    }
}

.presentation-banner {
    overflow: hidden;
    height: auto;
    min-height: 60vh;
    // max-height: 600px;

    &-preamble {
        font-size: calc(13px + (10) * var(--screen-size));
        line-height: calc(24px + (26 - 24) * var(--screen-size));
        font-weight: 700;
        text-transform: uppercase;
    }

    &-title {
        // font-size: calc(27px + (60 - 27) * var(--screen-size));
        // line-height: calc(39px + (76 - 39) * var(--screen-size));
        font-size: 30px;
        line-height: 36px;
        font-weight: 600;
        @media (min-width: 600px) {
            font-size: 40px;
            line-height: 47px;
            letter-spacing: 0.25px;
            font-weight: 600;
        }
        
    }

    &-description{
        text-align: justify;
        font-size: 18px;
        line-height: 25px;
    }

    &-image {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }
}

.panel {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    @media (max-width: 960px) {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
}

.v-carousel {
    @media (max-width: 960px) {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    &::v-deep {
        .v-window__next {
            @media (min-width: 960px) {
                right: 25%;
            }
        }

        .v-window__prev {
            @media (min-width: 960px) {
                left: 25%;
            }
        }

        .v-carousel__controls {
            .v-item-group {
                @media (min-width: 960px) {
                    width: 35%;
                }
            }
        }
    }
}

.panel-left {
    .panel {
        @media (min-width: 960px) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    }

    .v-carousel {
        @media (min-width: 960px) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    }
}

.panel-rigth {
    @media (min-width: 960px) {
        flex-direction: row-reverse;

        .panel {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .v-carousel {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    }
}
</style>
