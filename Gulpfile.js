'use strict';

var gulp       = require('gulp');
var sass       = require('gulp-sass');
var concat     = require('gulp-concat');
var uglify     = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

var dir = {
    assets: './src/AppBundle/Resources/public/',
    dist: './web/',
    bower: './bower_components/'
};

gulp.task('sass', function() {
    gulp.src(
        [
            dir.bower + 'font-awesome/css/font-awesome.min.css',
            //dir.assets + 'temp.css',
            dir.assets + 'sass/style.scss'
        ])
        .pipe(sourcemaps.init())
        //.pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(concat('style.css'))
        .pipe(gulp.dest(dir.dist + 'css'));
});

gulp.task('scripts', function() {
    gulp.src([
        dir.bower + 'jquery/dist/jquery.min.js',
        dir.bower + 'modernizr/modernizr.js',
        dir.bower + 'bootstrap-sass/assets/javascripts/bootstrap.min.js',
        dir.assets + 'script/script.js',
    ])
        .pipe(concat('script.js'))
        //.pipe(uglify())
        .pipe(gulp.dest(dir.dist + 'js'));
});

gulp.task('fonts', function() {
    gulp.src([
        dir.bower + 'bootstrap-sass/assets/fonts/bootstrap/**',
        dir.bower + 'bootstrap-sass/assets/fonts/**',
        dir.bower + 'font-awesome/fonts/**'
    ])
        .pipe(gulp.dest(dir.dist + 'fonts'));
});

gulp.task('images', function() {
    gulp.src([
        dir.assets + 'images/**'
    ])
        .pipe(gulp.dest(dir.dist + 'images'));
});

gulp.task('default', ['sass', 'scripts', 'fonts', 'images']);
