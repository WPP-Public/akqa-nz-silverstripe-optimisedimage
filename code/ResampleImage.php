<?php

/**
 * Class ResampleImage
 *
 */
class ResampleImage extends DataExtension
{
    /**
     * @var
     */
    protected $config;

    /**
     * @param Config_ForClass $config
     */
    public function __construct(Config_ForClass $config = null)
    {
        parent::__construct();
        $this->config = $config ?: Config::inst()->forClass(__CLASS__);
        if ($memLimit = $this->config->get('memory')) {
            increase_memory_limit_to($memLimit);
        }
    }

    /**
     * Get the max Y dimensions for image resampling from the yaml configs
     *
     * @return int
     */
    public function getMaxX()
    {
        return (int) $this->config->get('max_x');
    }

    /**
     * Get the max Y dimensions for image resampling from the yaml configs
     *
     * @return int
     */
    public function getMaxY()
    {
        return (int) $this->config->get('max_y');
    }

    /**
     * Resamples the image to the maximum Height and Width
     */
    public function resampleImage()
    {
        $extension = strtolower($this->owner->getExtension());

        if($this->owner->getHeight() > $this->getMaxX() || $this->owner->getWidth() > $this->getMaxY()) {
            $original = $this->owner->getFullPath();
            $resampled = $original. '.tmp.'. $extension;

            $gd = new GD($original);

            if($gd->hasImageResource()) {
                $gd = $gd->resizeRatio($this->getMaxX(), $this->getMaxY());

                if($gd) {
                    $gd->writeTo($resampled);
                    unlink($original);
                    rename($resampled, $original);
                }
            }
        }
    }

    public function onAfterUpload()
    {
        $this->resampleImage();
    }

    public function onAfterWrite()
    {
        $this->resampleImage();
    }

}
