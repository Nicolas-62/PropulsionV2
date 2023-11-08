const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')
    .enableSassLoader()


    // website images.
    .copyFiles({
        from: './assets/front/images',
        to:    'front/images/[path][name].[ext]'
    })
    // website fonts.
    .copyFiles({
        from: './assets/front/fonts',
        to:    'front/fonts/[path][name].[ext]'
    })
    // Maquette images.
    .copyFiles({
        from: './assets/maquette/images',
        to:    'maquette/images/[path][name].[ext]'
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. main.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    // BACKOFFICE
    .addEntry('backoffice', './assets/bo/js/main.js')
    .addEntry('bo_pictures',  './assets/bo/js/pictures.js')
    .addEntry('bo_files',  './assets/bo/js/files.js')
    .addEntry('bo_medias',  './assets/bo/js/medias.js')
    .addEntry('bo_articles','./assets/bo/js/articles.js')
    .addEntry('bo_article', './assets/bo/js/article.js')
    .addEntry('bo_gallery', './assets/bo/js/gallery.js')
    .addEntry('bo_category', './assets/bo/js/category.js')
    .addEntry('bo_projets', './assets/bo/js/projets.js')


    // FRONTOFFICE

    // MAIN
    .addEntry('fo_main_js',    './assets/front/js/main.js')
    .addEntry('fo_main_css',    './assets/front/styles/main.scss')
    // PAGES
    .addEntry('home_js',           './assets/front/js/home.js')
    .addEntry('home_css',           './assets/front/styles/home.scss')



    // MAQUETTE
    .addEntry('maquette_js',    './assets/maquette/js/main.js')
    .addEntry('maquette_css',    './assets/maquette/styles/main.scss')
    .addEntry('maquette_infos_js',    './assets/maquette/js/infos.js')
    .addEntry('maquette_infos_css',    './assets/maquette/styles/infos.scss')
    .addEntry('maquette_event_cancelled_js',    './assets/maquette/js/cancelled.js')


    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.addExternals({
    // jquery: 'jQuery'
}).getWebpackConfig();

