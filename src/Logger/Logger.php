<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09.01.17
 * Time: 18:32
 */

namespace zaboy\res\Logger;

use InvalidArgumentException;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected $file;

    protected $delimiter;
    protected $endString;

    protected $levelEnum = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug'
    ];

    public function __construct($file = STDOUT, $delimiter = ';', $endString = "\n")
    {
        $this->file = $file;
        $this->delimiter = $delimiter;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $replace = [];
        if (!in_array($level, $this->levelEnum)) {
            throw new InvalidArgumentException("Invalid Level");
        }
        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = $value;
            }
        }

        $split = preg_split('/\|/', strtr($message, $replace), 2, PREG_SPLIT_NO_EMPTY);
        if (count($split) == 2) {
            $id = is_numeric($split[0]) ? $split[0] : (new \DateTime($split[0]))->getTimestamp();
            $message = $split[1];
        } else {
            $id = microtime(true) - date('Z');
            $message = $split[0];
        }
        $id = base64_encode(uniqid("", true) .'_'. $id);

        $string = $id . $this->delimiter . $level . $this->delimiter . $message . $this->endString;
        fwrite($this->file, $string, strlen($string));
    }

    /**
     * Close file descriptor
     */
    public function __destruct()
    {
        fclose($this->file);
    }

}
