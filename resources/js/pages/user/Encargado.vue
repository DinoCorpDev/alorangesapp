<template>
    <v-form autocomplete="chrome-off" class="form" @submit.prevent="submitEncargado">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <CustomInput
                        v-model="formEncargadoModelo.nombre_encargado"
                        name="nombre_encargado"
                        required
                        placeholder="NOMBRE COMPLETO"
                    />
                </v-col>
                <v-col cols="12">
                    <span class="subtitle1 text-uppercase bold">TELÉFONO / CELULAR</span>
                </v-col>

                <v-col cols="12">
                    <vue-tel-input
                        v-model="formEncargadoModelo.encargado_telefono"
                        :onlyCountries="availableCountries"
                        aria-placeholder="NUMERO DE TELEFONO"
                        mode="international"
                        inputOptions.placeholder=""
                    >
                    </vue-tel-input>
                </v-col>

                <v-col cols="4">
                    <CustomButton color="grey" class="mr-3" text="CANCELAR" @click="$emit('ocultar-form-encargado')" />
                </v-col>
                <v-col cols="4"> </v-col>
                <v-col cols="4">
                    <CustomButton color="grey" class="mr-3" type="submit" text="GUARDAR" />
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>
<script>
import { VueTelInput } from "vue-tel-input";
import { mapGetters } from "vuex";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

export default {
    components: {
        CustomButton,
        CustomInput,
        VueTelInput
    },
    data() {
        return {
            formEncargadoModelo: {
                nombre_encargado: null,
                indicativo: null,
                encargado_telefono: null
            }
        };
    },
    computed: {
        ...mapGetters("app", ["availableCountries"])
    },
    methods: {
        submitEncargado() {
            this.$emit("datos-encargado", this.formEncargadoModelo);
            this.cleanFormEncargado();
        },
        cleanFormEncargado() {
            this.formEncargadoModelo = {
                nombre_encargado: null,
                indicativo: null,
                encargado_telefono: null
            };
        }
    }
};
</script>
