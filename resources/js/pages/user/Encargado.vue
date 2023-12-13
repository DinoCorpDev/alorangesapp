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
                <v-col cols="3" md="3">
                    <CustomInput
                        v-model="formEncargadoModelo.indicativo"
                        name="indicativo"
                        required
                        placeholder="+XX"
                    />
                </v-col>

                <v-col cols="9" md="9" sm="9">
                    <CustomInput
                        v-model="formEncargadoModelo.encargado_telefono"
                        name="encargado_telefono"
                        required
                        placeholder="TELEFONO"
                    />
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
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

export default {
    components: {
        CustomButton,
        CustomInput
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
