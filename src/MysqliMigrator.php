<?php

namespace RebelCode\Storage\Migration\Sql;

use Dhii\I18n\StringTranslatingTrait;
use mysqli;

/**
 * A simple mysqli migrator that uses locally stored migration instances and logs all run migrations.
 *
 * @since [*next-version*]
 */
class MysqliMigrator extends AbstractLoggingMigrator
{
    /* @since [*next-version*] */
    use MysqliRunQueryCapableTrait;

    /* @since [*next-version*] */
    use MigrationLogTableAwareTrait;

    /* @since [*next-version*] */
    use LocalMigrationsAwareTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param mysqli                 $mysqli     The database connection instance.
     * @param string                 $mLogTable  The name of the migrations log table.
     * @param MigrationInterface[][] $migrations A list of migration arrays, with each list is mapped to a version key.
     */
    public function __construct(mysqli $mysqli, $mLogTable, $migrations)
    {
        $this->setMysqli($mysqli);
        $this->setMigrationLogTable($mLogTable);
        $this->setLocalMigrations($migrations);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function getMigrations($version, $direction)
    {
        return $this->getLocalMigrations($version);
    }
}
