let InformationLayout = () => import("../components/information/InformationLayout");
let PactoAmbiental = () => import("../pages/information/PactoAmbiental");
let TerminosCondiciones = () => import("../pages/information/TerminosCondiciones");
let PolizaGarantia = () => import("../pages/information/PolizaGarantia");
let CambiosDevoluciones = () => import("../pages/information/CambiosDevoluciones");
let TiempoEnvios = () => import("../pages/information/TiempoEnvios");
let ProteccionDatos = () => import("../pages/information/ProteccionDatos");
let PrivacidadCokies = () => import("../pages/information/PrivacidadCokies");
let MetodoPago = () => import("../pages/information/MetodoPago");
let LogisticaEnvio = () => import("../pages/information/LogisticaEnvio");

export default [
    {
        path: "/information/",
        component: InformationLayout,
        redirect: "/information/pactoAmbiental",
        children: [
            {
                path: "pactoAmbiental",
                component: PactoAmbiental,
                name: "PactoAmbiental"
            },
            {
                path: "terminosCondiciones",
                component: TerminosCondiciones,
                name: "TerminosCondiciones"
            },
            {
                path: "polizaGarantia",
                component: PolizaGarantia,
                name: "PolizaGarantia"
            },
            {
                path: "cambiosDevoluciones",
                component: CambiosDevoluciones,
                name: "CambiosDevoluciones"
            },
            {
                path: "tiempoEnvios",
                component: TiempoEnvios,
                name: "TiempoEnvios"
            },
            {
                path: "proteccionDatos",
                component: ProteccionDatos,
                name: "ProteccionDatos"
            },
            {
                path: "privacidadCokies",
                component: PrivacidadCokies,
                name: "PrivacidadCokies"
            },
            {
                path: "metodoPago",
                component: MetodoPago,
                name: "MetodoPago"
            },
            {
                path: "logisticaEnvio",
                component: LogisticaEnvio,
                name: "LogisticaEnvio"
            }
        ]
    }
];
