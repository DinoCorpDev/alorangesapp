<template>
    <v-container class="policies-container">
        <v-row class="row">
            <v-col cols="12">
                <h1 class="policy-title">Tiempo y Costo de Envío</h1>
                <p>
                    <strong>www.aloranges.com</strong> proporciona un estimado de tiempos de entrega; esta información
                    no representa tiempos exactos. El pedido llegará al lugar de residencia según la fecha asignada,
                    contando a partir del día inmediatamente siguiente, entre las 7:00 am y las 12:00 am de lunes a
                    domingo.
                </p>
                <p>
                    La mercancía se entregará en la dirección indicada por el cliente en el pedido. Para conjuntos
                    cerrados o propiedades horizontales donde no se permita el ingreso, se entregará en la portería;
                    para vías peatonales, se dejará hasta donde pueda ingresar el vehículo.
                </p>
                <p>
                    La factura de compra será enviada a la dirección indicada por el cliente dentro de los 10 días
                    hábiles siguientes a la fecha de compra en ciudades principales, y dentro de los 15 días hábiles
                    siguientes para otras ciudades.
                </p>
                <p>
                    En caso de que se visite el domicilio del comprador y no se logre la entrega del pedido porque él o
                    su autorizado no se encontraban presentes, podrá reprogramarse un nuevo envío, previa comunicación
                    telefónica con el cliente para notificar la novedad y coordinar la nueva entrega. Si el cliente no
                    se encuentra y se debe reprogramar una tercera entrega, esta tendrá un costo adicional igual al
                    costo de transporte cobrado en la factura.
                </p>
            </v-col>
            <v-col cols="12">
                <section class="policy-section">
                    <h2 class="policy-subtitle">Políticas de Garantías</h2>
                    <ol class="policy-list">
                        <li>
                            <strong>Oportunidad:</strong> Dentro de los treinta (30) días siguientes a la entrega del
                            producto.
                        </li>
                        <li>
                            <strong>Estado de la mercancía:</strong> El usuario deberá devolver el producto a
                            <strong>ALORANGES</strong> en las mismas condiciones que lo recibió, sin haber usado ni
                            armado el artículo, en su caja o empaque original, en perfecto estado, con sus piezas
                            completas, catálogos y manuales; apto para una nueva venta.
                        </li>
                        <li>
                            <strong>Costos de transporte:</strong> Deberán ser asumidos por el usuario, así como los
                            demás que conlleven la devolución del bien.
                        </li>
                        <li>
                            <strong>Productos excluidos:</strong> El derecho de retracto no aplicará para productos
                            sobre pedido o aquellos que fueron elaborados con exclusividad o especificaciones propias
                            del cliente que demuestren que son claramente personalizados.
                        </li>
                        <li>
                            <strong>ALORANGES</strong> efectuará la devolución del dinero al usuario dentro de los
                            treinta (30) días calendario siguientes a la recepción de la mercancía devuelta. La
                            mercancía podrá ser devuelta a través de los siguientes medios, a elección del usuario:
                            <ul>
                                <li>
                                    Entregándola personalmente en la ciudad de Bogotá o Cali en la dirección
                                    suministrada en la sección de contacto.
                                </li>
                                <li>
                                    Llamando a la línea directa, donde se coordinará la recogida en su domicilio a
                                    través de una empresa transportadora de mercancía. Se recuerda que quien ejerza el
                                    derecho de retracto deberá pagar el valor de este servicio.
                                </li>
                            </ul>
                        </li>
                    </ol>
                </section>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "PoliciesComponent"
};
</script>

<style scoped>
.policies-container {
    padding: 20px;
}

.policy-section {
    margin-bottom: 40px;
}

.policy-title {
    font-size: 24px;
    font-weight: bold;
}

.policy-subtitle {
    font-size: 20px;
    font-weight: bold;
}

.policy-list,
.policy-list ol {
    list-style: decimal;
    margin-left: 20px;
    text-align: justify;
}
</style>
