<template>
    <v-dialog
      v-model="showLogin"
      width="auto"
    >
      <template v-slot:activator="{ props }">
        <v-btn
          color="primary"
          v-bind="props"
        >
          Open Dialog
        </v-btn>
      </template>

      <v-card>
        <v-card-text>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" block @click="dialog = false">Close Dialog</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script>
import { required, email } from "vuelidate/lib/validators";
import { mapActions, mapGetters, mapMutations } from "vuex";



export default {
    components: {
        
    },
    data: () => ({
        form: {
            email: "",
            password: ""
        },
        loading: false,
        showLogin: false,
       
    }),
    validations: {
        form: {
            email: {
                required,
                email
            },
            password: {
                required
            }
        }
    },
    computed: {

    },
    mounted() {
        this.$vuetify.theme.dark = false;
    },
    methods: {
        async showModal() {
            return this.showLogin = true;
        }
    }
};
</script>
