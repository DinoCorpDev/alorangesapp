<template>
    <v-dialog
        v-model="conversationDialog"
        width="600"
        :style="{ zIndex: '601', padding:'0 50px'}"
        @click:outside="hideDialog"
    >
        <v-card>
            <v-toolbar color="grey lighten-4" class="d-block" dense flat>
                <v-toolbar-title class="fw-600">{{  $t('any_query_about_this_product') }}</v-toolbar-title>
                <v-btn icon fab small class="ms-auto" @click="hideDialog">
                    <i class="las la-times fs-24"></i>
                </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4 text-dark">
                <div class="white pa-5 rounded">
                    <v-form lazy-validation autocomplete="chrome-off" @submit.prevent="addNewConversation()">
                        <div class="mb-3">
                            <div class="mb-1 fs-13 fw-500">{{ $t('title') }}</div>
                            <v-text-field 
                                v-model="form.title"  
                                :placeholder="$t('title')"
                                :error-messages="titleErrors"
                                hide-details="auto" 
                                type="text"
                                required
                                outlined
                            ></v-text-field>
                        </div>
                        <div class="mb-3">
                            <div class="mb-1 fs-13 fw-500">{{ $t('message') }}</div>
                            <v-textarea
                                v-model="form.message"
                                :placeholder="$t('message')"
                                :error-messages="messageErrors"
                                hide-details="auto"
                                rows="4"
                                required
                                outlined
                            ></v-textarea>
                        </div> 
                        <div class="text-right mt-4"> 
                            <v-btn 
                                elevation="0"
                                type="submit"
                                color="primary"
                                :loading="sending"  
                            >{{ $t('send') }}
                            </v-btn>
                        </div>
                    </v-form>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script> 
import { required } from "vuelidate/lib/validators";
import {mapGetters,mapActions} from "vuex"; 
export default {
    props: ["product"],
    data:() => ({ 
        sending: false,
        form:{  
            product_id:null,
            title: "",
            message: "", 
        }
    }),
    created(){  
        this.form.product_id  = this.product.id;
        this.form.title  = this.product.name;
        this.form.message  = window.location.origin+'/product/'+ this.product.slug;
    },
    validations: {
        form: {
            title: { required },
            message: { required },
        }
    },
    computed:{
        ...mapGetters('auth', {
            conversationDialog:'showConversationDialog', 
        }),
        titleErrors() {
            const errors = [];
            if (!this.$v.form.title.$dirty) return errors;
            !this.$v.form.title.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        messageErrors() {
            const errors = [];
            if (!this.$v.form.message.$dirty) return errors;
            !this.$v.form.message.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
    }, 
    methods:{
        ...mapActions('auth',['showConversationDialog']), 
        hideDialog(){
            this.showConversationDialog({status:false}) 
        }, 
        async addNewConversation(){
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            // submit data & reset if success
            this.sending = true;

            const res = await this.call_api("post", "user/new-query",this.form);
            if(res.data.success){ 
                this.snack({ message: res.data.message });
                this.resetData();
                this.hideDialog();                
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.sending = false; 
        },
        resetData(){  
            this.form.title  = this.product.name;
            this.form.message  = window.location.origin+'/product/'+ this.product.slug;
            this.$v.form.$reset();
        },
    }
}
</script>