#Heyday silverstripe-optimisedimage

Uses various binary tools like jpegoptim and optipng to optimise resampled images create by SilverStripe

##License

Publish permissions is licensed under an [MIT license](http://heyday.mit-license.org/)

##Installation

###Non-composer

To install just drop the silverstripe-optimisedimage directory into your SilverStripe root and run a /dev/build/?flush=1

###Composer

Add the following to your composer.json file:

```json
{
	"require": {
		"heyday/silverstripe-optimisedimage": "*"
	}
}
```

###Usage

Extend `OptimisedImage` in your custom image class, or set `OptimisedImage` as your field type in your `has_one` array.