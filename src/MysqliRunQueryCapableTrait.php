<?php

namespace RebelCode\Storage\Migration\Sql;

use mysqli;
use mysqli_result;
use RuntimeException;

/**
 * Functionality for migrators that use a mysqli database connection for running queries.
 *
 * @since [*next-version*]
 */
trait MysqliRunQueryCapableTrait
{
    /**
     * The mysqli database connection.
     *
     * @since [*next-version*]
     *
     * @var mysqli
     */
    protected $mysqli;

    /**
     * Runs an SQL query.
     *
     * @since [*next-version*]
     *
     * @param string $query The query.
     *
     * @throws RuntimeException If the query failed.
     *
     * @return mixed The query result.
     */
    protected function runQuery($query)
    {
        $result = $this->mysqli->query($query);

        if (!(bool) $result) {
            throw new RuntimeException($this->mysqli->error, $this->mysqli->errno);
        }

        return ($result instanceof mysqli_result)
            ? $result->fetch_all(MYSQLI_ASSOC)
            : $result;
    }

    /**
     * Sets the mysqli database connection instance.
     *
     * @since [*next-version*]
     *
     * @param mysqli $mysqli The mysqli database connection instance.
     */
    protected function setMysqli(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }
}
