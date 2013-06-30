# SilverStripe Optimised Image

Uses various binary tools like jpegoptim and optipng to optimise resampled images create by SilverStripe

For a SilverStripe `2.4` version check the `1.0` branch.

## License

Optimised image is licensed under an [MIT license](http://heyday.mit-license.org/)

## Installation (with composer)

	$ composer require heyday/silverstripe-dataobjectpreview

## Usage

Extend `OptimisedImage` in your custom image class, or set `OptimisedImage` as your field type in your `has_one` array.