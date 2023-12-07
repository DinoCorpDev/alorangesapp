<template>
    <v-form v-if="mostrarFormEncargado" autocomplete="chrome-off" class="form">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <CustomInput required placeholder="NOMBRE COMPLETO" />
                </v-col>
                <v-col cols="12">
                    <span class="subtitle1 text-uppercase bold">TELÉFONO / CELULAR</span>
                </v-col>
                <v-col cols="3" md="3">
                    <CustomInput required placeholder="+XX" />
                </v-col>

                <v-col cols="9" md="9" sm="9">
                    <CustomInput required placeholder="TELEFONO" />
                </v-col>

                <v-col cols="4">
                    <v-btn type="button" block class="mt-2" @click="$emit('ocultarFormEncargado')">CANCELAR</v-btn>
                </v-col>
                <v-col cols="4"> </v-col>
                <v-col cols="4">
                    <v-btn type="submit" block class="mt-2" @click="$emit('ocultarFormEncargado')">GUARDAR ></v-btn>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>
<script>
export default {};
</script>
