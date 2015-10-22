'use strict';
module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        // watch our project for changes
        watch: {
            sass: {
                files: ['public/assets/sass/front/**/*', 'public/assets/sass/admin/**/*'],
                tasks: ['sass']
            },
            livereload: {
                options: { livereload: true },
                files: ['public/assets/**/*', 'admin/assets/**/*', '**/*.html', '**/*.php', 'public/assets/img/**/*.{png,jpg,jpeg,gif,webp,svg}']
            }
        },
        
        // watch and compile scss files to css
        compass: {
            front_dev: {
                options: {
                    sassDir: 'public/assets/sass/front',
                    cssDir: 'public/assets/css',
                    environment: 'development',
                    watch:true,
                    trace:true,
                    outputStyle: 'compressed' // nested, expanded, compact, compressed.
                },
            },
            back_dev: {
                options: {
                    sassDir: 'public/assets/sass/admin',
                    cssDir: 'admin/assets/css',
                    environment: 'development',
                    watch:true,
                    trace:true,
                    outputStyle: 'compressed' // nested, expanded, compact, compressed.
                },
            }

        },
    

        uglify: {
            publicscripts: {
                options:{
                    preserveComments:'some'
                },
                files: {
                        'public/assets/js/feature-request.js': [
                        'public/assets/js/transition.js',
                        'public/assets/js/load-posts.js',
                        'public/assets/js/general.js',
                        'public/assets/js/jquery.form.min.js',
                        'public/assets/js/textext.core.js',
                        'public/assets/js/textext.plugin.autocomplete.js',
                        'public/assets/js/textext.plugin.tags.js'

                    ]
                }
            }
        },

         // Running multiple blocking tasks
        concurrent: {
            watch_frontend_scss: {
                tasks: ['compass', 'watch'],
                options: {
                    logConcurrentOutput: true
                }
            }
        },

        copy: {
          main: {
            files: [
          
              //copy public folder
              {expand: true, cwd: 'public/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/public/'},

              //copy template folder
              {expand: true, cwd: 'templates/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/templates/'},

              //copy admin folder
              {expand: true, cwd: 'admin/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/admin/'},
              //copy includes folder
              {expand: true, cwd: 'includes/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/includes/'}
         
            ],
          },

          release: {
            files: [
              // makes all src relative to cwd 
              { expand: true, 
              	src: ['**', '!release', '!?.', '!node_modules/**/*', '!node_modules', '!*.md', '!*.json', '!Gruntfile.js', '!*.txt', 
              		  '!public/assets/sass/**/*', '!public/assets/sass', '!wp-assets', '!.*'], 
              	dest: 'release/'},
            ],
          },

        },

    	clean: {
    		release: ['release/*'],
    	},


    	// deploy via rsync
        deploy: {
            options: {
                args: ["--verbose -zP"], // z:compress while transfering data, P: display progress
                exclude: ['.git*', 'node_modules', '.sass-cache', 'Gruntfile.js', 'package.json', 'public/assets/sass',
                          '.*', 'README.md', 'config.rb', '.jshintrc', 'bower.json', 'deploy.sh', 'deploy-build.sh',
                          'bower_components','build', 'contributors.txt', 'config.rb', 'wp-assets', 'release'
                ],
                recursive: true,
                syncDestIgnoreExcl: true
            },

            build: {
                options: {
                    src: "./",
                    dest: "build/feature-request/"
                }
            }
        }

    });
	
	// rename tasks
    grunt.renameTask('rsync', 'deploy');

    // register task
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('default', ['sass']);
    grunt.registerTask('build'  , ['clean', 'copy:release']);
    grunt.registerTask('release', ['deploy:build']);
};