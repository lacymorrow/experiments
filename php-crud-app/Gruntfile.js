module.exports = function(grunt) {
  require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*!\n' +
          ' * <%= pkg.name%> v<%= pkg.version %> (<%= pkg.homepage %>)\n' +
          ' * Copyright 2014-<%= grunt.template.today("yyyy") %> <%= pkg.author %>\n' +
          ' * Licensed under <%= _.pluck(pkg.licenses, "type") %>\n' +
          ' */\n',
    jqueryCheck: 'if (typeof jQuery === \'undefined\') { throw new Error(\'Bootstrap requires jQuery\') }\n\n',
    /* EXECUTE BUILD ASSET GRUNT - 
    Remember: to add new assets install to devDependencies (package.json) and copy list below.
    You are responsible for keeping assets up to date (e.g. git pull) */
    exec: {
      /* ng: {
        command: '(cd assets/angular.js;git pull;npm install;grunt -f)'
      }, */
      bootstrap: {
        command: '(cd assets/bootstrap;git pull;npm install;grunt -f)'
      }
    },
    copy: {
    /* BUILD ASSETS TO COPY */
      bootstrap: {
        expand: true,
        cwd: 'assets/bootstrap/dist/',
        src: ['**'],
        dest: 'dist/assets/bootstrap/'
      },
      /*
      ng: {
        expand: true,
        cwd: 'assets/angular.js/build/',
        src: ['**'],
        dest: 'dist/assets/ng/'
      },
      */
    /* END OF BUILD ASSETS */
      app: {
        expand: true,
        cwd: 'src/',
        src: ['app/**'],
        dest: 'dist/'
      },
      favicon: {
        expand: true,
        cwd: 'src/img/',
        src: 'favicon.ico',
        dest: 'dist/'
      },
      fonts: {
        expand: true,
        cwd: 'src/fonts/',
        src: ['**'],
        dest: 'dist/fonts/'
      },
      html: {
        expand: true,
        cwd: 'src/',
        src: ['html/**'],
        dest: 'dist/'
      },
      img: {
        expand: true,
        cwd: 'src/',
        src: ['img/**'],
        dest: 'dist/'
      },
      index: {
        expand: true,
        cwd: 'src/',
        src: ['index*'],
        dest: 'dist/'
      }
    },
    autoprefixer: {
        options: {
            browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'ie 9']
        },
        dist: {
          expand: true,
          flatten: true,
          src: 'dist/css/*.css',
          dest: 'dist/css/'
        },
    },
    clean: {
      less: 'dist/css/less.css',
      dist: 'dist/*'
    },
    concat: {
      options: {
        banner: '<%= banner %>\n',
        stripBanners: false
      },
      css: {
        src: ['src/css/**/*.css'],
        dest: 'dist/css/<%= pkg.name %>.css'
      },
      js: {
        options: {
          banner: '<%= banner %>\n<%= jqueryCheck %>\n'
        },
        src: ['src/js/**/*.js'],
        dest: 'dist/js/<%= pkg.name %>.js'
      },
      php: {
      	options: {
	        banner: '',
	        stripBanners: true
	      },
        src: ['src/app/includes/functions/**/*.php'],
        dest: 'dist/app/includes/functions.php'
      },
    },
    connect: {
      server: {
        options: {
          open: true,
          port: 8000,
          base: 'dist/'
        }
      }
    },
    csscomb: {
      dist: {
        files: {
          'dist/css/<%= pkg.name %>.css': 'dist/css/<%= pkg.name %>.css',
          'dist/css/less.css': 'dist/css/less.css'
        }
      }
    },
    csslint: {
      src: [
        '<%= cssmin.dist.dest %>'
      ]
    },
    cssmin: {
      dist: {
        options: {
          noAdvanced: true, // turn advanced optimizations off until the issue is fixed in clean-css
          selectorsMergeMode: 'ie8',
          keepSpecialComments: 0
        },
        src: [
          'dist/css/*.css',
          '!*.min.css'
        ],
        dest: 'dist/css/<%= pkg.name %>.min.css'
      }
    },
    htmlbuild: {
      dist: {
        src: 'index.html',
        dest: 'dist/',
        options: {
          beautify: true,
          relative: true,
          styles: {
            bundle: [
              '**/*.css'
            ]
          },
          scripts: {
            app: '**/*.js'
          }
        }
      }
    },
    imagemin: {
      dist: {
        files: [{
          expand: true,
          //cwd: '', 
          src: ['src/img/**/*'],
          dest: 'dist/img/'
        }]
      }
    },
    jscs: {
      grunt: {
        src: 'Gruntfile.js'
      },
      src: {
        src: 'src/js/*.js'
      },
    },
	  jshint: {
      dist: ['Gruntfile.js', 'src/js/**/*.js', 'test/**/*.js'],
      options: {
        // options here to override JSHint defaults
        globals: {
          jQuery: true,
          console: true,
          module: true,
          document: true
        }
      }
    },
    less: {
      dist: {
        options: {
          cleancss: true,
        },
        files: {
          "dist/css/less.css": "src/less/<%= pkg.name %>.less"
        }
      }
    },
    php: {
        server: {
            options: {
                port: 8000,
                base: 'dist/',
                open: true
            }
        }
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        files: {
          'dist/js/<%= pkg.name %>.min.js': ['<%= concat.js.dest %>']
        }
      }
    },
    watch: {
      files: ['<%= jshint.dist %>','src/**/*'],
      tasks: ['dist']
    }
  });

  grunt.registerTask('test', ['jshint']);
  grunt.registerTask('assets', ['exec', 'copy']);
  grunt.registerTask('js', ['concat:js', 'uglify']);
  grunt.registerTask('css', ['less', 'concat:css', 'clean:less', 'autoprefixer', 'csscomb', 'cssmin']);
  grunt.registerTask('static', ['copy',/* 'imagemin', 'htmlbuild' */]);
  grunt.registerTask('dist', ['clean:dist', 'js', 'css', 'concat:php', 'static']);
  grunt.registerTask('serve', ['dist',/* 'connect', */ 'php', 'watch']);
  grunt.registerTask('all', ['assets','test','dist']);
  grunt.registerTask('default', ['dist']);
};
