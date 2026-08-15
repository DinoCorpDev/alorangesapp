<template>
    <div>
        <v-breadcrumbs
            v-if="breadcrumbItems[0].text != 'disabled'"
            class="mt-4 app-breadcrumbs"
            exact-active-class="active"
            active-class="disabled"
            :items="breadcrumbItems"
        >
            <template v-slot:divider>
                <i class="las la-angle-right"></i>
            </template>
        </v-breadcrumbs>
    </div>
</template>

<script>
import { mapGetters } from "vuex";


export default {
    name: "Breadcrumb",
    computed: {
        ...mapGetters("auth", ["userShortName"]),
        breadcrumbItems() {
            return this.$store.getters["breadcrumb/breadcrumbItems"];
        }
    },
};
</script>

<style lang="scss" scoped>
// El estilo iba inline (incluido un `font-size: 18px !important` imposible
// de sobreescribir). Pasa a clase para poder adaptarlo por breakpoint.
::v-deep .app-breadcrumbs {
    background-color: #f4f5f7;
    margin: 12px;
    margin-bottom: 0;
    // Antes: padding-left fijo de 50px, que en un movil de 320px se comia
    // el 15% del ancho. El hueco solo hace falta para el icono de home.
    padding: 10px 12px 10px 32px;
    // Las rutas largas hacen wrap en lugar de desbordar.
    flex-wrap: wrap;
    row-gap: 4px;

    @include respond-up("sm") {
        padding-left: 50px;
        padding-right: 16px;
    }
}

::v-deep .app-breadcrumbs li:first-of-type a {
    &:first-of-type::before {
        content: "";
        background-image: url("./Home.png");
        background-size: contain;
        background-repeat: no-repeat;
        width: 15px;
        height: 15px;
        display: inline-block;
        position: absolute;
        // Acompana al padding-left reducido de movil para que el icono no
        // se salga del contenedor.
        left: -18px;
        top: 45%;
        transform: translateY(-50%);

        @include respond-up("sm") {
            left: -20px;
        }
    }
}

::v-deep .app-breadcrumbs li,
::v-deep .app-breadcrumbs li a {
    // Escala con la tipografia del sistema en vez de quedar clavado en 17px.
    font-size: var(--font-size-body1);
    line-height: 1.4;

    @include respond-up("md") {
        font-size: 17px;
    }
}

::v-deep .app-breadcrumbs li a {
    position: relative;
}

::v-deep .theme--light.v-breadcrumbs .v-breadcrumbs__divider,
.theme--light.v-breadcrumbs .v-breadcrumbs__item--disabled {
    color: #f58634;
    margin-bottom: 5px;
}
</style>
