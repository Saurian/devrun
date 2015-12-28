module.exports = (grunt) ->
  grunt.initConfig
    useminPrepare:
      html: ['app/modules/front-module/src/Presenters/templates/@layout.latte']
      options:
        dest: '.'

    uglify:
      options: {
        compress: {
          global_defs: {
            "DEBUG": false
          },
          dead_code: true
        }
      }

    netteBasePath:
      basePath: 'www'
      options:
        removeFromPath: ['app\\modules\\front-module\\src\\Presenters\\templates\\']

  # These plugins provide necessary tasks.
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-usemin'
  grunt.loadNpmTasks 'grunt-nette-basepath'

  # Default task.
  grunt.registerTask 'default', [
    'useminPrepare'
    'netteBasePath'
    'concat'
    'uglify'
    'cssmin'
  ]