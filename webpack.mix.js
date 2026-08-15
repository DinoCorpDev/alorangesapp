const mix = require("laravel-mix");

mix.setResourceRoot(process.env.MIX_ASSET_URL);
mix.config.fileLoaderDirs.fonts = "web-assets/fonts";
mix.webpackConfig({
    output: {
        chunkFilename: "web-assets/js/[name].js?id=[chunkhash]",
        publicPath: "/public/",
    },
});
// Inyecta el sistema de breakpoints en TODOS los bloques
// <style lang="scss"> de los componentes .vue, para que puedan usar
// respond-up()/respond-down() sin importarlo uno a uno.
// _breakpoints.scss no emite CSS, asi que no se duplica nada.
mix.options({
    globalVueStyles: "resources/sass/_breakpoints.scss",
});

mix.sass("resources/sass/app.scss", "public/web-assets/css")
    .js("resources/js/app.js", "public/web-assets/js")
    .version();