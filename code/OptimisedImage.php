<?php

class OptimisedImage extends Image
{

	protected static $default_quality = 85;
	protected static $enabled = true;
	protected static $type_commands = array(
		2 => 'jpegoptim -p -m$Quality --strip-all $Filename',
		3 => 'optipng $Filename'
	);
	protected static $bin_directory = '';
	protected static $exec_ending = ' > /dev/null 2>&1 &';
	protected static $logging = false;
	protected static $logging_file = '../heyday-optimisedimage/logs/output.log';
	protected static $background_processing = true;

	protected $quality = false;

	public static function set_default_quality($qual)
	{

		self::$default_quality = $qual;
		
	}

	public static function set_enabled($enabled)
	{

		self::$enabled = $enabled;
		
	}

	public static function add_type_command($image_type, $cmd)
	{
		
		switch (strtolower($image_type)) {

			case 'gif':
				self::$type_commands[1] = $cmd;
				break;

			case 'jpg':
			case 'jpeg':
				self::$type_commands[2] = $cmd;
				break;

			case 'png':
				self::$type_commands[3] = $cmd;
				break;

		}

	}

	public static function set_bin_directory($directory)
	{

		self::$bin_directory = $directory;
		
	}

	public static function set_exec_ending($ending)
	{

		self::$exec_ending = $ending;
		
	}

	public static function set_logging($logging)
	{

		self::$logging = $logging;
		
	}

	public static function set_logging_file($file)
	{

		self::$logging_file = $file;
		
	}

	public static function set_background_processing($enabled)
	{

		self::$background_processing = $enabled;
		
	}

	public function setQuality($qual)
	{

		$this->quality = $qual;
		
	}

	public function getQuality()
	{

		return $this->quality ? $this->quality : self::$default_quality;

	}
	
	function generateFormattedImage($format, $arg1 = null, $arg2 = null) {

		$cacheFile = $this->cacheFilename($format, $arg1, $arg2);
	
		$gd = new GD(Director::baseFolder()."/" . $this->Filename);
		
		
		if($gd->hasGD()){

			$generateFunc = "generate$format";

			if($this->hasMethod($generateFunc)){

				$gd = $this->$generateFunc($gd, $arg1, $arg2);

				$resampledFile = Director::baseFolder() . '/' . $cacheFile;

				if($gd){

					$gd->writeTo($resampledFile);

				}

				if (self::$enabled) {

					list($width, $height, $type, $attr) = getimagesize($resampledFile);

					if (isset(self::$type_commands[$type])) {

						$viewer = new SSViewer_FromString(self::$type_commands[$type]);

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
							))) . (self::$background_processing ? self::$exec_ending : ''));
							
						}

					}

				}
	
			} else {

				USER_ERROR("Image::generateFormattedImage - Image $format function not found.",E_USER_WARNING);

			}

		}

	}
	
}