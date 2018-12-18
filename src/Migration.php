<?php

namespace RebelCode\Storage\Migration\Sql;

/**
 * Default implementation of a migration object.
 *
 * @since [*next-version*]
 */
class Migration implements MigrationInterface
{
    /**
     * The migration key, to uniquely identify it.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $key;

    /**
     * The migration's priority index.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $priority;

    /**
     * The migration's "up" SQL query.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $upSql;

    /**
     * The migration's "down SQL query.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $downSql;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $key      The migration key, to uniquely identify it.
     * @param int    $priority The migration's priority index, where larger numbers indicate later execution.
     * @param string $upSql    The migration's "up" SQL query.
     * @param string $downSql  The migration's "down" SQL query.
     */
    public function __construct($key, $priority, $upSql, $downSql)
    {
        $this->key      = $key;
        $this->priority = $priority;
        $this->upSql    = $upSql;
        $this->downSql  = $downSql;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getUpQuery()
    {
        return $this->upSql;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getDownQuery()
    {
        return $this->downSql;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
