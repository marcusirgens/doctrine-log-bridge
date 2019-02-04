<?php
/**
 * @copyright Marcus Pettersen Irgens 2019
 * @license MIT
 * @author Marcus Pettersen Irgens <marcus.pettersen.irgens@gmail.com>
 */

namespace Marcuspi\DoctrineLogBridge;

use Doctrine\DBAL\Logging\SQLLogger;

class LogBridge implements SQLLogger
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $level;

    /**
     * @var float|null
     */
    private $lastStart;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql The SQL to be executed.
     * @param mixed[]|null $params The SQL parameters.
     * @param int[]|string[]|null $types The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->lastStart = microtime(true);
        $this->logger->log(
            $this->level,
            "Executing query {sql}",
            ["sql" => $sql, "params" => $params, "types" => $types]
        );
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        if ($this->lastStart !== null) {
            $time = microtime(true) - $this->lastStart;
            $this->logger->log($this->level, "Query finished after {time} seconds", ["queryTime" => $time]);
        } else {
            $this->logger->log($this->level, "Query finished.");
        }
    }
}
