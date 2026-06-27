<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        @keydown.esc="close"
        scrollable
    >
        <v-card>
            <v-card-title :class="options.color" class="fs-16 fw-600">
                {{ $t('rate_and_review_purchased_product') }}
                <button class="ms-auto" @click.stop="close" type="button">
                    <i class="la la-close fs-20"></i>
                </button>
            </v-card-title>
            <v-card-text class="pa-4 black--text">
                <div v-if="productId">
                    <div v-if="!product" class="h-270px"></div>
                    <div class="px-5 py-4" v-else>
                        <v-alert
                            dense
                            text
                            icon="la-info-circle"
                            type="info"
                            v-if="hasOldReview"
                        >
                            {{ $t("youve_already_reviewed_this_product_you_can_update_the_review") }}
                        </v-alert>
                        <v-form>
                            <div>{{ $t('rating') }}</div>
                            <v-rating
                                empty-icon="las la-star"
                                full-icon="las la-star active"
                                half-icon="las la-star half half"
                                background-color=""
                                v-model="review.rating"
                                hover
                                length="5"
                                size="32"
                            ></v-rating>
                            <div class="mt-3">{{ $t('comment') }}</div>
                            <v-textarea
                                v-model="review.comment"
                                :error-messages="commentErrors"
                                hide-details="auto"
                                outlined
                            ></v-textarea>
                        </v-form>
                    </div>
                </div>
                <div class="text-center fs-15 py-4" v-else>
                    {{ $t('this_product_is_not_available') }}
                </div>
            </v-card-text>
            <v-card-actions class="pt-3">
                <v-spacer></v-spacer>
                <v-btn
                    color="grey darken-3"
                    text
                    class=""
                    @click.native="close"
                >{{ $t('close') }}</v-btn>
                <v-btn
                    class="px-5"
                    color="primary"
                    elevation="0"
                    :loading="submitting"
                    :disabled="submitting"
                    @click.native="submit"
                >{{ $t('submit') }}</v-btn
            ></v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { required } from "vuelidate/lib/validators";
export default {
    name: "ReviewDialog",
    data() {
        return {
            dialog: false,
            submitting: false,
            productId: null,
            product: null,
            hasOldReview: false,
            review: {
                rating: null,
                comment: null
            },
            options: {
                color: "grey lighten-3",
                width: 800,
                zIndex: 200,
            },
        };
    },
    validations: {
        review: {
            comment: {
                required,
            }
        }
    },
    computed:{
        commentErrors() {
            const errors = [];
            if (!this.$v.review.comment.$dirty) return errors;
            !this.$v.review.comment.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
    },
    methods: {
        open(productId) {
            this.dialog = true;
            this.hasOldReview = false;
            this.product = null;
            this.review.rating = null;
            this.review.comment = null;

            this.productId = productId;
            if(productId)
                this.getReviewStatus(productId)
        },
        async getReviewStatus(productId){
            const res = await this.call_api("get", `user/review/check/${productId}`);
            if (res.data.success) {
                this.product = res.data.product
                this.review = res.data.oldReview
                if(!res.data.oldReview.rating){
                    this.review.rating = 5
                }else{
                    this.hasOldReview = true
                }
            }else{
                this.snack({ message: res.data.message })
            }
        },
        async submit() {
            this.$v.review.$touch();
            if (this.$v.review.$anyError) {
                return;
            }
            this.review.product_id = this.productId
            this.submitting = true;
            const res = await this.call_api("post", "user/review/submit", this.review);
            if (res.data.success) {
                this.snack({message: res.data.message })
            }else{
                this.snack({message: res.data.message, color: 'red'})
            }
            this.submitting = false;
            this.dialog = false;
        },
        close() {
            this.dialog = false;
        },
    },
};
</script>