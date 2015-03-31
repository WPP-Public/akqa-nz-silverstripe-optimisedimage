# SilverStripe Optimised Image

This module provides two image manipulation services that can be configured independently:

* Run command-line image optimisation tools on images resampled by SilverStripe: reduces the file size of the generated images without reducing fidelity in a noticeable way
* Resize images on upload to fit within configured dimensions: prevents images with massive dimensions entering the assets and becoming a memory problem when resampling.

For a SilverStripe `2.4` version check the `0.1` branch.

Configurations for some common image optimisation tools (eg. jpegoptim and optipng) are provided as part of this module, though any command line program can be used with the image optimisation service. No optimisation program binaries are bundled with this module, so you'll need to install any programs you want to use on the target system.

Note that only resampled images are optimised by the optimisation service module (eg. using CroppedImage, SetWidth, SetHeight, PaddedResize, etc). If you want to optimise images without resizing them, [there is currently a workaround to trigger optimisation](https://github.com/heyday/silverstripe-optimisedimage/issues/4#issuecomment-60821831).


## Installation (with composer)

	$ composer require heyday/silverstripe-optimisedimage


## Usage

### Resampling Images on upload

By default, resampling on upload is not enabled. To activate it, the `ResampleImage` extension needs to be added to `Image`. In your `mysite/_config/config.yml` add:

```yml
Image:
  extensions:
    - ResampleImage
```

The default maximum width & height for uploaded images is **1024 x 1024** pixels. Images larger than this will be scaled to fit their largest dimension to this size.

You can set your own maximum height and width for uploaded images by overriding the default config in your `mysite/_config/config.yml`:

```yml
ResampleImage:
  max_x: 2000
  max_y: 2500
```

Note that resampling on upload is a destructive process: the original uploaded image is discarded.


### Optimising SilverStripe Resampled Images - Selecting a backend

The image backend that SilverStripe uses needs to be changed to either `OptimisedGDBackend` or `OptimisedImagickBackend` depending on whether
you want to us GD or Imagick. To do this, in your `mysite/_config/config.yml` add:

```yml
Image:
    backend: OptimisedGDBackend

// or

Image:
    backend: OptimisedImagickBackend
```

Configuration options:

* enabledCommands
* availableCommands
* binDirectory
* optimisingQuality

At the very least `enabledCommands` needs to be overridden in your own config in order to enable the optimising of images resampled by SilverStripe.

This can be done as follows,

1. Create a file `mysite/_config/optimisedimage.yml`
2. Add the following contents

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
ImageOptimiserService:
  enabledCommands:
    - jpegoptim
    - optipng
```

If you want to add your own commands, you can override `availableCommands` e.g.

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
ImageOptimiserService:
  enabledCommands:
    - jpegoptim
    - optipng
  availableCommands:
    jpg:
      jpegoptim: '%s/jpegoptim -p --strip-all --all-progressive %s'
    png:
      optipng: '%s/optipng %s -o 1 -strip all -i 1'
    gif:
      optipng: '%s/optipng %s -o 1 -strip all'
```

If your binaries are not located at `/usr/local/bin/` you can override this by setting `binDirectory`

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
ImageOptimiserService:
  enabledCommands:
    - jpegoptim
    - optipng
  binDirectory: '/home/user/bin/'
```

If your binaries aren't all located in the same directory you will need to manually enter the command by overriding `availableCommands`

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
ImageOptimiserService:
  enabledCommands:
    - jpegoptim
    - optipng
  availableCommands:
    jpg:
      jpegoptim: '/my/special/path/jpegoptim -p -m%3$d --strip-all %2$s'
```

Commands have certain variables exposed to them when they are built, this is done by `sprintf`, the variables available are in the following order:

1. `binDirectory`
2. File path to be optimised
3. `optimisingQuality`

If when defining a custom command you need to use these arguments in a different order, you will need to use a position specifier, see [PHP sprintf](http://php.net/manual/en/function.sprintf.php#example-4811)

## License

Optimised image is licensed under an [MIT license](http://heyday.mit-license.org/)
