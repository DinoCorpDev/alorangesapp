# Aloranges — guía del proyecto

Tienda B2B de suministros (papelería, aseo, cafetería, cartonería, tecnología,
seguridad industrial). Laravel + SPA Vue 2 + panel Blade.

## Arranque en local

Laragon. **No hay servidor de desarrollo**: lo sirve Apache en
`https://alorangesapp.test`. Si no responde, arrancar servicios:

```powershell
Start-Process "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe" -ArgumentList "--defaults-file=C:\laragon\bin\mysql\mysql-8.0.30-winx64\my.ini" -WindowStyle Hidden
Start-Process "C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\bin\httpd.exe" -WindowStyle Hidden
```

MySQL tarda ~30s en inicializar InnoDB; la primera petición puede tardar 40s.

**`php` no está en el PATH.** Usar siempre la ruta completa:
`/c/laragon/bin/php/php-8.1.10-Win32-vs16-x64/php.exe artisan ...`
Igual con mysql: `/c/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql.exe -u root aloranges_db`

Compilar assets: `npm run development` (Laravel Mix + webpack 4). Tarda ~2 min.

Para pruebas se usan usuarios locales `qa.cliente@test.local` y
`qa.admin@test.local` (existen solo en la base de datos local; las credenciales
no se versionan). El admin necesita el rol Spatie **Super Admin** o recibe 403
en casi todo el panel (`Gate::before` en AuthServiceProvider solo concede
permisos a ese rol).

## Arquitectura

Dos frontends independientes sobre el mismo Laravel:

| Zona | Tecnología | Dónde |
|---|---|---|
| Tienda pública | SPA Vue 2 + Vuetify 2 + Vuex + vue-router | `resources/js/` |
| Panel admin | Blade + plantilla AIZ (Bootstrap 4 + jQuery) | `resources/views/backend/` |
| API de la tienda | Laravel | `routes/api.php` (148 rutas) |

- La SPA se monta en `resources/views/frontend/app.blade.php` (`<div id="app">`)
  desde `resources/js/app.js`, que renderiza `components/TheShop.vue`.
- **`TheShop.vue` es la raíz**: contiene header, footer, drawers globales y mide
  la altura real del header para desplazar el contenido (ver más abajo).
- La ruta `/` carga `pages/Home2.vue` (no `Home.vue`, que es `/home`).

## Trampas conocidas (leer antes de tocar nada)

**Código muerto abundante — comprobar SIEMPRE si un archivo se usa.**
Antes de editar una página, verificar que el router la referencia:
`grep -rn "NombreArchivo" resources/js/router/`

Muertos confirmados: todos los `*Old.vue` (13), `Home copy.vue`,
`Profile copy.vue`, **la carpeta entera `components/home/` (22 componentes)**
—solo la usa `Home copy.vue`—, `TempLanding.vue` y `TheShopOld.vue`.

**`resources/sass/main.scss` NO se compila** (está comentado en `app.scss:15`
desde 2022), pero ~45 de sus clases se siguen usando en plantillas. Esas clases
no tienen ningún estilo aplicado.

**Vuetify pisa estilos con CSS inline.** El `padding` de `v-main` se escribe
inline, así que una regla CSS nunca gana. Por eso el offset del header se aplica
como `margin-top` inline desde `mainOffsetStyle` en `TheShop.vue`.

**`requestAnimationFrame` se pausa en pestañas ocultas.** Afecta a
`<transition>` de Vue (se queda a medias para siempre) y al posicionamiento de
`v-menu`. Si algo debe funcionar sí o sí, usar animación CSS sobre `:key` en vez
de `<transition>`. El `ResizeObserver` tampoco dispara en el navegador integrado;
por eso `TheShop.vue` lleva un vigilante por intervalo como red de seguridad.

**Precios a 0.** 4.109 de 17.174 productos tienen `lowest_price = 0` y nombres
tipo `****PAPEL CONTACT`. Cualquier listado ordenado solo por nombre los pone
primero. Ordenar por relevancia: coincidencia exacta de referencia → empieza por
el término → tiene precio.

**Imágenes que faltan.** 50 de 1.838 registros de `uploads` apuntan a ficheros
inexistentes (tampoco están en producción). Cuando falta, Laravel devuelve el
**HTML de la SPA con HTTP 200**, no un 404 — el `<img>` queda con
`naturalWidth: 0`. El ajuste `invoice_logo` apunta a una foto de papel higiénico,
no a un logo; usar `public/assets/img/aloranges-logo.png`.

**mPDF ignora `object-fit` y las clases CSS sobre `<img>`.** Para dimensionar
imágenes en PDFs usar el atributo `width`.

## Sistema de estilos (frontend)

Hay un sistema centralizado, **usarlo siempre**:

- `resources/sass/_breakpoints.scss` — mixins `respond-up("md")`,
  `respond-down("sm")`, `respond-between()`. Alineados con Vuetify:
  sm 600 · md 960 · lg 1264 · xl 1904. `respond-down` corta en `.98` para no
  solaparse con `respond-up`.
- Se inyecta automáticamente en **todos** los `<style lang="scss">` de los `.vue`
  vía `globalVueStyles` (webpack.mix.js). No hace falta importarlo.
- `resources/sass/_variables.scss` — escala tipográfica fluida con `clamp()`
  (`--font-size-h1`…`--font-size-body2`). Solo lo importa `app.scss`: emite CSS y
  se duplicaría si se inyectara en cada componente.

**No escribir `@media (max-width: 768px)` a mano.** El proyecto llegó a tener 27
valores de breakpoint distintos; los que quedan son deuda por migrar.

En el **panel admin** el sistema es otro: reutilizar los componentes de la
plantilla AIZ (`aiz-titlebar`, `aiz-table`, `btn-soft-*`, `badge-soft-*`,
`size-40px`, `img-fit`, `form-control`) e iconos Line Awesome (`las la-*`).

## Módulo de cotizaciones (propio, no de la plantilla)

`app/Http/Controllers/QuotationController.php`, modelos `Quotation` /
`QuotationItem`, vistas en `resources/views/backend/quotations/`.
Sección principal del menú lateral. La numeración continúa desde la 5819 en papel.
Los datos de cliente y producto **se copian** a la cotización (documento
histórico): si cambia el producto, la cotización enviada no debe cambiar.

## Verificación

No se pueden hacer capturas de pantalla en `alorangesapp.test` (el panel exige
aprobación por acción), y la pestaña suele estar oculta a nivel de sistema, así
que **el diseño visual hay que confirmarlo con el usuario**.

Lo que sí se puede medir por JS en el navegador integrado: desbordamiento
horizontal (`scrollWidth - clientWidth`), geometría de elementos, estilos
computados, errores de consola y peticiones fallidas. Para el backend, `curl`
con cookies + CSRF funciona bien.
