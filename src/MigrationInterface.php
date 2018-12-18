<?php

namespace RebelCode\Storage\Migration\Sql;

/**
 * The interface for objects that represent database migrations.
 *
 * @since [*next-version*]
 */
interface MigrationInterface
{
    /**
     * Retrieves the migration's unique key.
     *
     * @since [*next-version*]
     *
     * @return string A string key that uniquely identifies the migration within the version's migration set.
     */
    public function getKey();

    /**
     * Retrieves this migration's "up" query for updating the database.
     *
     * @since [*next-version*]
     *
     * @return string The query string.
     */
    public function getUpQuery();

    /**
     * Retrieves this migration's "down" query for reverting the "up" query.
     *
     * @since [*next-version*]
     *
     * @return string The query string.
     */
    public function getDownQuery();

    /**
     * Retrieves the migration's priority index.
     *
     * @since [*next-version*]
     *
     * @return int A zero or positive integer, where smaller integers indicate earlier execution.
     */
    public function getPriority();
}
