<?php

namespace RebelCode\Storage\Migration\Sql;

use Exception;
use RuntimeException;
use Traversable;

/**
 * Common functionality for migrator implementations.
 *
 * @since [*next-version*]
 */
abstract class AbstractMigrator implements MigratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function migrate($targetVer)
    {
        $versions = $this->getMigrationInfo($targetVer, $direction);

        foreach ($versions as $version) {
            $uMigrations = $this->getMigrations($version, $direction);
            $sMigrations = $this->sortMigrations($uMigrations, $direction);

            try {
                $this->runMigrations($version, $direction, $sMigrations);
            } catch (RuntimeException $exception) {
                $this->onMigrationsError($version, $direction, $sMigrations, $exception);
            }
        }
    }

    /**
     * Retrieves the versions that need to be processed to migrate to a particular target version.
     *
     * @since [*next-version*]
     *
     * @param int      $targetVer   The target version.
     * @param int|null $direction   This will be set to a positive integer for "up" migrations, a negative integer for
     *                              "down" migrations or zero if no migration is required.
     *
     * @return int[]|string[] An array of versions that need to be processed.
     */
    protected function getMigrationInfo($targetVer, &$direction = null)
    {
        $targetVer = max(0, (int) $targetVer);
        $currVer = max(0, (int) $this->getCurrentVersion());
        $direction = $targetVer - $currVer;

        if ($direction === 0) {
            return [];
        }

        return ($direction > 0)
            ? range($currVer + 1, $targetVer)  // up
            : range($currVer, $targetVer + 1); // down
    }

    /**
     * Runs a list of migrations, rolling back any run migrations if at least one fails.
     *
     * @since [*next-version*]
     *
     * @param int                              $version    An integer version number to migration to.
     * @param int                              $direction  A positive integer for "up" migrations or a negative integer
     *                                                     for "down" migrations.
     * @param MigrationInterface[]|Traversable $migrations The list of migration instances to run.
     *
     * @throws RuntimeException If an error occurred while migrating the database.
     */
    protected function runMigrations($version, $direction, $migrations)
    {
        // Queue of migrations that have been run
        $runMigrations = [];
        // Will hold the failed migration and the corresponding exception, if any
        $mException = null;
        $fMigration = null;

        foreach ($migrations as $migration) {
            try {
                $this->doMigration($migration, $version, $direction);
                $runMigrations[] = $migration;
            } catch (Exception $mException) {
                $fMigration = $migration;
                break;
            }
        }

        // If no exception was thrown, simply return
        if ($mException === null) {
            return;
        }

        // If an exception was thrown, iterate through the queue and run each migration in the reverse direction
        foreach ($runMigrations as $rMigration) {
            try {
                $this->doMigration($rMigration, $version, $direction * -1);
            } catch (Exception $rException) {
                // Hopefully this never happens
                throw new RuntimeException(
                    sprintf(
                        'The "%1$s" %2$s migration failed and rollback was unsuccessful; consider resetting your db',
                        $fMigration->getKey(),
                        $direction > 0 ? 'up' : 'down'
                    ),
                    null,
                    $rException
                );
            }
        }

        // Wrap the previously thrown exception in a runtime exception and throw
        throw new RuntimeException(
            sprintf(
                'The "%1$s" %2$s migration failed. Rollback was successful',
                $fMigration->getKey(),
                $direction > 0 ? 'up' : 'down'
            ),
            0,
            $mException
        );
    }

    /**
     * Sorts the migrations by their priority.
     *
     * @since [*next-version*]
     *
     * @param MigrationInterface[]|Traversable $migrations The list of migrations to sort.
     * @param int                              $direction  A positive integer for "up" migrations or a negative integer
     *                                                     for "down" migrations.
     *
     * @return MigrationInterface[] The sorted migration instance.
     */
    protected function sortMigrations($migrations, $direction = 1)
    {
        // Reduce direction to 1 (up) or -1 (down)
        $mult = (int) ($direction / abs($direction));
        // If an iterator, change to array to be able to use usort()
        $array = is_array($migrations) ? $migrations : iterator_to_array($migrations);

        usort($array, function (MigrationInterface $a, MigrationInterface $b) use ($mult) {
            // The multiplier is used to reverse the sorting if the direction is down
            return ($a->getPriority() - $b->getPriority()) * $mult;
        });

        return $array;
    }

    /**
     * Runs a migration in a particular direction.
     *
     * @since [*next-version*]
     *
     * @param MigrationInterface $migration The migration instance.
     * @param int                $version   An integer version number to migration to.
     * @param int                $direction A positive integer for "up" migrations or a negative integer for "down"
     *                                      migrations.
     *
     * @throws RuntimeException If the query failed.
     */
    protected function doMigration(MigrationInterface $migration, $version, $direction)
    {
        $query = ($direction > 0) ? $migration->getUpQuery() : $migration->getDownQuery();

        $this->runQuery($query);
    }

    /**
     * Invoked when migration fails due to some error.
     *
     * @since [*next-version*]
     *
     * @param int                              $version    An integer version number.
     * @param int                              $direction  A positive integer for "up" migrations or a negative integer
     *                                                     for "down" migrations.
     * @param MigrationInterface[]|Traversable $migrations The list of migration instances that were run.
     * @param RuntimeException                 $exception  The exception that was thrown.
     */
    protected function onMigrationsError($version, $direction, $migrations, RuntimeException $exception)
    {
        throw $exception;
    }

    /**
     * Retrieves the database's current version.
     *
     * @since [*next-version*]
     *
     * @return int An integer version number to migration to.
     */
    abstract protected function getCurrentVersion();

    /**
     * Retrieves the migrations for a particular version in a particular direction.
     *
     * @since [*next-version*]
     *
     * @param int $version   An integer version number.
     * @param int $direction A positive integer for "up" migrations or a negative integer for "down" migrations.
     *
     * @return MigrationInterface[]|Traversable The migration instances.
     */
    abstract protected function getMigrations($version, $direction);

    /**
     * Runs an SQL query.
     *
     * @since [*next-version*]
     *
     * @param string $query The query.
     *
     * @throws RuntimeException If the query failed.
     */
    abstract protected function runQuery($query);
}
