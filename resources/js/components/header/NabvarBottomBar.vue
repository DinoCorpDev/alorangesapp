<template>
    <v-container class="bar" fluid>
        <div class="bar-item bar-link" @click="$router.back()">
            <BackArrowIcon />
            <span class="bar-label ml-3"> Volver </span>
        </div>
        <a v-if="$route.meta.shareble" class="bar-item" href="#">
            <span class="bar-label mr-3"> Compartir </span>
            <ShareIcon />
        </a>
        <div v-else class="bar-item">
            <span class="bar-label mr-3"> Seguro </span>
            <PadLockIcon />
        </div>
    </v-container>
</template>

<script>
import BackArrowIcon from "../icons/BackArrow.vue";
import PadLockIcon from "../icons/PadLock.vue";
import ShareIcon from "../icons/Share.vue";

export default {
    name: "NabvarBottomBar",
    props: {
        share: {
            type: Boolean,
            default: false
        }
    },
    components: {
        BackArrowIcon,
        PadLockIcon,
        ShareIcon
    }
};
</script>

<style lang="scss" scoped>
.bar {
    background-color: #dfdfdf;
    display: flex;
    justify-content: space-between;
    align-items: center;

    &-item {
        display: flex;
        align-items: center;

        &::v-deep {
            svg {
                path {
                    fill: #040405;
                }
            }
        }
    }

    &-link {
        cursor: pointer;
    }

    &-label {
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.15px;
        color: #000000;
        margin-top: 2px;

        @media (min-width: 600px) {
            font-size: 16px;
        }
    }
}
</style>
