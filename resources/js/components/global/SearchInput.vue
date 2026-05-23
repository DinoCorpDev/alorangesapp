<template>
    <div class="search-input">
        <input
            @keyup.enter="search()"
            class="search-input-input"
            placeholder="Escribe lo que buscas"
            v-model="searchKeyword"
            type="search"
            required
            :class="{'show-input' : showInput}"
        />
        <button class="search-input-button" type="button" @click.stop.prevent="search()">
            <SearchIcon />
            <span class="search-input-label ml-2">
                {{ buttonLabel }}
            </span>
        </button>
    </div>
</template>

<script>
import SearchIcon from "../icons/Search.vue";

export default {
    name: "SearchInput",
    props: {
        placeholder: {
            type: String,
            default: "Escribe para buscar"
        },
        buttonLabel: {
            type: String,
            default: "Buscar"
        },
        showInput: {
            type: Boolean,
            default: false
        }
    },
    components: {
        SearchIcon
    },
    data() {
        return {
            searchKeyword: ""
        };
    },
    methods: {
        search() {
            this.$router
                .push({
                    name: "Search",
                    params: this.searchKeyword.length > 0 ? { keyword: this.searchKeyword } : {},
                    query: {
                        page: 1
                    }
                })
                .catch(() => {});
        }
    }
};
</script>

<style lang="scss" scoped>
.search-input {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    width: 100%;
    max-width: 836px;

    border-radius: 6px;
    overflow: hidden;

    box-shadow: rgba(0, 0, 0, 0.18) 0px 2px 8px;

    @media (max-width: 699px) {
        justify-content: flex-end;
    }

    &-input,
    &-label {
        display: none;

        @media (min-width: 700px) {
            display: block;
        }
    }

    &-input {
        flex: 1;
        min-width: 0;

        height: 40px;

        font-family: "Roboto";
        font-size: 15px;
        letter-spacing: 0.5px;

        background-color: #ffffff;
        border: 1px solid transparent;

        outline: none;

        padding: 0 1rem;

        transition: all 0.2s ease-in-out;

        appearance: none;
        -webkit-appearance: none;

        &:focus {
            border-color: #f58634;
        }

        &::placeholder {
            color: rgb(180, 180, 180);
        }
    }

    &-button {
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border: none;

        color: #ffffff;
        background-color: #f58634 !important;

        padding: 0 14px;

        transition: all 0.2s ease-in-out;

        cursor: pointer;

        @media (min-width: 960px) {
            padding: 0 2rem;
        }

        &:hover {
            background-color: #e97318 !important;
        }

        &:focus {
            outline: none;
        }

        :deep(svg) {
            display: flex;
            align-items: center;
            justify-content: center;

            path {
                fill: #ffffff;
            }
        }

        span {
            color: #ffffff;

            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            line-height: 1;

            margin-top: 1px;

            @media (min-width: 960px) {
                font-size: 14px;
            }
        }
    }
}

.show-input {
    display: block !important;
}

.v-application {
    &.theme--light {
        .search-input {
            &:hover {
                .search-input-input {
                    background-color: #ffffff;
                }
            }
        }
    }

    &.theme--dark {
        .search-input {
            background-color: #1f1f1f;

            &-input {
                background-color: #1f1f1f;
                color: #ffffff;

                &::placeholder {
                    color: rgba(255, 255, 255, 0.5);
                }
            }
        }
    }
}
</style>