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
    protected $up;

    /**
     * The migration's "down SQL query.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $down;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $key      The migration key, to uniquely identify it.
     * @param int    $priority The migration's priority index, where larger numbers indicate later execution.
     * @param string $up       The migration's "up" SQL query.
     * @param string $down     The migration's "down" SQL query.
     */
    public function __construct($key, $priority, $up, $down)
    {
        $this->key = $key;
        $this->priority = $priority;
        $this->up = $up;
        $this->down = $down;
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
        return $this->up;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getDownQuery()
    {
        return $this->down;
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
