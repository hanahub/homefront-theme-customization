var gulp = require('gulp');
var hash = require('gulp-hash');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');
var autoprefixer = require('gulp-autoprefixer');
var browserSync = require('browser-sync');
var uglify = require('gulp-uglify');
var cssnano = require('gulp-cssnano');
var imagemin = require('gulp-imagemin');
var cache = require('gulp-cache');
var del = require('del');
var runSequence = require('run-sequence');
var config = {
    browserslist: [
        '> 1%',
        'last 5 versions',
        'IE >= 9',
        'Firefox ESR',
        'Opera 12.1'
    ]
};

// Development Tasks 
// -----------------

// Start browserSync server
gulp.task('browser-sync', function() {
  var env = require('./env.json');
  var files = [
    'assets/dist/css/style.min.css',
    './*.php'
    ];

  browserSync.init(files, {
    proxy: env.env,
    notify: true,
    browser: "google chrome"
  });
});

gulp.task('styles', function(){
  del(['assets/dist/css/*.css']).then(paths => {
    return gulp.src(['assets/src/sass/**/*.scss'])
      .pipe(plumber({
        errorHandler: function (error) {
          console.log(error.message);
          this.emit('end');
      }}))
      .pipe(hash())
      .pipe(sass())
      .pipe(autoprefixer({ browsers: config.browserslist }))
      // .pipe(gulp.dest('dist/css/'))
      .pipe(rename({suffix: '.min'}))
      .pipe(cssnano())
      .pipe(gulp.dest('assets/dist/css/'))
      .pipe(browserSync.reload({stream:true}))
  });
});

gulp.task('scripts', function(){
  del(['assets/dist/js/*.js']).then(paths => {
    return gulp.src('assets/src/js/**/*.js')
      .pipe(plumber({
        errorHandler: function (error) {
          console.log(error.message);
          this.emit('end');
      }}))
      .pipe(hash())
      .pipe(rename({suffix: '.min'}))
      .pipe(uglify())
      .on('error', function (err) { gutil.log(gutil.colors.red('[Error]'), err.toString()); })
      .pipe(gulp.dest('assets/dist/js/'))
      .pipe(browserSync.reload({stream:true}))
  });
});

// Watchers
gulp.task('watch', function() {
  gulp.watch('assets/src/scss/**/*.scss', ['styles']);
  gulp.watch('templates/*.php', browserSync.reload);
  gulp.watch('assets/src/js/**/*.js', browserSync.reload);
})

// Optimizing Images 
gulp.task('images', function() {
  return gulp.src('assets/src/images/**/*.+(png|jpg|jpeg|gif|svg)')
    // Caching images that ran through imagemin
    .pipe(cache(imagemin({
      interlaced: true,
    })))
    .pipe(gulp.dest('assets/dist/images'))
});

// Copying fonts 
gulp.task('fonts', function() {
  return gulp.src('assets/fonts/**/*')
    .pipe(gulp.dest('assets/dist/fonts'))
})

// Cleaning 
gulp.task('clean', function() {
  return del.sync('dist').then(function(cb) {
    return cache.clearAll(cb);
  });
})

gulp.task('clean:dist', function() {
  return del.sync(['assets/dist/**/*', '!assets/dist/images', '!assets/dist/images/**/*']);
});

// Build Sequences
// ---------------

gulp.task('default', function(callback) {
  runSequence(['styles', 'scripts', 'images', 'fonts'],
    callback
  )
})

gulp.task('build', function(callback) {
  runSequence(['styles', 'scripts', 'images', 'fonts'],
    callback
  )
})