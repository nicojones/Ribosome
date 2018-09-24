var src = './src/resources/assets/',
    scripts = {
        'script.js': [
            'libs/jQuery/jQuery.js',
            '/custom.js',
            '/home/home.js'
        ]
    },

    styles = {
        'style.css': [
            'libs/bootstrap/themes/paper_theme.css',
            'custom.css'
        ]
    };


var gulp   = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    argv = require('yargs').argv,
    mixins = merge(scripts, styles),
    ngAnnotate = require('gulp-ng-annotate'),
    htmlmin = require('gulp-htmlmin'),
    sass = require('gulp-sass');


gulp.task('ribosome', function() {

    for (var dst in scripts)
    {
        var type  = dst.split('.').pop(),
            files = mixins[dst],
            newFiles = [];

        for (var i = 0, fl = files.length, f; (f = files[i]) || i < fl; ++i) {
            newFiles.push(src + type + '/' + f);
        }

        var dest = gulp.src(newFiles).pipe(concat(dst)).pipe(ngAnnotate());
        if (argv.production !== undefined) {
            dest = dest.pipe(uglify())
            //dest = dest.pipe(uglify({ mangle: false }))
        }
        dest.pipe(gulp.dest('./public/' + type + '/'));

        console.log("Compressed '" + dst + "' (" + files.length + " files)");
    }
    for (var dst in styles)
    {
        var type  = 'css',//dst.split('.').pop(),
            files = mixins[dst],
            file  = styles[dst];

        var dest = gulp.src(src + 'sass' + '/' + file).pipe(concat(dst))
            .pipe(sass().on('error', sass.logError));

        if (argv.production !== undefined) {
            dest = dest.pipe(uglifycss({
                "maxLineLen": 80,
                "uglyComments": true
            }));
        }

        dest.pipe(gulp.dest('./public/' + type + '/'));
        console.log("Compressed '" + dst + "' (" + files.length + " files)");
    }

});

gulp.task('watch', function() {
    gulp.watch([src + 'js/**/*.js', src + 'js/**/**/*.js', src + 'sass/*.scss', src + 'sass/**/*.scss'], ['ribosome']);
});


// For this, you need to make a
gulp.task('html-min', function() {
    gulp.src('./src/resources/uc_views/**')
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
