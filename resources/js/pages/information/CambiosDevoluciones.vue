<template>
    <v-container class="policies-container">
        <v-row class="row">
          <v-col cols="12">
            <h1 class="policy-title">Cambios, Devoluciones y Garantías</h1>
          </v-col>
            <v-col cols="6">
                <section class="policy-section">
                  <h2 class="policy-subtitle">Políticas de Garantías</h2>
                    <ol class="policy-list">
                        <li>
                            Tenga a la mano la factura y/o información y todos los soportes de compra. En caso de no
                            contar con ella, puede solicitar la constancia y/o certificado de compra.
                        </li>
                        <li>
                            La garantía de cualquier producto, servicio o intangible estará sujeta a un diagnóstico
                            técnico y/o evaluación según sea el caso.
                        </li>
                        <li>
                            Toda solicitud se atenderá en un tiempo máximo de quince (15) días hábiles siguientes a la
                            recepción. Tenga en cuenta que el producto se remitirá al proveedor para su diagnóstico
                            técnico y su reparación (si aplica). El proceso de reparación se realizará dentro de los
                            treinta (30) días hábiles contados a partir del día siguiente a la recepción de la
                            solicitud.
                        </li>
                        <li>
                            Para agilizar la respuesta en la solicitud de garantía, le recomendamos contactar
                            directamente al Centro de Servicios Autorizado por el fabricante. Esto también aplica para
                            productos instalados. Para saber cuál es el centro de servicio técnico más cercano, solicite
                            esta información a través de la línea telefónica directa o chat en el borde inferior
                            derecho.
                        </li>
                        <li>
                            Si procede la garantía del producto, se efectuará la reparación totalmente gratuita de los
                            defectos del bien y se suministrarán oportunamente los repuestos requeridos. Si el producto
                            no admite reparación, se procederá a la devolución del precio pagado.
                        </li>
                        <li>
                            Si el producto se encuentra en un lugar diferente al de la compra, el cliente deberá asumir
                            los costos de transporte para hacer efectiva la garantía.
                        </li>
                        <li>
                            En caso de repetirse la falla, a elección del consumidor, se procederá a una nueva
                            reparación, la devolución del precio pagado o al cambio del producto por otro de la misma
                            especie, con similares características o especificaciones técnicas.
                        </li>
                        <li>
                            La garantía no será efectiva si el diagnóstico emitido indica:
                            <ol>
                                <li>Hubo fuerza mayor o caso fortuito.</li>
                                <li>El hecho de un tercero, manipulación del producto.</li>
                                <li>Uso indebido del bien por parte del consumidor.</li>
                                <li>
                                    El consumidor no atendió las instrucciones de instalación, uso o mantenimiento
                                    indicadas en el manual del producto y en la garantía.
                                </li>
                                <li>
                                    Una vez expirado el término de la garantía legal, el cliente deberá asumir el pago
                                    de cualquier revisión, diagnóstico, reparación y/o repuesto que requiera el bien.
                                </li>
                            </ol>
                        </li>
                    </ol>
                </section>
              </v-col>

            <v-col cols="6">
                <section class="policy-section">
                    <h2 class="policy-subtitle">Políticas de Devolución</h2>
                    <ol class="policy-list">
                        <li>
                            El producto debe estar en condiciones aptas para su venta (sin uso, sin armar, en el empaque
                            original, con catálogos, manuales y todas sus piezas).
                        </li>
                        <li>
                            Debe escribir a nuestro correo en la sección de contacto, indicando el número de la factura
                            de compra. En caso de no contar con ella, puede solicitar la constancia y/o certificado de
                            compra.
                        </li>
                        <li>
                            El plazo máximo para solicitar el cambio de productos será de 30 días calendario a partir de
                            la fecha de compra, excepto para productos sobre pedido o aquellos que fueron elaborados con
                            especificaciones propias del cliente que demuestran que son claramente personalizados.
                        </li>
                        <li>
                            La mercancía podrá ser devuelta a través de estos medios a elección del usuario:
                            <ol>
                                <li>
                                    Entregándola personalmente en la ciudad de Bogotá o Cali en la dirección
                                    suministrada en la sección de contacto.
                                </li>
                                <li>
                                    Llamando a la línea directa donde se coordinará la recogida en su domicilio a través
                                    de una empresa transportadora de mercancía. Le recordamos que quien ejerza el
                                    derecho de retracto deberá pagar el valor de este servicio.
                                </li>
                            </ol>
                        </li>
                        <li>
                            Para productos comprados en nuestro sitio web que no han sido recibidos, comuníquese a la
                            línea de atención directa.
                        </li>
                        <li>
                            La devolución se realizará de igual forma al medio de pago utilizado o mediante un código de
                            recompra, según escoja el cliente solicitante, equivalente al valor real pagado por el
                            producto.
                        </li>
                        <li>Las compras realizadas en nuestro sitio web están sujetas al Derecho de Retracto.</li>
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
