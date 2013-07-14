# SilverStripe Optimised Image

Uses various binary tools like jpegoptim and optipng to optimise resampled images created by SilverStripe

For a SilverStripe `2.4` version check the `1.0` branch.

## Installation (with composer)

	$ composer require heyday/silverstripe-optimisedimage

## Usage

Configuration options:

* enabledCommands
* availableCommands
* binDirectory
* optimisingQuality

In order to enable the optimising of images resampled by SilverStripe, at the very least `enabledCommands` needs to be overridden in your own config.

This can be done as follows,

1. Create a file `mysite/_config/optimisedimage.yml`
2. Add the following contents

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
OptimisedGDBackend:
  enabledCommands:
    - jpegoptim
    - optipng
```

If you want to add your own commands, you can override `availableCommands` e.g.

```yml
---
After: 'silverstripe-optimisedimage/config#core'
---
OptimisedGDBackend:
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
OptimisedGDBackend:
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
OptimisedGDBackend:
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