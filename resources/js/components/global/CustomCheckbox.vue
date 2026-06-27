<template>
    <div
        class="custom-checkbox"
        :class="{ 'error--text': error, bordered: bordered, 'bordered-active': isRadioChecked && bordered }"
        @click="toggleCheckbox"
    >
        <div class="custom-checkbox-input">
            <input
                :id="id"
                :name="name"
                :required="required"
                :type="type"
                :value="inputValue"
                ref="input"
                v-model="isChecked"
            />
            <span class="custom-checkbox-checkmark">
                <svg xmlns="http://www.w3.org/2000/svg" width="15.673" height="12.19" viewBox="0 0 15.673 12.19">
                    <path
                        d="M19.929,13.245l-1.741,1.741-1.741,1.741-1.741,1.741-1.741,1.741-1.741,1.741L7.739,18.469,6,20.211l3.483,3.483h0l.871.871.871.871h0l1.741-1.741,1.741-1.741,1.741-1.741,1.741-1.741,1.741-1.741,1.741-1.741Z"
                        transform="translate(-5.998 -13.245)"
                        fill="#fff"
                    />
                </svg>
            </span>
        </div>
        <label class="custom-checkbox-label">
            <template v-if="label">{{ label }}</template>
            <slot v-else />
        </label>
    </div>
</template>

<script>
export default {
    name: "CustomCheckbox",
    props: {
        label: {
            type: String,
            default: ""
        },
        required: {
            type: Boolean,
            default: false
        },
        value: {
            type: [Boolean, String, Number]
        },
        inputValue: {
            type: [Boolean, String, Number],
            default: ""
        },
        error: {
            type: Boolean,
            default: false
        },
        type: {
            type: String,
            default: "checkbox"
        },
        bordered: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: undefined
        },
        id: {
            type: String,
            default: undefined
        }
    },
    computed: {
        isChecked: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit("input", value);
            }
        },
        isRadioChecked() {
            return this.isChecked === this.inputValue;
        }
    },
    methods: {
        toggleCheckbox() {
            if (this.type === "radio") {
                this.$refs.input.click();
            }

            if (this.type === "checkbox") {
                this.isChecked = !this.isChecked;
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.theme--dark {
    .custom-checkbox {
        &-checkmark {
            border: 1px solid #ffffff;

            &:is(:hover) {
                background-color: #ffffff;

                svg {
                    path {
                        fill: #000;
                    }
                }
            }
        }

        input {
            &:is(:checked) {
                ~ .custom-checkbox-checkmark {
                    background-color: #ffffff;

                    svg {
                        path {
                            fill: #000;
                        }
                    }
                }
            }
        }
    }
}

.custom-checkbox {
    display: flex;
    align-items: center;
    position: relative;
    padding-left: 35px;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    min-height: 38px;

    &-input {
        position: absolute;
        left: 0;
    }

    &.bordered {
        padding-left: 40px;
        // border: 1px solid;
        border-radius: 5px;

        &-checkmark {
            margin-left: 5px;
        }

        &-active {
            color: black;
            border-color: black;
        }
    }

    &-checkmark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        width: 24px;
        border-radius: 5px;
        border: 1px solid #7e8693;
        background-color: #f5f5f5;
    }

    &:hover {
        .custom-checkbox-checkmark {
            background-color: #dfdfdf;
        }
    }

    /* Hide the browser's default checkbox */
    input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;

        &:is(:checked) {
            ~ .custom-checkbox-checkmark {
                background-color: #f58634;
                border-color: #f58634;
            }
        }

        &:not(:checked) {
            ~ .custom-checkbox-checkmark {
                svg {
                    opacity: 0;
                }
            }
        }
    }

    &-label {
        font-family: "Roboto", sans-serif;
        font-size: var(--font-size-body1);
        cursor: pointer;
        color: black;

        a {
            text-decoration: underline;
            font-weight: 700;
        }
    }

    &.error--text {
        .custom-checkbox-checkmark {
            border-color: currentColor;
            border-width: 2px;
        }
    }
}
</style>
