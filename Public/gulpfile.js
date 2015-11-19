

	// 引入 gulp
	var gulp = require('gulp');

	// 引入组件
	var sass = require('gulp-sass');
	var cache = require('gulp-cache');
	var babel = require('gulp-babel');
	var concat = require('gulp-concat');
	var jshint = require('gulp-jshint');
	var uglify = require('gulp-uglify');
	var rename = require('gulp-rename');
	var plumber = require('gulp-plumber');
	var cssmin = require('gulp-minify-css');
	var imagemin = require('gulp-imagemin');
	var pngquant = require('imagemin-pngquant');
	var browserSync = require('browser-sync').create();



	// 前端js ES6=>ES5
	gulp.task('fontend-js', () => {
	    return gulp.src('src/public/home/js/**/*.js')
	    	.pipe(plumber())
	        .pipe(babel({
	            presets: ['es2015']
	        }))
	        .pipe(gulp.dest('public'));
	});

	// 后端js ES6=>ES5
	gulp.task('backend-js', () => {
	    return gulp.src('src/app/**/*.js')
	        .pipe(plumber())
	        .pipe(babel({
	            presets: ['es2015']
	        }))
	        .pipe(gulp.dest('app'));
	});


	gulp.task('backend-js-watch', ['backend-js'], browserSync.reload);
	gulp.task('fontend-js-watch', ['fontend-js'], browserSync.reload);


	/* 压缩图片
		gulp.task('imagemin', function () {
		    gulp.src('./src/home/images/*.{png, jpg, gif, ico}')
		        .pipe(cache(imagemin({
		            optimizationLevel: 1, //类型：Number  默认：3  取值范围：0-7（优化等级）
		            progressive: true, //类型：Boolean 默认：false 无损压缩jpg图片
		            interlaced: true, //类型：Boolean 默认：false 隔行扫描gif进行渲染
		            use: [pngquant({quality: '65-80'})]
		        })))
		        .pipe(gulp.dest('./build/home/images'));
		});
	*/


	/* 合并，压缩JS
		gulp.task('scripts', function() {
		    gulp.src('./src/js/*.js')
		        .pipe(concat('index.min.js'))
		        .pipe(uglify())
		        .pipe(gulp.dest('./src/js'));
		});
	*/


	// 编译Sass
	gulp.task('sass', function() {
	    gulp.src('home/scss/*.scss')
	        .pipe(plumber())
	        .pipe(sass())
	        .pipe(gulp.dest('home/css'))
	        .pipe(browserSync.reload({stream: true}));
	});


	/* 合并，压缩CSS
		gulp.task('styles', function() {
		    gulp.src('./src/css/index.css')
		        .pipe(concat('index.min.css'))
		        .pipe(cssmin())
		        .pipe(gulp.dest('./src/css'));
		});
	*/



	// 服务器
	gulp.task('server', function() {
	    browserSync.init({
	        proxy: "localhost:80"
	    });
	    // 后端文件变化
	    gulp.watch('home/js/*.js', function () {
	    	browserSync.reload;
	    });
	    // 前端文件变化
	    //gulp.watch('public/home/js/**/*.js', ['fontend-js-watch']);
	    // sass 编译
	    gulp.watch("home/scss/*.scss", ['sass']);
	    // 页面改动监听
	    gulp.watch("../Application/**/*.html").on('change', browserSync.reload);
	    //gulp.watch('src/public/home/images/**/*.{png, jpg, gif, ico}', ['imagemin']);
	});

	// 默认任务
	
	gulp.task('default', ['server']);

