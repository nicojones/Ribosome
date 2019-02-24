var src = './src/resources/assets/',
    scripts = {
        'script.js': [
            'libs/jQuery/jQuery.js', // remove this if you don't want jQuery
            'custom.js',
            'home/home.js'
        ]
    },

    styles = {
        'style.css': [
            'libs/bootstrap/themes/paper_theme.css', // this is just a Bootstrap theme. do what you wish.
            'custom.css' 
            //'main.scss' // you can use SASS as well of course! Use the flag --sass when compiling
        ]
    };


var gulp   = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    argv = require('yargs').argv,
    mixins = merge(scripts, styles),
    //ngAnnotate = require('gulp-ng-annotate'), // use this with AngularJS
    htmlmin = require('gulp-htmlmin'),
    //sass = require('gulp-sass'), // doesn't work on OSX 64bit + Node v10
    sourcemaps = require('gulp-sourcemaps');


/**
 *
 * use '--production' to get a minified version: $~ gulp ribosome --production
 * use '--sass' or '--scss' if you are using .scss files: $~ gulp ribosome --sass
 */
gulp.task('ribosome', function() {

    for (var destinationNameJS in scripts)
    {
        var fileTypeScript  = destinationNameJS.split('.').pop(),
            scriptPartials  = mixins[destinationNameJS],
            fullPathToScripts = [];
  
        if (scriptPartials.length)
        {
            for (var i = 0, l1 = scriptPartials.length, file1; (file1 = scriptPartials [i]) || i < l1; ++i) {
                fullPathToScripts.push(src + fileTypeScript + '/' + file1);
            }
            
            var destinationPipeScripts =
              gulp
              .src(fullPathToScripts)
              .pipe(sourcemaps.init())
              .pipe(concat(destinationNameJS));
            
            if (argv.production !== undefined)
            {
                destinationPipeScripts = destinationPipeScripts.pipe(uglify({mangle: false}))
            }
            
            destinationPipeScripts
              .pipe(sourcemaps.write())
              .pipe(gulp.dest('./public/' + fileTypeScript + '/'));
            
            console.log("Compressed '" + destinationNameJS + "' (" + scriptPartials.length + " files)");
        }
    }
    for (var destinationNameCSS in styles)
    {
        var fileTypeStyle  = destinationNameCSS.split('.').pop(),
            stylePartials  = mixins[destinationNameCSS],
            fullPathToStyles = [];

        if (stylePartials.length)
        {
            for (var j = 0, l2 = stylePartials.length, file2; (file2 = stylePartials[j]) || j < l2; ++j)
            {
                fullPathToStyles.push(src + fileTypeStyle + '/' + file2);
            }
    
            var destinationPipeStyles = gulp.src(fullPathToStyles).pipe(concat(destinationNameCSS));
  
            destinationPipeStyles.pipe(sourcemaps.init());
            
            if (argv.sass !== undefined || argv.scss !== undefined)
            {
              destinationPipeStyles.pipe(sass().on('error', sass.logError));
            }
            
            destinationPipeStyles.pipe(sourcemaps.write());
    
            if (argv.production !== undefined)
            {
                destinationPipeStyles = destinationPipeStyles.pipe(uglifycss({
                    "maxLineLen": 80,
                    "uglyComments": true
                }));
            }
    
            destinationPipeStyles.pipe(gulp.dest('./public/' + fileTypeStyle + '/'));
            console.log("Compressed '" + destinationNameCSS + "' (" + stylePartials.length + " files)");
        }
    }

    return destinationPipeScripts;
});

// For this, you need to make a folder, uc_views and code the views inside it. The minified ones will be compressed in /views
//  resources |
//
//            |  views   |
//                       | index.php // a view. MINIFIED
//                       | ....
//
//            | uc_views |
//                       | index.php // a view. this is the one you edit
//                       | ....
//
gulp.task('html-min', function() {
    return gulp.src('./src/resources/uc_views/**')
        .pipe(htmlmin({
            collapseWhitespace: true,
            caseSensitive: true,
            removeComments: true,
            sortClassName: true
        }))
        .pipe(gulp.dest('./src/resources/views/'));
});

function merge(obj1,obj2){
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
}


/**
 * Call "gulp watch" to watch for changes in JS, CSS and SASS. Uncomment 'html-min' to watch for minimized html as well.
 */
gulp.task('watch', function() {
    //gulp.watch(['./src/views/uc_html/**/*.php', './src/views/uc_html/**/**/*.php', './src/views/uc_templates/*.php'], ['html-min']);
    return gulp.watch([src + 'js/**/*.js', src + 'js/**/**/*.js', src + 'sass/*.scss', src + 'sass/**/*.scss'], ['ribosome']);
});

