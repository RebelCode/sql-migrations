<?php

namespace RebelCode\Storage\Migration\Sql;

use RuntimeException;

/**
 * The interface for an object that can update or revert a database to a specific version.
 *
 * @since [*next-version*]
 */
interface MigratorInterface
{
    /**
     * Migrates the database to a specific target version.
     *
     * @since [*next-version*]
     *
     * @param int $version An integer version number to migration to.
     *
     * @throws RuntimeException If an error occurred during migration.
     */
    public function migrate($version);
}
