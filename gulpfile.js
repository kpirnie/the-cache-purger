// require gulp
import gulp from "gulp";

// require delete
import deleter from "del";

// require sass plugin
import dartSass from 'sass';
import gulpSass from 'gulp-sass';
const sass = gulpSass( dartSass );

// require js concat and minify plugins
import uglify from "gulp-uglify";
import gconcat from "gulp-concat";
import grename from "gulp-rename";

// require css minifier
import cssnano from "gulp-cssnano";

// require image optimizer
import imagemin from "gulp-imagemin";

// translations
import wpPot from "gulp-wp-pot";

// svgs
import svgo from "gulp-svgo";

// gulp text replacer
// import replace from "gulp-replace";

// fs
import fs from "fs";

// read in our package json file
const pkg = JSON.parse( fs.readFileSync( './package.json' ) )

// source directory
const src = {
    root: `${pkg.source}`,
};

// distribution directory
const dist = {
    root: `${pkg.distribution}`,
};

// set our source assests paths
src.assets = `${src.root}/assets`;
src.scss = `${src.assets}/scss`;
src.css = `${src.assets}/css`;
src.fonts = `${src.assets}/fonts`;
src.js = `${src.assets}/js`;
src.images = `${src.assets}/images`;
src.vendor = `${src.root}/vendor/**/*`

// set our distribution assests paths
dist.assets = `${dist.root}/assets`;
dist.css = `${dist.assets}/css`;
dist.fonts = `${dist.assets}/fonts`;
dist.js = `${dist.assets}/js`;
dist.images = `${dist.assets}/images`;
dist.vendor = `${dist.root}/vendor/`;

// our working glob paths
const globs = {
    src: {
        js: [ 
            `${src.js}/*.js`,
            `!${src.js}/custom.js`
        ],
        scss: [ 
            `${src.scss}/*.scss` 
        ],
        css: [ 
            `${src.css}/*.css`,
            `!${src.css}/custom.css`
        ],
        fonts: [ 
            `${src.fonts}/**/*` 
        ],
        img: [ 
            `${src.images}/*.+(png|jpg|jpeg|gif)` 
        ],
        svgs: [ 
            `${src.images}/*.+(svg|svgz)` 
        ],
        php: [ 
            `${src.root}/*.php`, 
            `${src.root}/**/*.php` 
        ],
        templates: [ 
            `${src.root}/*.php`, 
            `${src.root}/**/*.php`, 
            `${src.root}/screenshot.png`, 
            `${src.root}/style.css`,
            `${src.root}/readme.txt`,
            `${src.root}/readme.md`,
        ],
    },
};

/** Setup our tasks to run */

// cleanup
gulp.task( 'cleanup', function( ) {
    console.log( '# Cleaning Up Distribution' );
    return deleter( [`${dist.root}/**`, `!${dist.root}`] );
} );

// cleanup concat files
gulp.task( 'cleanupconcat', function( ) {
    console.log( '# Cleaning Up Concatenated Files' );
    return deleter( [`${dist.css}/concat.css`, `${src.css}/temp.css`, `${dist.js}/concat.js`] );
} );

// sass
gulp.task( 'sass', function( ) {
    console.log( '# Working on SASS' );
    return gulp.src( globs.src.scss )
        .pipe( sass.sync( ).on( 'error', sass.logError ) )
        .pipe( gulp.dest( `${src.css}` ) );
} );

// css
gulp.task( 'stylesheets', function( ) {
    console.log( '# Working on Stylesheets' );
    return gulp.src( globs.src.css )
        .pipe( gconcat( 'concat.css' ) )
        .pipe( gulp.dest( `${dist.css}` ) )
        .pipe( grename( 'style.min.css' ) )
        .pipe( cssnano( ) )
        .pipe( gulp.dest( `${dist.css}` ) );
} );

// javascript
gulp.task( 'javascripts', function( ) {
    console.log( '# Working on JS' );
    return gulp.src( globs.src.js )
        .pipe( gconcat( 'concat.js' ) )
        .pipe( gulp.dest( `${dist.js}` ) )
        .pipe( grename( 'script.min.js' ) )
        .pipe( uglify( ) )
        .pipe( gulp.dest( `${dist.js}` ) );
} );

// copying fonts
gulp.task( 'fonts', function( ) {
    console.log( '# Working on Fonts' );
    return gulp.src( globs.src.fonts )
        .pipe( gulp.dest( `${dist.fonts}` ) );
})

// images
gulp.task( 'images', function( ) {
    console.log( '# Working on Images' );
    return gulp.src( globs.src.img )
        .pipe( imagemin( ) )
        .pipe( gulp.dest( `${dist.images}` ) );
} );

// svgs
gulp.task( 'svgs', function( ) {
    console.log( '# Working on SVGs' );
    return gulp.src( globs.src.svgs )
        .pipe( svgo( ) )
        .pipe( gulp.dest( `${dist.images}` ) );
} );

// languages
gulp.task( 'languages', function( ) {
    console.log( '# Working on Languages' );
    return gulp.src( globs.src.php )
		.pipe( wpPot( {
			domain: `${pkg.name}`,
			package: `${pkg.package}`,
		} ) )
		.pipe( gulp.dest( `${dist.root}/languages/${pkg.name}.pot` ) ); 
} );

// our php templates
gulp.task( 'templates', function( ) {
    console.log( '# Working on Templates' );
    return gulp.src( globs.src.templates, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.root}` ) );
} );

// our custom assets
gulp.task( 'customs', function( ) {
    console.log( '# Working on Custom Assets' );
    return gulp.src( `${src.css}/custom.css`, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.css}` ) ),
        gulp.src( `${src.js}/custom.js`, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.js}` ) );
} ); 

// our vendor folder
gulp.task( 'vendor', function( ) {
    console.log( '# Working on Vendor' );
    return gulp.src( `${src.vendor}`, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.vendor}` ) );
} );

// debug assets
gulp.task( 'debug_assets', function( ) {
    console.log( '# Copying in Debug Assets' );
    return gulp.src( globs.src.css, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.css}` ) ),
        gulp.src( globs.src.js, { allowEmpty: true } )
        .pipe( gulp.dest( `${dist.js}` ) );
} );

// replace environment set
//gulp.task( 'replace-env', function( ) {
//    console.log( '# Replacing Environment in DIST' );
//    return gulp.src( `${dist.root}/functions.php` )
//        .pipe( replace( /staging/g, 'production' ) )
//        .pipe( gulp.dest( `${dist.root}` ) );
//} );

// replace the production theme name
//gulp.task( 'replace-themename', function( ) {
//    console.log( '# Replacing DIST Theme Name' );
//    return gulp.src( `${dist.root}/style.css` )
//        .pipe( replace( /The Base Stage/g, 'The Base WP' ) )
//        .pipe( gulp.dest( `${dist.root}` ) );
//} );

// production copy
gulp.task( 'production_copy', function( done ) {

    if( pkg.production.shouldcopy ) {
        console.log( '# Copying to Production' );
        return gulp.src( `${dist.root}/**/*`, { allowEmpty: true }  )
            .pipe( gulp.dest( `${pkg.production.path}/` ) );
    }

    // default this to return false
    done( );

} );

// setup our default task to run our build sequencing
gulp.task( 'default', gulp.series(
    'cleanup',
    'sass',
    'stylesheets',
    'javascripts',
    'cleanupconcat',
    [ 'fonts', 'images', 'svgs', 'languages', 'templates', 'customs' ],
    //[ 'replace-env', 'replace-themename' ],
    [ 'vendor', 'debug_assets', 'production_copy', ],
) );
