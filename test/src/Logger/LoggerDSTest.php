<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 19.12.16
 * Time: 11:43 AM
 */
namespace zaboy\test\res\Logger;

use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\Test\LoggerInterfaceTest;
use Xiag\Rql\Parser\Query;
use zaboy\dic\InsideConstruct;
use zaboy\res\Logger\Logger;
use zaboy\res\Logger\LoggerDS;
use zaboy\rest\DataStore\Interfaces\DataStoresInterface;

class LoggerDSTest extends LoggerInterfaceTest
{
    /** @var  LoggerInterface */
    protected $object;

    /** @var  ContainerInterface */
    protected $container;

    public function setUp()
    {
        $this->object = $this->getLogger();
    }

    /**
     * @dataProvider provideLogDateTime
     * @param $dateTime
     * @param $expectedTime
     */
    public function testLogWithTime($dateTime, $expectedTime)
    {
        $this->object->log(
            LogLevel::ERROR,
            $dateTime . "|" . "Error message of level emergency with context: {user}",
            ['user' => 'Bob']
        );
        $expected = [
            $expectedTime . ' ' . LogLevel::ERROR . ' ' .
            'Error message of level emergency with context: Bob'
        ];
        $this->assertEquals($expected, $this->getLogsWithTime());
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return new Logger(fopen(realpath("logs.txt"), "w"));
    }

    public function provideLogDateTime()
    {
        $time = new \DateTime();
        return [
            [$time->format('Y-m-d H:i:s'), $time->getTimestamp()],
            [$time->format('D M j G:i:s T Y'), $time->getTimestamp()],
            [$time->getTimestamp(), $time->getTimestamp()],
        ];
    }

    /**
     * This must return the log messages in order.
     *
     * The simple formatting of the messages is: "<LOG LEVEL> <MESSAGE>".
     *
     * Example ->error('Foo') would yield "error Foo".
     *
     * @return string[]
     */
    public function getLogs()
    {
        $file = fopen(realpath("logs.txt"), "w");

        $logs = [];
        while (($log = fgets($file))) {
            $part = explode(';', $log);
            $logs[] = $part[1] . ' ' . $part[2];
        }
        return $logs;
        /*$data = $this->dataStore->query(new Query());
        foreach ($data as $item) {
            $logs[] = $item['level'] . " " . $item['message'];
        }
        return $logs;*/
    }

    public function getLogsWithTime()
    {
        $file = fopen(realpath("logs.txt"), "w");

        $logs = [];
        while (($log = fgets($file))) {
            $part = explode(';', $log);
            $logs[] = $part[0] . ' ' . $part[1] . ' ' . $part[2];
        }
        return $logs;
    }
}
