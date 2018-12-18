<?php

namespace RebelCode\Storage\Migration\Sql;

/**
 * Common functionality for migrators that keep a log of the database's current state in a table.
 *
 * This implementation logs any run up migrations into a table. The database's current version is determined using
 * this log table as the largest recorded migration version number. The migrator will also remove any recorded
 * migrations from the table when the database is migrated downwards.
 *
 * @since [*next-version*]
 */
abstract class AbstractLoggingMigrator extends AbstractMigrator
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function doMigration(MigrationInterface $migration, $version, $direction)
    {
        parent::doMigration($migration, $version, $direction);

        $this->prepareLogTable();

        if ($direction < 0) {
            $this->delogMigration($migration, $version);

            return;
        }

        $this->logMigration($migration, $version);
    }

    /**
     * Logs a migration into the migrations log table.
     *
     * @since [*next-version*]
     *
     * @param MigrationInterface $migration The migration instance.
     * @param int                $version   An integer version number to migration to.
     */
    protected function logMigration(MigrationInterface $migration, $version)
    {
        $values = [
            $version,
            $migration->getPriority(),
            $migration->getKey(),
            $migration->getUpQuery(),
            $migration->getDownQuery(),
        ];
        $valuesQuoted = array_map(function ($value) {
            return sprintf('"%s"', $value);
        }, $values);
        $valuesList = implode(', ', $valuesQuoted);

        $this->runQuery(
            sprintf(
            /* @lang sql */
                'INSERT INTO `%1$s` (`version`, `priority`, `key`, `up`, `down`) VALUES (%2$s)',
                $this->getMigrationLogTable(),
                $valuesList
            )
        );
    }

    /**
     * De-logs a migration from the migrations log table.
     *
     * @since [*next-version*]
     *
     * @param MigrationInterface $migration The migration instance.
     * @param int                $version   An integer version number to migration to.
     */
    protected function delogMigration(MigrationInterface $migration, $version)
    {
        $this->runQuery(
            sprintf(
                'DELETE FROM `%1$s` WHERE `key` = "%2$s" AND `version` = "%3$s" LIMIT 1;',
                $this->getMigrationLogTable(),
                $migration->getKey(),
                $version
            )
        );
    }

    /**
     * Ensures that the migrations log table exists.
     *
     * @since [*next-version*]
     */
    protected function prepareLogTable()
    {
        $this->runQuery(
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%s` (
                    `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `version` INT NOT NULL,
                    `priority` INT NOT NULL,
                    `key` varchar(80) NOT NULL,
                    `up` LONGTEXT NOT NULL,
                    `down` LONGTEXT NOT NULL
                )',
                $this->getMigrationLogTable()
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function getCurrentVersion()
    {
        $this->prepareLogTable();

        $result = $this->runQuery(
            sprintf('SELECT MAX(`version`) AS `version` FROM `%s`;', $this->getMigrationLogTable())
        );

        return isset($result[0]['version'])
            ? $result[0]['version']
            : 0;
    }

    /**
     * Retrieves the name of the migration log table.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    abstract protected function getMigrationLogTable();
}
