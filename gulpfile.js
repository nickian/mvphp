const gulp = require('gulp');
const sass = require('gulp-sass');
const terser = require('gulp-terser');
const rename = require("gulp-rename");
const uglifycss = require('gulp-uglifycss');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');
const imageResize = require('gulp-image-resize');

var app_js_files = 'frontend/js/*.js';

var vendor_js_files = [
	'node_modules/bootstrap/dist/js/bootstrap.min.js',
	'node_modules/popper.js/dist/popper.min.js',
	'node_modules/jquery/dist/jquery.min.js',
];

var vendor_css_files = [
	'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
	'node_modules/animate.css/animate.min.css'
];

var font_files = 'node_modules/@fortawesome/fontawesome-free/webfonts/*';

var sassOptions = {
    errLogToConsole: true,
    includePaths: ['node_modules/bootstrap/scss']
};

var scss_files = [
	'frontend/scss/app.scss'
];

var scss_watch_files = [
	'frontend/scss/*.scss'
];

// convert scss files to css
gulp.task('css', function(){
  return gulp.src(scss_files)
  	.pipe(sass(sassOptions))
  	.pipe(concat('app.css'))
  	.pipe(rename({suffix: '.min'}))
    .pipe(uglifycss())
    .pipe(gulp.dest('public/css'));
});

// Move vendor css files
gulp.task('vendor_css', function(){
	return gulp.src(vendor_css_files)
	.pipe(gulp.dest('public/css'));
});

// Move vendor font files
gulp.task('fonts', function(){
	return gulp.src(font_files)
	.pipe(gulp.dest('public/webfonts'));
});

// Move vendor js files
gulp.task('vendor_js', function(){
	return gulp.src(vendor_js_files)
	.pipe(gulp.dest('public/js'));
});

// Uglify app js
gulp.task('app_js', function(){
	return gulp.src(app_js_files)
	.pipe(concat('app.js'))
	.pipe(rename({suffix: '.min'}))
	.pipe(terser())
	.pipe(gulp.dest('public/js'));
});

// Watch file path for change
gulp.task('watch', function() {
  gulp.watch(scss_watch_files, gulp.series('css'));
  gulp.watch(app_js_files, gulp.series('vendor_js', 'app_js'));
});

// Start Tasks
gulp.task('default', gulp.series('css', 'vendor_css', 'fonts', 'vendor_js', 'app_js'));
