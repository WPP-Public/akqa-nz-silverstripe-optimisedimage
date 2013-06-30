<?php

/**
 * Class ImageOptimiserInterface
 */
interface ImageOptimiserInterface
{
    /**
     * @param $filename
     * @return mixed
     */
    public function optimiseImage($filename);
}
