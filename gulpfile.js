// Just Change theme folder Name in 'path';

const path = './assets/';
const gulp = require( 'gulp' );
const rename = require( 'gulp-rename' );
const terser = require('gulp-terser');
const inject = require('gulp-inject');

/*clone style.min from dist to .php for template */
const jsToPhp = () => {
	var sources = gulp.src(path + 'dist/js/script.min.js', {
		allowEmpty: true,
	});
	return gulp.src('./includes/lazify_script.php').pipe(inject(sources, {
		relative: true,
		starttag: '/* {{name}}:{{ext}} */',
		endtag: '/* endinject */',
		transform: function (filePath, file) {
			return file.contents.toString('utf8')
		}
	}))
	.pipe(gulp.dest('./includes'));
};

/* Minify JS*/
const minifyJs = () => {
	return gulp.src(path + 'build/js/script.js')
		.pipe(terser())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest(path + 'dist/js/'));
};

/*Watch JS*/
const watchJs = () => {
	gulp.watch(path + 'build/js/script.js', minifyJs);
};

/*Watch CSS MAIN*/
const watchFinalJs = () => {
	gulp.watch( path + 'dist/js/script.min.js' , jsToPhp );
}
const build = gulp.parallel(watchJs, watchFinalJs);

exports.minifyJs = minifyJs;
exports.watchFinalJs = watchFinalJs;
exports.watchJs = watchJs;
exports.jsToPhp = jsToPhp;

gulp.task( 'default', build );
