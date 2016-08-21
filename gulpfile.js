var gulp   = require('gulp'),
    minify = require('gulp-minify'),
    concat = require('gulp-concat');


gulp.task('default', function() {
    console.log("it's working!");

    var src = './src/resources/assets/js/';
    //gulp.src('src/resources/assets/js/*/*.js, src/resources/assets/js/*.js')
    gulp.src([
        src + 'libs/jQuery/jQuery.js',
        src + '/*.js',
        src + '/home/home.js'
        ])
        .pipe(concat('script.js'))
        .pipe(minify({
            ext: {
                src: '.min.js',
                debug: '.js'
            },
            compress: false,
            preserveComments: true
        }))
        .pipe(gulp.dest('./public/js/'));
});