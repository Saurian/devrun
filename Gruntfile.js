module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		copy: {
			main: {
				files: [
					{expand: true, cwd: 'bower_components/nette-forms/src/assets/', src: 'netteForms.js', dest: 'www/assets/nette-forms/'},
					{expand: true, cwd: 'bower_components/bootstrap/dist/', src: '**', dest: 'www/assets/bootstrap/'},
					{expand: true, cwd: 'bower_components/bs-slider/', src: ['js/*.js', 'css/*'], dest: 'www/assets/bootstrap-slider/'},
                    {expand: true, cwd: 'bower_components/font-awesome/', src: ['fonts/**', 'css/*'], dest: 'www/assets/font-awesome/'},
					{expand: true, cwd: 'bower_components/jquery/dist/', src: '**', dest: 'www/assets/jquery/', filter: 'isFile'},
                    {expand: true, cwd: 'bower_components/jquery-ui/', src: ['themes/base/**', 'jquery-ui.min.js'], dest: 'www/assets/jquery-ui/', filter: 'isFile'},
					{expand: true, cwd: 'bower_components/select2/dist/', src: '**', dest: 'www/assets/select2/'},
					{expand: true, cwd: 'bower_components/nette.ajax.js/', src: ['nette.ajax.js', 'extensions/*.js'], dest: 'www/assets/nette.ajax.js/'},
					{expand: true, cwd: 'bower_components/history.nette.ajax.js/client-side', src: 'history.ajax.js', dest: 'www/assets/history.ajax.js/'},
					{expand: true, cwd: 'bower_components/qtip2/basic/', src: '*', dest: 'www/assets/jquery.qtip/'},
					{expand: true, cwd: 'bower_components/Ionicons/', src: ['css/*', 'fonts/*', 'png/**'], dest: 'www/assets/Ionicons/'},
					{expand: true, cwd: 'bower_components/adminlte/', src: ['dist/css/**', 'dist/img/**', 'dist/js/app.js', 'bootstrap/**', 'plugins/**'], dest: 'www/assets/adminlte/'},

				]
			}

		},

	  	uglify: {
	  		options: {
		        beautify: true
		    },
			js: {
				files: {
					'Resources/public/js/application.min.js': ['Resources/public/js/*.js', '!js/*.min.js']
				}
			}
		},

		cssmin: {
			combine: {
				files: {
					'Resources/public/css/application.min.css': ['Resources/public/css/application.css']
				}
			},
			minify: {
				expand: true,
				cwd: 'www/css/',
				src: ['index.css', 'legacy_ie.css', 'custom.css'],
				dest: 'www/webcache/'
			}
		},

		imagemin: {
			dynamic: {
				options: {
					optimizationLevel: 3
				},
				files: [{
					expand: true,
					cwd: 'Resources/public/',
					src: ['**/*.{png,jpg,gif}'],
					dest: 'Resources/public/'
				}]
			}
		},

        autoprefixer: {
            dist: {
                options: {
                    browsers: ['last 1 version', '> 1%', 'ie 8', 'ie 7']
                },
                files: {
                    'www/css/index.css': ['www/css/style.css'],
                    'www/css/custom.css': ['www/css/custom.css']
                }
            }
        },

        shell: {
            options: {
                stderr: false
            },
            commandsList: {
                command: 'php www/index.php',
                options: {
                    stdout: true,
                    stderr: false,
                    execOptions: {
                        encoding : 'utf8'
                    }
                }
            },
            validateSchema: {
                command: 'php www/index.php orm:validate-schema'
            },
            dumpSchema: {
                command: 'php www/index.php orm:schema-tool:update --dump-sql'
            },
            updateSchema: {
                command: 'php www/index.php orm:schema-tool:update --force'
            }
        }

		//watch: {
		//	css: {
		//		files: ['Resources/public/*/*.scss'],
		//		tasks: ['autoprefixer', 'cssmin']  // 'sass',
		//	},
        //
		//	imagemin: {
		//		files: [
		//			'Resources/public/*/*.jpg',
		//			'Resources/public/*/*.jpeg',
		//			'Resources/public/*/*.png',
		//			'Resources/public/*/*.gif'
		//		],
		//		tasks: ['imagemin']
		//	}
		//}
	});

    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	//grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-autoprefixer');

	//grunt.registerTask('default', ['copy', 'uglify', 'autoprefixer', 'cssmin', 'imagemin']);  // 'sass',
	grunt.registerTask('watch', ['watch']);
    grunt.registerTask('terminal', ['shell']);
};
