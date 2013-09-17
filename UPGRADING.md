# 2.0.0

* A new `ImageOptimiserService` class was created to shared logic between GD and Imagick, due to this
you need to change instances of `OptimisedGDBackend` to `ImageOptimiserService` in your config yml files

* No longer does the module select the backend for you. You need to do this yourself by adding a `Image::set_backend()`
call to your `mysite/_config.php`