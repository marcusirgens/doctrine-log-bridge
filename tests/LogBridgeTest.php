<?php
/**
 * @copyright Marcus Pettersen Irgens 2019
 * @license MIT
 * @author Marcus Pettersen Irgens <marcus.pettersen.irgens@gmail.com>
 */

namespace Marcuspi\DoctrineLogBridge\Test;

use Marcuspi\DoctrineLogBridge\LogBridge;
use PHPUnit\Framework\Constraint\IsType;
use \PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class LogBridgeTest extends TestCase
{
    /**
     * Test the constructor
     */
    public function testCanConstructClass()
    {
        $this->setName("Test that the LogBridge is correctly created");
        $logger = new NullLogger();
        $level = LogLevel::INFO;

        $this->assertInstanceOf(LogBridge::class, new LogBridge($logger, $level));
    }

    /**
     * Test that the LoggerInterface is invoked when the Bridge's log method is used
     *
     * @throws \ReflectionException
     */
    public function testInvokesLogger()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method("log")
            ->with($this->isType(IsType::TYPE_STRING), $this->isType(IsType::TYPE_STRING), $this->arrayHasKey("sql"));

        $bridge = new LogBridge($logger, LogLevel::INFO);
        $bridge->startQuery("SELECT * FROM users", [], []);
    }

    /**
     * Test that the stopQuery method sets an array key in the context named
     * queryTime
     *
     * @throws \ReflectionException
     */
    public function testLogsTime()
    {

        $logger = $this->createMock(LoggerInterface::class);
        $bridge = new LogBridge($logger, LogLevel::INFO);

        $logger->expects($this->atLeast(1))
            ->method("log")
            ->with(
                $this->isType(IsType::TYPE_STRING),
                $this->isType(IsType::TYPE_STRING),
                $this->logicalOr(
                    $this->isType(IsType::TYPE_ARRAY),
                    $this->isType(IsType::TYPE_NULL)
                )
            );

        $bridge->startQuery("SELECT * FROM users", [], []);

        $logger->expects($this->once())
            ->method("log")
            ->with(
                $this->isType(IsType::TYPE_STRING),
                $this->isType(IsType::TYPE_STRING),
                $this->arrayHasKey("queryTime")
            );

        $bridge->stopQuery();
    }

    /**
     * Test that the stopQuery successfully runs and that no queryTime is set
     * if start time is not known
     *
     * @throws \ReflectionException
     */
    public function testLogsWhenNoStartTimeDefined()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $bridge = new LogBridge($logger, LogLevel::INFO);

        $logger->expects($this->once())
            ->method("log")
            ->with(
                $this->isType(IsType::TYPE_STRING),
                $this->isType(IsType::TYPE_STRING),
                $this->logicalNot($this->arrayHasKey("queryTime"))
            );

        $bridge->stopQuery();
    }

    /**
     * Check that the log level is correctly set
     *
     * @throws \ReflectionException
     */
    public function testUsesRightLogLevel()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method("log")
            ->with(
                $this->equalTo(LogLevel::INFO),
                $this->anything(),
                $this->anything()
            );
        $bridge = new LogBridge($logger, LogLevel::INFO);
        $bridge->startQuery("Random log statement");
        $bridge->stopQuery();

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method("log")
            ->with(
                $this->equalTo(LogLevel::ALERT),
                $this->anything(),
                $this->anything()
            );
        $bridge = new LogBridge($logger, LogLevel::ALERT);
        $bridge->startQuery("Alerting log statement");
        $bridge->stopQuery();
    }
}
