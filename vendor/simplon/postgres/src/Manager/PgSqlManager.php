<?php

namespace Simplon\Postgres\Manager;

use Simplon\Postgres\Postgres;
use Simplon\Postgres\PostgresQueryIterator;

class PgSqlManager
{
    /** @var Postgres */
    protected $dbInstance;

    /**
     * @param Postgres $mysqlInstance
     */
    public function __construct(Postgres $mysqlInstance)
    {
        $this->dbInstance = $mysqlInstance;
    }

    /**
     * @return Postgres
     */
    protected function getDbInstance()
    {
        return $this->dbInstance;
    }

    /**
     * @return bool|int
     */
    public function getRowCount()
    {
        return $this
            ->getDbInstance()
            ->getRowCount();
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return bool
     */
    public function executeSql(PgSqlQueryBuilder $sqlBuilder)
    {
        return $this
            ->getDbInstance()
            ->executeSql($sqlBuilder->getQuery());
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return bool|string
     */
    public function fetchColumn(PgSqlQueryBuilder $sqlBuilder)
    {
        $result = $this
            ->getDbInstance()
            ->fetchColumn($sqlBuilder->getQuery(), $sqlBuilder->getConditions());

        if ($result !== null)
        {
            return (string)$result;
        }

        return false;
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return array|bool
     */
    public function fetchColumnMany(PgSqlQueryBuilder $sqlBuilder)
    {
        $result = $this
            ->getDbInstance()
            ->fetchColumnMany($sqlBuilder->getQuery(), $sqlBuilder->getConditions());

        if ($result !== null)
        {
            return (array)$result;
        }

        return false;
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return PostgresQueryIterator
     */
    public function fetchColumnManyCursor(PgSqlQueryBuilder $sqlBuilder)
    {
        return $this
            ->getDbInstance()
            ->fetchColumnManyCursor($sqlBuilder->getQuery(), $sqlBuilder->getConditions());
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return array|bool
     */
    public function fetchRow(PgSqlQueryBuilder $sqlBuilder)
    {
        $result = $this
            ->getDbInstance()
            ->fetchRow($sqlBuilder->getQuery(), $sqlBuilder->getConditions());

        if ($result !== null)
        {
            return (array)$result;
        }

        return false;
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return array|bool
     */
    public function fetchRowMany(PgSqlQueryBuilder $sqlBuilder)
    {
        $result = $this
            ->getDbInstance()
            ->fetchRowMany($sqlBuilder->getQuery(), $sqlBuilder->getConditions());

        if ($result !== null)
        {
            return (array)$result;
        }

        return false;
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return PostgresQueryIterator
     */
    public function fetchRowManyCursor(PgSqlQueryBuilder $sqlBuilder)
    {
        return $this
            ->getDbInstance()
            ->fetchRowManyCursor($sqlBuilder->getQuery(), $sqlBuilder->getConditions());
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return array|null
     */
    public function insert(PgSqlQueryBuilder $sqlBuilder)
    {
        if ($sqlBuilder->hasMultiData())
        {
            return $this->getDbInstance()
                ->insertMany(
                    $sqlBuilder->getTableName(),
                    $sqlBuilder->getData(),
                    $sqlBuilder->hasInsertIgnore()
                );
        }

        return $this->getDbInstance()
            ->insert(
                $sqlBuilder->getTableName(),
                $sqlBuilder->getData(),
                $sqlBuilder->hasInsertIgnore()
            );
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return array|null
     */
    public function replace(PgSqlQueryBuilder $sqlBuilder)
    {
        if ($sqlBuilder->hasMultiData())
        {
            return $this->getDbInstance()
                ->replaceMany(
                    $sqlBuilder->getTableName(),
                    $sqlBuilder->getData()
                );
        }

        return $this->getDbInstance()
            ->replace(
                $sqlBuilder->getTableName(),
                $sqlBuilder->getData()
            );
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return bool
     */
    public function update(PgSqlQueryBuilder $sqlBuilder)
    {
        return $this->getDbInstance()
            ->update(
                $sqlBuilder->getTableName(),
                $sqlBuilder->getConditions(),
                $sqlBuilder->getData(),
                $sqlBuilder->getConditionsQuery()
            );
    }

    /**
     * @param PgSqlQueryBuilder $sqlBuilder
     *
     * @return bool
     */
    public function delete(PgSqlQueryBuilder $sqlBuilder)
    {
        return $this->getDbInstance()
            ->delete(
                $sqlBuilder->getTableName(),
                $sqlBuilder->getConditions(),
                $sqlBuilder->getConditionsQuery()
            );
    }
}
