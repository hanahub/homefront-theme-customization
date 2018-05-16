# MyHeritage Landing Pages

Follow this guide for build and deploy

### First time Installation

Assuming you have npm and gulp installed in your system, in theme dir, do the following:

```sh
$ npm install
$ gulp
```

You can also use gulp individual tasks:

```sh
$ gulp styles
$ gulp scripts
$ gulp fonts
$ gulp images
```

and:

```sh
$ gulp watch
```

For a full build, just run:

```sh
$ gulp
```

### Sources vs Destinations

| Directory | Note |
| ------ | ------ |
| `assets/src/sass` | Put your .scss files there. |
| `assets/src/js/scripts.js` | Put your JS code there. |
| `assets/src/images` | Put your images there. |
| `assets/vendors` | Put external plugins CSS/JS files there |

Please put your files in `src` and `vendor` dirs under assets. The `dist` dir under assets is ignored and recreated on every deployment, any files you put in the dist dir will be deleted on each gulp rebuild.

You should then link to images and files from the dist dir: `assets/dist/images/your-file-in-src.ext` . Note that images will be compressed and saved to the dist dir when `gulp images` or `gulp` runs.

Gulp will compress sass files to `main-hash.min.css` example `main-1bc5a818.min` and js file to `main.hash.min.js`.

Use the function `vendor_assets()` in the `homefront-theme-customization.php` file to enqueue vendor files from the `assets/vendors` directory.

### Modifying theme templates in the plugin

We're following homefront theme recommendation and making our customization in a plugin, instead of the child theme so we can also update the child theme when an update is available.

So in order to modify a template, for example `page.php`, create a file called page.php in the `templates` directory. If the directory doesn't exist, it means you're the first one trying to modify a theme template file! (congrats), just create a directory called `templates` in the plugin's root.

Same for Woocommerce template, if you'd like to modify/overwrite woocommerce templates, the woocommerce directory should go inside the templates dir example: `<plugindir>/templates/woocommerce/cart/cart.php`.

If you need to ask a question, please contact Eran/Bishoy via slack channel.

Enjoy!