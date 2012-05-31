<?php

/**
 * OptimisedImage
 */
class OptimisedImage extends Image
{

    protected static $default_quality = 85;

    protected static $enabled = true;

    protected static $type_commands = array(
        2 => array(
            'jpegoptim' => 'jpegoptim -p -m$Quality --strip-all $Filename'
        ),
        3 => array(
            'optipng' => 'optipng $Filename',
            'pngcrush' => 'pngcrush -rem gAMA -rem cHRM -rem iCCP -rem sRGB -ow $Filename',
            'advpng' => 'advpng -z4 $Filename'
        )
    );

    protected static $enabled_commands = array();

    protected static $bin_directory = '';

    protected static $exec_ending = ' > /dev/null 2>&1 &';

    protected static $logging = false;

    protected static $logging_file = '../heyday-optimisedimage/logs/output.log';

    protected static $bg_processing = true;

    protected $quality = false;

    public static function setDefaultQuality($qual)
    {

        self::$default_quality = $qual;
        
    }

    public static function setEnabled($enabled)
    {

        self::$enabled = $enabled;

    }

    public static function setEnabledCommands($enabledCommands)
    {

        foreach ($enabledCommands as $type => $binName) {

            if (is_int($type)) {

                self::$enabled_commands[$type] = $binName;

            } else {

                self::$enabled_commands[self::getNumberByType($type)] = $binName;

            }

        }

    }

    public static function getNumberByType($imageType)
    {

        switch (strtolower($imageType)) {

            case 'gif':
                return 1;

            case 'jpg':
            case 'jpeg':
                return 2;

            case 'png':
                return 3;

        }

        return 0;

    }

    public static function addTypeCommand($imageType, $binName, $cmd)
    {

        self::$type_commands[self::getNumberByType($imageType)][$binName] = $cmd;

    }

    public static function setBinDirectory($directory)
    {

        self::$bin_directory = $directory;

    }

    public static function setExecEnding($ending)
    {

        self::$exec_ending = $ending;

    }

    public static function setLogging($logging)
    {

        self::$logging = $logging;

    }

    public static function setLoggingFile($file)
    {

        self::$logging_file = $file;

    }

    public static function setBackgroundProcessing($enabled)
    {

        self::$bg_processing = $enabled;

    }

    public function setQuality($qual)
    {

        $this->quality = $qual;

    }

    public function getQuality()
    {

        return $this->quality ? $this->quality : self::$default_quality;

    }

    public function generateFormattedImage($format, $arg1 = null, $arg2 = null)
    {

        $cacheFile = $this->cacheFilename($format, $arg1, $arg2);

        $gd = new GD(Director::baseFolder() . '/' . $this->Filename);

        if ($gd->hasGD()) {

            $generateFunc = "generate$format";

            if ($this->hasMethod($generateFunc)) {

                $gd = $this->$generateFunc($gd, $arg1, $arg2);

                $resampledFile = Director::baseFolder() . '/' . $cacheFile;

                if ($gd) {

                    $gd->writeTo($resampledFile);

                }

                if (self::$enabled) {

                    list($width, $height, $type, $attr) = getimagesize($resampledFile);

                    if (isset(self::$enabled_commands[$type]) && isset(self::$type_commands[$type][self::$enabled_commands[$type]])) {

                        $viewer = new SSViewer_FromString(self::$type_commands[$type][self::$enabled_commands[$type]]);

                        if (self::$logging) {

                            $output = array();

                            exec(self::$bin_directory . $viewer->process(new ArrayData(array(
                                'Quality' => $this->getQuality(),
                                'Filename' => $resampledFile
                            ))), $output);

                            if (count($output)) {

                                file_put_contents(self::$logging_file, implode(PHP_EOL, $output) . PHP_EOL, FILE_APPEND);
                                
                            }
                            
                        } else {

                            exec(self::$bin_directory . $viewer->process(new ArrayData(array(
                                'Quality' => $this->getQuality(),
                                'Filename' => $resampledFile
                            ))) . (self::$bg_processing ? self::$exec_ending : ''));
                            
                        }

                    }

                }
    
            } else {

                user_error("Image::generateFormattedImage - Image $format function not found.", E_USER_WARNING);

            }

        }

    }
    
}