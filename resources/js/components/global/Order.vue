<template>
    <div class="div-order">
        <v-row class="order">
            <v-col cols="9">
                <div>
                    <h5 class="font-weight-bold mb-2" style="font-size: 24px;">Orden de pedido # {{ order }}</h5>
                    <p class="body-1" style="margin: 0 !important">Fecha {{ day }} de {{ month }} de {{ year }}</p>
                </div>
            </v-col>
            <v-col cols="3" class="pr-5 iconos">
                <div class="d-none d-md-flex pr-5 iconos">
                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <div v-bind="attrs" v-on="on">
                                <custom-button plain @click.stop="printWindow"> <Descargar /> </custom-button>
                            </div>
                        </template>
                        <span>Descargar</span>
                    </v-tooltip>

                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <div v-bind="attrs" v-on="on">
                                <custom-button plain :to="{ name: 'InvoicesDetails' }"> <Compartir /> </custom-button>
                            </div>
                        </template>
                        <span>Compartir</span>
                    </v-tooltip>
                </div>
                <div class="d-md-none" style="display: flex; justify-content: flex-end;">
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                            v-bind="attrs"
                            v-on="on"
                            style="box-shadow: none; background-color: transparent;"
                            >
                            <Ellipsis />
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item
                            v-for="(item, index) in items"
                            :key="index"
                            link
                            >
                            <v-list-item-title>
                                <v-icon>{{ item.icono }}</v-icon>
                                 {{ item.title }}
                            </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </v-col>
        </v-row>

    </div>
</template>

<script>
import CustomButton from "./CustomButton.vue";
import Compartir from "../../components/icons/Compartir.vue";
import Imprimir from "../../components/icons/Imprimir.vue";
import Descargar from "../../components/icons/Descargar.vue";
import Ellipsis from "../../components/icons/EllipsisIcon.vue";

export default {
    data: () => ({
      items: [
        { icono: 'la-download', title: 'Descargar' },
        { icono: 'la-share-alt-square', title: 'Compartir' },
        { icono: 'la-print', title: 'Imprimir' },
      ],
    }),
    components: {
        CustomButton,
        Compartir,
        Imprimir,
        Descargar,
        Ellipsis
    },
    props: {
        order: String,
        day: [String, Number],
        month: String,
        year: [String, Number],
        descriptionStatus: String,
        
    },
    methods:{
        printWindow(){
            window.print();
        }   
    }
};
</script>

<style lang="scss" scoped>
.div-order {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 17px;
    border: 1px solid #e6e6e6;
    border-radius: 10px;
}

.div-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.color-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
}

.status {
    display: flex;
    gap: 8px;
}

.iconos {
    display: flex;
    gap: 44px;
    align-items: center;
    justify-content: flex-end;    
}
</style>
