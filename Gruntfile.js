module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        sass:{
          dist:{
              options:{
                  //style: 'compressed'
              },
              files:{
                'css/style.css':'sass/style.scss'
              }
          }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-sass');

    // Default task(s).
    grunt.registerTask('default', ['sass']);

};