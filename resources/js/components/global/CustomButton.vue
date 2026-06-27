<template>
    <v-btn
        :block="block"
        :color="color"
        :dark="dark"
        :disabled="disabled"
        :href="href"
        :light="light"
        :loading="loading"
        :outlined="outlined"
        :plain="plain"
        :ripple="false"
        :to="to"
        :width="width"
        v-bind="$attrs"
        v-on="$listeners"
        @click.stop="$emit('click')"
        class="text-uppercase justify-content-center align-items-center flex-direction-column"
        depressed
    >
        <i v-if="icon && iconPosition == 'left'" :class="`las ${icon} ${text ? 'mr-3' : ''}`"></i>
        <template v-if="text">
            <span :class="textClass">{{ text }}</span></template
        >
        <slot v-else />
        <i v-if="icon && iconPosition == 'right'" :class="`las ${icon} ${text ? 'ml-3' : ''}`"></i>
    </v-btn>
</template>

<script>
export default {
    name: "CustomButton",
    props: {
        text: String,
        width: String,
        icon: {
            type: String
        },
        iconPosition: {
            type: String,
            default: "left"
        },
        textClass: {
            type: String
        },
        dark: {
            type: Boolean,
            default: false
        },
        light: {
            type: Boolean,
            default: false
        },
        color: {
            type: String
        },
        outlined: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        block: {
            type: Boolean,
            default: false
        },
        plain: {
            type: Boolean,
            default: false
        },
        href: {
            type: String,
            default: undefined
        },
        to: {
            type: [String, Object]
        },
        loading: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<style lang="scss" scoped>
.v-btn {
    font-size: var(--font-size-btn);
    font-weight: 600;
    letter-spacing: 0;
    transition: all 0.2s ease-in-out;
    min-width: 10px !important;

    @media (max-width: 959px) {
        padding: 0 12px !important;
    }

    @media (min-width: 960px) {
        letter-spacing: 1.25px;
    }

    &:before {
        content: unset;
    }

    border-radius: 100px;

    i {
        font-size: 20px;
    }

    &:hover:before,
    &:focus:before {
        opacity: 1;
    }

    &.v-size--default {
        height: 38px;
    }

    &::v-deep {
        .v-btn__content {
            margin-top: 2px;
        }
    }

    &.text {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translate(-50%, -50%);
    }

    &.v-btn--plain {
        min-width: unset;
        height: unset !important;
        padding-left: 0;
        padding-right: 0;

        &::v-deep {
            .v-btn__content {
                margin-top: 0;
                opacity: 1 !important;
            }
        }
    }

    &.v-btn--outlined {
        &:hover {
            background-color: rgba(#ffffff, 0.8);
            color: #000000;
        }

        &:focus,
        &.v-btn--active {
            background-color: #ffffff;
            color: #000000;
        }
    }

    &.grey {
        color: #000000;
        background-color: #dfdfdf !important;

        &:hover {
            color: #fff;
            background-color: rgba(#434343, 0.8) !important;
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #161616 !important;
        }

        &.theme--dark {
            &:is(:hover, :focus) {
                color: #000000;
                background-color: #ffffff !important;
            }
        }
    }
    &.orange {
        color: #fff;
        background-color: #f58634 !important;

        &:hover {
            color: #000;
            background-color: rgba(#fcd6ba, 0.8) !important;
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.orange-cart {
        color: #fff;
        background-color: #f58634 !important;
        border: 1px solid #f58634;
        border-radius: 5px !important;

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.orange-cart2 {
        color: #fff;
        background-color: #f58634 !important;
        border: 1px solid #f58634;
        border-radius: 5px !important;
        font-size: 18px;
        font-weight: 700;
        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.orange-sidemenu {
        color: #e58644;
        background-color: #fbd6bb !important;

        &:hover {
            color: #fff;
            background-color: #f58634 !important;
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.orange2 {
        color: #000;
        background-color: #fcd6ba !important;

        &:hover {
            color: #fff;
            background-color: rgba(#f58634, 0.8) !important;
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.orange3 {
        color: #f58634;
        background-color: #fafcfb !important;
        @media (min-width: 600px) {
            color: #fff;
            background-color: #f58634 !important;
        }

        &:hover {
            color: #fcd6ba;
            @media (min-width: 600px) {
                color: #000;
                background-color: rgba(#fcd6ba, 0.8) !important;
            }
            
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #f58634 !important;
        }
    }
    &.grey2 {
        color: #000000;
        background-color: #dfdfdf;

        &:hover {
            color: #fff;
            background-color: rgba(#000000, 0.8);
        }

        &:focus,
        &.v-btn--active {
            color: #fff;
            background-color: #000000;
        }
    }

    &.black {
        color: #ffffff;
        background-color: #000000 !important;

        &:hover {
            background-color: rgba(#858585, 0.8) !important;
        }

        &:focus,
        &.v-btn--active {
            background-color: #161616;
        }
    }

    &.black2 {
        color: #ffffff;
        background-color: rgba(#161616, 0.5);

        &:hover {
            background-color: rgba(#161616, 0.8) !important;
        }

        &:focus,
        &.v-btn--active {
            background-color: #161616;
        }
    }

    &.doubleblack {
        color: #ffffff;
        background-color: rgba(#18191a, 0.5);

        &:hover {
            background-color: rgba(#18191a, 0.8);
            color: #000 !important;
        }

        &:focus,
        &.v-btn--active {
            background-color: #18191a;
            color: #000 !important;
        }
    }

    &.nero {
        color: #ffffff !important;
        background-color: #858585;

        &:hover {
            background-color: rgba(#434343, 0.8);
        }

        &:focus,
        &.v-btn--active {
            background-color: #161616;
        }
    }

    &.nero2 {
        color: #ffffff !important;
        background-color: rgba(#161616, 0.5);

        &:hover {
            background-color: rgba(#161616, 0.8);
        }

        &:focus,
        &.v-btn--active {
            background-color: #f58634;
        }
    }
    &.nero3 {
        color: #464b52;
        background-color: transparent;
        border: 1px solid #707780;
        border-radius: 8px !important;
        font-size: 18px;
        font-weight: 700;
        &:hover {
            background-color: rgba(#fcd6ba, 0.8);
            border: 1px solid rgba(#fcd6ba, 0.8);
            color: white;
        }

        &:focus,
        &.v-btn--active {
            background-color: rgba(#fcd6ba, 0.8);
        }
    }

    &.white {
        color: #f58634;
        background-color: #fafcfb!important;
        border: 1px solid #f58634 !important;    
        &:hover {
            color: #fcd6ba;
            @media (min-width: 600px) {
                background-color: #f58634 !important;
                color: #ffffff;
            }
        }
    }
    &.white2 {
        color: #000;
        background-color: #ffffff !important;

        &:hover {
            background-color: rgba(#161616, 0.8) !important;
            color: #f5f5f5;
        }

        &:focus,
        &.v-btn--active {
            background-color: #161616 !important;
            color: #f5f5f5;
        }
    }

    &.white3 {
        color: #000;
        background-color: #ffffff !important;

        &:hover {
            background-color: white !important;
            color: #f5f5f5;
        }

        &:focus,
        &.v-btn--active {
            background-color: #161616 !important;
            color: #f5f5f5;
        }
    }
}
</style>
