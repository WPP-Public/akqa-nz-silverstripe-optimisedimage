<?php

use Symfony\Component\Process\Process;

/**
 * Class ImageOptimiserService
 */
class ImageOptimiserService implements ImageOptimiserInterface
{
    /**
     * @var Config_ForClass
     */
    protected $config;
    /**
     * @var Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param Config_ForClass          $config
     */
    public function __construct(Psr\Log\LoggerInterface $logger = null, Config_ForClass $config = null)
    {
        $this->config = $config ?: Config::inst()->forClass(__CLASS__);
        if ($logger) {
            $this->logger = $logger;
        }
    }
    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
    /**
     * @param $filename
     * @return mixed|void
     */
    public function optimiseImage($filename)
    {
        if (file_exists($filename)) {
            $size = @getimagesize($filename);
            if (!is_array($size)) {
                if (null !== $this->logger) {
                    $this->logger->error("Error reading file when attempting to optimize");
                }
                return;
            }
            list($width, $height, $type, $attr) = $size;

            $commands = $this->getCommands($filename, $type = $this->getImageType($type));

            foreach ($commands as $command) {
                try {
                    $process    = $this->execCommand($command);
                    $successful = in_array($process->getExitCode(), $this->config->get('successStatuses'));

                    if (null !== $this->logger && (!$successful || $this->config->get('debug'))) {
                        // Do this so the log isn't treated as a web request in raven
                        $requestMethod = $_SERVER['REQUEST_METHOD'];
                        unset($_SERVER['REQUEST_METHOD']);
                        $logType = $successful ? 'info' : 'error';
                        $this->logger->$logType(
                            "SilverStripe \"$type\" optimisation $logType",
                            array(
                                'command'     => $command,
                                'exitCode'    => $process->getExitCode(),
                                'output'      => $process->getOutput(),
                                'errorOutput' => $process->getErrorOutput()
                            )
                        );
                        $_SERVER['REQUEST_METHOD'] = $requestMethod;
                    }

                } catch (\Exception $e) {
                    if (null !== $this->logger) {
                        $this->logger->error(
                            "SilverStripe \"$type\" optimisation exception",
                            array(
                                'exception' => $e
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Returns a text version of IMAGETYPE_* constants
     * @param $type
     * @return bool|string
     */
    protected function getImageType($type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            case IMAGETYPE_GIF:
                return 'gif';
            default:
                return false;
        }
    }
    /**
     * Gets all enabled commands for the file and type of image
     * @param $filename
     * @param $type
     * @return array
     */
    protected function getCommands($filename, $type)
    {
        $commands = $this->config->get('availableCommands');

        if (!$type || !isset($commands[$type])) {
            return array();
        }

        $command = array();

        foreach ((array)$this->config->get('enabledCommands') as $commandType) {
            if (isset($commands[$type][$commandType])) {
                $command[] = sprintf(
                    $commands[$type][$commandType],
                    rtrim($this->config->get('binDirectory'), '/'),
                    escapeshellarg($filename),
                    $this->config->get('optimisingQuality')
                );
            }
        }

        return $command;
    }
    /**
     * Executes the specified command
     * @param $command
     * @return \Symfony\Component\Process\Process
     */
    private function execCommand($command)
    {
        $process = new Process($command);
        $process->run();

        return $process;
    }
}
