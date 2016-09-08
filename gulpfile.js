var src = './src/resources/assets/',
    compress = false,
    mixins = {
        'script.js': [
            'libs/jQuery/jQuery.js',
            '/custom.js',
            '/home/home.js'
        ],
        'style.css': [
            'libs/bootstrap/themes/paper_theme.css',
            'custom.css'
        ]
    };


var gulp   = require('gulp'),
    minify = require('gulp-minify'),
    concat = require('gulp-concat');
gulp.task('ribosome', function() {

    for (var dst in mixins)
    {
        var type  = dst.split('.').pop(),
            files = mixins[dst];

        for (var i = 0, fl = files.length, f; (f = files[i]) || i < fl; ++i) {
            files[i] = src + type + '/' + f;
        }

        gulp.src(files)
            .pipe(concat(dst))
            //.pipe(minify({
            //    ext: {
            //        src: '.dev.js'
            //    },
            //    compress: compress,
            //    preserveComments: true
            //}))
            .pipe(gulp.dest('./public/' + type + '/'));

        console.log("Compressed '" + dst + "' (" + files.length + " files)");
    }

});

