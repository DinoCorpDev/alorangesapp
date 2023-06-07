<template>
    <v-dialog v-model="isVisible" max-width="960px" @click:outside="closeDialog">
        <div class="white pa-8 rounded">
            <v-form class="inputs" ref="loginForm">
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Primer Nombre</span>
                        <custom-input />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase"> Segundo Nombre (Opcional) </span>
                        <custom-input />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Primer Apellido</span>
                        <custom-input />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Segundo Apellido</span>
                        <custom-input />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Documento (Representante)</span>
                        <select-custom />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Numero de Documento</span>
                        <custom-input />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Nombre de la Empresa</span>
                        <custom-input />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Entidad Comercial</span>
                        <select-custom />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Documento</span>
                        <select-custom />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase"> Numero de documento </span>
                        <custom-input />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6"> </v-col>
                    <v-col cols="12" md="6">
                        <custom-button block class="mt-5" text="Guardar" type="submit" color="black" />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-dialog>
</template>

<script>
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";

export default {
    props: {
        show: { type: Boolean, required: true, default: false }
    },
    components: {
        CustomButton,
        CustomInput,
        SelectCustom
    },
    data: () => ({}),
    computed: {
        isVisible: {
            get: function () {
                return this.show;
            },
            set: function (newValue) {}
        }
    },
    methods: {
        closeDialog() {
            this.isVisible = false;
            this.$emit("close");
        }
    }
};
</script>
