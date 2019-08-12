<?php

namespace Simplon\Postgres\Crud;

use Simplon\Postgres\Postgres;
use Simplon\Postgres\PostgresException;

/**
 * PgSqlCrudManager
 * @package Simplon\Postgres\Crud
 * @author Tino Ehrich (tino@bigpun.me)
 */
class PgSqlCrudManager
{
    /**
     * @var Postgres
     */
    private $dbInstance;

    /**
     * @param Postgres $postgres
     */
    public function __construct(Postgres $postgres)
    {
        $this->dbInstance = $postgres;
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     * @param bool $insertIgnore
     *
     * @return bool|PgSqlCrudInterface
     * @throws PostgresException
     */
    public function create(PgSqlCrudInterface $sqlCrudInterface, $insertIgnore = false)
    {
        // do something before we save
        $sqlCrudInterface->crudBeforeSave(true);

        // save to db
        $insertId = $this
            ->getDbInstance()
            ->insert(
                $sqlCrudInterface->crudGetSource(),
                $this->getData($sqlCrudInterface),
                $sqlCrudInterface->crudPkName(),
                $insertIgnore
            );

        if ($insertId !== false)
        {
            // set id
            if (is_bool($insertId) !== true && method_exists($sqlCrudInterface, 'setId'))
            {
                $sqlCrudInterface->setId($insertId);
            }

            // do something after we saved
            $sqlCrudInterface->crudAfterSave(true);

            return $sqlCrudInterface;
        }

        return false;
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     * @param array $conds
     * @param null $sortBy
     * @param null $condsQuery
     *
     * @return bool|PgSqlCrudInterface
     */
    public function read(PgSqlCrudInterface $sqlCrudInterface, array $conds, $sortBy = null, $condsQuery = null)
    {
        // handle custom query
        $query = $sqlCrudInterface->crudGetQuery();

        // fallback to standard query
        if ($query === null)
        {
            $query = "SELECT * FROM {$sqlCrudInterface::crudGetSource()} WHERE {$this->getCondsQuery($conds, $condsQuery)}";
        }

        // add sorting
        if ($sortBy !== null)
        {
            $query .= " ORDER BY {$sortBy}";
        }

        // fetch data
        $data = $this->getDbInstance()->fetchRow($query, $conds);

        if ($data !== false)
        {
            return $this->setData($sqlCrudInterface, $data);
        }

        return false;
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     * @param array $conds
     * @param null $sortBy
     * @param null $condsQuery
     *
     * @return bool|PgSqlCrudInterface[]
     */
    public function readMany(PgSqlCrudInterface $sqlCrudInterface, array $conds = array(), $sortBy = null, $condsQuery = null)
    {
        // handle custom query
        $query = $sqlCrudInterface->crudGetQuery();

        // fallback to standard query
        if ($query === null)
        {
            $query = "SELECT * FROM {$sqlCrudInterface::crudGetSource()}";
        }

        // add conds
        if (empty($conds) === false)
        {
            $query .= " WHERE {$this->getCondsQuery($conds, $condsQuery)}";
        }

        // add sorting
        if ($sortBy !== null)
        {
            $query .= " ORDER BY {$sortBy}";
        }

        // fetch data
        $cursor = $this->getDbInstance()->fetchRowManyCursor($query, $conds);

        // build result
        $sqlCrudInterfaceMany = array();

        if ($cursor !== false)
        {
            foreach ($cursor as $data)
            {
                $sqlCrudInterfaceMany[] = $this->setData(clone $sqlCrudInterface, $data);
            }

            return empty($sqlCrudInterfaceMany) ? false : $sqlCrudInterfaceMany;
        }

        return false;
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     * @param array $conds
     * @param null $condsQuery
     *
     * @return bool|PgSqlCrudInterface
     * @throws PostgresException
     */
    public function update(PgSqlCrudInterface $sqlCrudInterface, array $conds, $condsQuery = null)
    {
        // do something before we save
        $sqlCrudInterface->crudBeforeSave(false);

        $response = $this
            ->getDbInstance()
            ->update(
                $sqlCrudInterface->crudGetSource(),
                $conds,
                $this->getData($sqlCrudInterface),
                $this->getCondsQuery($conds, $condsQuery)
            );

        if ($response !== false)
        {
            // do something after update
            $sqlCrudInterface->crudAfterSave(false);

            return $sqlCrudInterface;
        }

        return false;
    }

    /**
     * @param $crudSource
     * @param array $conds
     * @param null $condsQuery
     *
     * @return bool
     */
    public function delete($crudSource, array $conds, $condsQuery = null)
    {
        return $this
            ->getDbInstance()
            ->delete(
                $crudSource,
                $conds,
                $this->getCondsQuery($conds, $condsQuery)
            );
    }

    /**
     * @return Postgres
     */
    private function getDbInstance()
    {
        return $this->dbInstance;
    }

    /**
     * @param array $conds
     * @param null $condsQuery
     *
     * @return string
     */
    protected function getCondsQuery(array $conds, $condsQuery = null)
    {
        if ($condsQuery !== null)
        {
            return (string)$condsQuery;
        }

        $condsString = array();

        foreach ($conds as $key => $val)
        {
            $query = $key . ' = :' . $key;

            if (is_array($val) === true)
            {
                $query = $key . ' IN (:' . $key . ')';
            }

            $condsString[] = $query;
        }

        return join(' AND ', $condsString);
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     *
     * @return array
     */
    protected function getData(PgSqlCrudInterface &$sqlCrudInterface)
    {
        $data = array();

        foreach ($sqlCrudInterface->crudColumns() as $variable => $column)
        {
            $methodName = 'get' . ucfirst($variable);
            $columnValue = $sqlCrudInterface->$methodName();

            $includeValue =
                $columnValue !== null
                || ($columnValue === null && $variable !== $sqlCrudInterface->crudPkName()); // include NULL only if NOT primary key field

            if ($includeValue === true)
            {
                $data[$column] = $columnValue;
            }
        }

        return $data;
    }

    /**
     * @param PgSqlCrudInterface $sqlCrudInterface
     * @param array $data
     *
     * @return PgSqlCrudInterface
     */
    protected function setData(PgSqlCrudInterface $sqlCrudInterface, array $data)
    {
        $columns = array_flip($sqlCrudInterface->crudColumns());

        foreach ($data as $column => $value)
        {
            if (isset($columns[$column]))
            {
                $methodName = 'set' . ucfirst($columns[$column]);
                $sqlCrudInterface->$methodName($value);
            }
        }

        return $sqlCrudInterface;
    }
}