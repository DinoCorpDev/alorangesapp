<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- title -->
    <title>{!! $meta['meta_title'] !!}</title>

    <!-- meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">
    <meta name="description" content="{!! $meta['meta_description'] !!}" />
    <meta name="keywords" content="{{ $meta['meta_keywords'] }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{!! $meta['meta_title'] !!}">
    <meta name="twitter:description" content="{!! $meta['meta_description'] !!}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ $meta['meta_image'] }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{!! $meta['meta_title'] !!}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->full() }}" />
    <meta property="og:image" content="{{ $meta['meta_image'] }}" />
    <meta property="og:description" content="{!! $meta['meta_description'] !!}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('web-assets/css/app.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script src="{{ mix('web-assets/js/app.js') }}" defer></script>

    <style>
        :root {
            --primary: {{ get_setting('base_color', '#e62d04') }};
            --soft-primary: {{ hex2rgba(get_setting('base_color', '#e62d04'), 0.15) }};
        }

        @font-face {
            font-family: 'Causten';
            src: url("{{ static_asset('assets/fonts/Causten-Regular.woff2') }}") format("woff2");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Causten';
            src: url("{{ static_asset('assets/fonts/Causten-Medium.woff2') }}") format("woff2");
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Causten';
            src: url("{{ static_asset('assets/fonts/Causten-SemiBold.woff2') }}") format("woff2");
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: 'Causten';
            src: url("{{ static_asset('assets/fonts/Causten-Bold.woff2') }}") format("woff2");
            font-weight: 700;
            font-style: normal;
        }
    </style>

    @include('frontend.inc.pwa')

    <script>
        window.shopSetting = @json($settings);
    </script>

    @if (get_setting('google_analytics') == 1)
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ env('TRACKING_ID') }}');
        </script>
    @endif

    @if (get_setting('facebook_pixel') == 1)
        <!-- Facebook Pixel Code -->
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endif

    {!! get_setting('web_custom_css') !!}
    {!! get_setting('header_script') !!}

    {{-- Estilos de la pantalla de carga EN LINEA a proposito: app.css pesa
         ~0,8 MB y el bundle ~5,3 MB, asi que sin esto el usuario ve la pagina
         en blanco durante toda la descarga. Asi el logo aparece al instante. --}}
    <style>
        #app-splash {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 26px;
            background: linear-gradient(160deg, #ffffff 0%, #fff6ed 100%);
            transition: opacity .45s ease, visibility .45s ease;
        }

        #app-splash.is-oculto {
            opacity: 0;
            visibility: hidden;
        }

        .splash-marca {
            position: relative;
            width: 96px;
            height: 96px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Anillo que gira alrededor del isotipo */
        .splash-anillo {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid rgba(245, 134, 52, .18);
            border-top-color: #f58634;
            animation: splash-giro 1s linear infinite;
        }

        .splash-icono {
            width: 54px;
            height: 54px;
            object-fit: contain;
            animation: splash-latido 1.8s ease-in-out infinite;
        }

        .splash-logo {
            width: 168px;
            max-width: 60vw;
            height: auto;
            opacity: .92;
        }

        .splash-texto {
            margin: 0;
            font-family: "Roboto", system-ui, sans-serif;
            font-size: 13px;
            letter-spacing: .3px;
            color: #8a8f94;
        }

        @keyframes splash-giro {
            to { transform: rotate(360deg); }
        }

        @keyframes splash-latido {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        /* Respeta a quien pidio menos animacion en su sistema */
        @media (prefers-reduced-motion: reduce) {
            .splash-anillo { animation-duration: 3s; }
            .splash-icono { animation: none; }
        }
    </style>
</head>

<body>
    <noscript>To run this application, JavaScript is required to be enabled.</noscript>

    {{-- Se retira desde TheShop en cuanto la aplicacion monta --}}
    <div id="app-splash" role="status" aria-live="polite" aria-label="Cargando">
        <span class="splash-marca">
            <span class="splash-anillo"></span>
            <img
                class="splash-icono"
                src="{{ static_asset('assets/img/LogoIconoAloranges.png') }}"
                alt=""
                aria-hidden="true"
            >
        </span>
        <img
            class="splash-logo"
            src="{{ static_asset('assets/img/aloranges-logo.png') }}"
            alt="{{ get_setting('site_name') }}"
        >
        <p class="splash-texto">Preparando tu catálogo…</p>
    </div>

    <div id="app">
        <theShop></theShop>
    </div>

    @if (get_setting('facebook_chat') == 1)
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v3.3'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat" attribution=setup_tool page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif

    {!! get_setting('footer_script') !!}
</body>

</html>
