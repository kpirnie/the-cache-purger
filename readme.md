# Kevin's Wordpress Setup

## Requirements

* Node - 16.13.1 and up
* NVM - latest (this allows you to quickly change node/npm versions if you need to)
* NPM - 8.3.0 and up
* Gulp - 4.0.2 and up

Once you have node and npm setup you may need to install some other modules.  You can do this from inside your project, though it should get done automagically, if not run these inside your project... or swap the `--save-dev` for `-g` to install them globally.

```
npm install --save-dev gulp@latest
npm install --save-dev sass@latest
npm install --save-dev gulp-sass@latest
npm install --save-dev gulp-uglify@latest
npm install --save-dev gulp-cssnano@latest
npm install --save-dev gulp-imagemin@latest
npm install --save-dev gulp-svgo@latest
npm install --save-dev gulp-wp-pot@latest
npm install --save-dev gulp-concat@latest
npm install --save-dev gulp-rename@latest
npm install --save-dev gulp-replace@latest
npm install --save-dev del@latest
npm install --save-dev fs@latest
```

## Description

NPM and Gulp setup to compile Sass, minify and concatenate CSS, minify and concatenate Javascript files, and optimize images.  Process ignores `assets/css/custom.css` and `assets/js/custom.js` from compiling.

Will generate POT files for template/plugin language translations.

Assumes the following folder structure

```
/
    - this should contain your theme or plugin files
/assets/
    - css/
    - scss/
    - js/
    - images/
    - fonts/
```

## Setup

* Clone this repo.
* Open `package.json` and configure everything that shows `your*`
    * You can also change the `source` and `distribution` names, these specify your projects source files and the distribution output.
    * You can also change the `production`.`shouldcopy` to `true` and set the `path` if you want to copy the compiled distribution output to another location.
* Run `npm install`
* Copy your source files into a directory inside this repo called `source`
* Run `gulp`
* Copy the files inside the `dist` folder to your staging/development/production environment.
    * Unless you automated this with the above `production`.`shouldcopy` configuration.
* Commit your changes to a new repo
