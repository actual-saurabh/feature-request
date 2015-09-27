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
                
                sassDir: 'public/assets/sass',
                cssDir: 'public/assets/css'
            },
            front_dev: {
                options: {
                    environment: 'development',
                    watch:true,
                    trace:true,
                    outputStyle: 'compact' // nested, expanded, compact, compressed.
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
         
              // makes all src relative to cwd 
              {expand: true, cwd: 'public/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/public/'},

              //copy template folder
              {expand: true, cwd: 'templates/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/templates/'},

              //copy admin
              {expand: true, cwd: 'admin/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/admin/'},
              //copy includes
              {expand: true, cwd: 'includes/', src: ['**'], dest: 'e:/xampp/htdocs/wp/wp-content/plugins/feature-request/includes/'}
         
            ],
          },
        },

    });

    // register task
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('default', ['sass']);

};