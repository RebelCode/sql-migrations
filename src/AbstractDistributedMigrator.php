<?php

namespace RebelCode\Storage\Migration\Sql;

use Traversable;

/**
 * Common functionality for migrator instances that retrieve down migrations from the log table.
 *
 * This implementation is particular useful for applications that are distributed to client servers. Downgrading to an
 * earlier version of the application and its database requires the "down" migrations from the later version, which are
 * not present after the client has installed the earlier version.
 *
 * When migrating upwards, this migrator implementation will store the "down" counterparts of any completed migration
 * queries. For migrating downwards, the migrator will retrieve those queries from the database and invoke them. This
 * also yields the benefit of guaranteeing that the only down migrations that are run are for only the "up" migrations
 * that were originally run, as well as providing a VCS-style of history for how to undo the changes made to the
 * database, incrementally.
 *
 * @since [*next-version*]
 */
abstract class AbstractDistributedMigrator extends AbstractLoggingMigrator
{
    /**
     * Retrieves the migrations for a particular version in a particular direction.
     *
     * @since [*next-version*]
     *
     * @param int $version   An integer version number to migration to.
     * @param int $direction A positive integer for "up" migrations or a negative integer for "down" migrations.
     *
     * @return MigrationInterface[]|Traversable The migration instances.
     */
    protected function getMigrations($version, $direction)
    {
        return ($direction < 0)
            ? $this->getDbMigrations($version)
            : $this->getLocalMigrations($version);
    }

    /**
     * Retrieves the migrations from the database for a particular version.
     *
     * @since [*next-version*]
     *
     * @param int $version An integer version number for which to retrieve the down migrations.
     *
     * @return MigrationInterface[]|Traversable The migration instances.
     */
    protected function getDbMigrations($version)
    {
        $rows = $this->runQuery(
            sprintf(
                'SELECT * FROM `%1$s` WHERE `version` = "%2$s" ORDER BY `priority` DESC;',
                $this->getMigrationLogTable(),
                $version
            )
        );

        $migrations = array_map(function ($row) {
            return new Migration(
                $row['key'],
                $row['priority'],
                $row['up'],
                $row['down']
            );
        }, $rows);

        return $migrations;
    }

    /**
     * Retrieves the local migrations for a particular version.
     *
     * @since [*next-version*]
     *
     * @param int $version An integer version number for which to retrieve the up migrations.
     *
     * @return MigrationInterface[]|Traversable The migration instances.
     */
    abstract protected function getLocalMigrations($version);
}
