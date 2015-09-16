'use strict';
module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        // watch our project for changes
        watch: {
            sass: {
                files: ['public/assets/sass/**/*'],
                tasks: ['sass']
            },
            livereload: {
                options: { livereload: true },
                files: ['public/assets/**/*', 'admin/assets/**/*', '**/*.html', '**/*.php', 'public/assets/img/**/*.{png,jpg,jpeg,gif,webp,svg}']
            }
        },
        
        // watch and compile scss files to css
        compass: {
            options: {
                
                sassDir: 'css/sass',
                cssDir: 'css/sass-output'
            },
            front_dev: {
                options: {
                    environment: 'development',
                    watch:true,
                    trace:true,
                    outputStyle: 'compact' // nested, expanded, compact, compressed.
                    

                },
                files: {
                    'public/assets/css/feature-request.css': 'public/assets/sass/master.scss'
                }
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
                          'public/assets/js/modal.js',
                          'public/assets/js/load-posts.js',
                          'public/assets/js/general.js',
                        'public/assets/js/jquery.form.min.js',
                        'public/assets/js/textext.core.js',
                        'public/assets/js/textext.plugin.autocomplete.js',
                        'public/assets/js/textext.plugin.ajax.js',
                        'public/assets/js/textext.plugin.arrow.js',
                        'public/assets/js/textext.plugin.filter.js',
                        'public/assets/js/textext.plugin.clear.js',
                        'public/assets/js/textext.plugin.focus.js',
                        'public/assets/js/textext.plugin.prompt.js',
                        'public/assets/js/textext.plugin.suggestion.js',
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
         
              // makes all src relative to cwd 
              {expand: true, cwd: 'public/', src: ['**'], dest: '../../../../../wamp/www/wp/wp-content/plugins/feature-request/public/'},

              //copy template folder
              {expand: true, cwd: 'templates/', src: ['**'], dest: '../../../../../wamp/www/wp/wp-content/plugins/feature-request/templates/'},

              //copy admin
              {expand: true, cwd: 'admin/', src: ['**'], dest: '../../../../../wamp/www/wp/wp-content/plugins/feature-request/admin/'},
              //copy includes
              {expand: true, cwd: 'includes/', src: ['**'], dest: '../../../../../wamp/www/wp/wp-content/plugins/feature-request/includes/'}
         
            ],
          },
        },

    });

    // register task
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('default', ['sass']);

};