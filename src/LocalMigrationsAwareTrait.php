<?php

namespace RebelCode\Storage\Migration\Sql;

use InvalidArgumentException;
use Traversable;

/**
 * Functionality for migrators that are aware of a list of locally stored migrations.
 *
 * @since [*next-version*]
 */
trait LocalMigrationsAwareTrait
{
    /**
     * The migrations list.
     *
     * @since [*next-version*]
     *
     * @var MigrationInterface[][]
     */
    protected $migrations;

    /**
     * Retrieves the local migrations for a particular version.
     *
     * @since [*next-version*]
     *
     * @param int $version An integer version number for which to retrieve the up migrations.
     *
     * @return MigrationInterface[]|Traversable The migration instances.
     */
    public function getLocalMigrations($version)
    {
        return isset($this->migrations[$version])
            ? $this->migrations[$version]
            : [];
    }

    /**
     * Sets the local migrations.
     *
     * @since [*next-version*]
     *
     * @param MigrationInterface[][] $migrations A list of migration arrays, with each list is mapped to a version key.
     */
    protected function setLocalMigrations($migrations)
    {
        if (!is_array($migrations)) {
            throw new InvalidArgumentException($this->__('Argument is not an array'));
        }

        $this->migrations = ($migrations instanceof Traversable)
            ? iterator_to_array($migrations)
            : $migrations;
    }

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     * @see   _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
