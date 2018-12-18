<?php

namespace RebelCode\Storage\Migration\Sql;

use InvalidArgumentException;

/**
 * Functionality for migrators that are aware of a migrations log table name.
 *
 * @since [*next-version*]
 */
trait MigrationLogTableAwareTrait
{
    /**
     * The migrations log table name.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $migrationLogTable;

    /**
     * Retrieves the migrations log table name.
     *
     * @since [*next-version*]
     *
     * @return string The migrations log table name.
     */
    public function getMigrationLogTable()
    {
        return $this->migrationLogTable;
    }

    /**
     * Sets the migrations log table name.
     *
     * @since [*next-version*]
     *
     * @param string $mLogTable The migrations log table name.
     */
    protected function setMigrationLogTable($mLogTable)
    {
        if (!is_string($mLogTable) && !(is_object($mLogTable) && method_exists($mLogTable, '__toString'))) {
            throw new InvalidArgumentException($this->__('Argument is not a string'));
        }

        $this->migrationLogTable = $mLogTable;
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
