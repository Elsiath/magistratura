'use strict'
const gulp = require('gulp');
const concat = require('gulp-concat');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const del = require('del');
const browserSync = require('browser-sync').create();
const sass = require('gulp-sass');
const imagemin = require('gulp-imagemin');
const sourcemaps = require('gulp-sourcemaps');
var rigger = require('gulp-rigger');

sass.compiler = require('node-sass');

function styles() {
    return gulp.src('./src/css/main.sass')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['> 0.1%'],
            cascade: false
        }))
        .pipe(cleanCSS({
            level: 2
        }))
        .pipe(gulp.dest('./build/css'))
        .pipe(sourcemaps.write())
        .pipe(browserSync.stream({once: true}));
}

function script() {
    return gulp.src(['./src/js/jquery.min.js', './src/js/bootstrap.min.js', './src/js/main.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./build/js'))
        .pipe(browserSync.stream({once: true}));
}

function html() {
    return gulp.src('./src/*.html')
        .pipe(rigger())
        .pipe(gulp.dest('./build'))
        .pipe(browserSync.stream({once: true}));
}

function img() {
    return gulp.src('./src/img/*')
        .pipe(gulp.dest('./build/img'));
}

function fonts() {
    return gulp.src('./src/fonts/**/*.*')
        .pipe(gulp.dest('./build/fonts'));
}

function watch() {

    browserSync.init({
        server: {
            baseDir: "./build"
        },
        tunnel: true,
        notify: false
    });

    gulp.watch('./src/css/**/*.sass', styles);
    gulp.watch('./src/js/**/*.js', script);
    gulp.watch('./src/**/*.html', html);
}

function clean() {
    return del(['build/*']);
}

gulp.task('watch', watch);
gulp.task('build', gulp.series(clean,
    gulp.parallel(styles, script, html, img, fonts)
));
gulp.task('dev', gulp.series('build', 'watch'));