<?php

if (class_exists('ImagickBackend')) {

/**
 * Class OptimisedImagickBackend
 */
class OptimisedImagickBackend extends ImagickBackend
{
    /**
     * @var ImageOptimiserService
     */
    protected $optimiserService;
    /**
     * @param ImageOptimiserService $optimiserService
     */
    public function setOptimiserService(ImageOptimiserService $optimiserService)
    {
        $this->optimiserService = $optimiserService;
    }
    /**
     * Calls the original writeTo function and then after that completes optimises the image
     * @param string $filename
     */
    public function writeTo($filename)
    {
        parent::writeTo($filename);

        if ($this->optimiserService instanceof ImageOptimiserInterface) {
            $this->optimiserService->optimiseImage($filename);
        }
    }
}

}
