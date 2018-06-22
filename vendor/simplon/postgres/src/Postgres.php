<?php

namespace Simplon\Postgres;

/**
 * Postgres
 * @package Simplon\Postgres
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Postgres
{
    /**
     * @var Postgres
     */
    private $dbh;

    /**
     * @var int
     */
    protected $fetchMode;

    /**
     * @var \PDOStatement
     */
    protected $lastStatement;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int $port
     * @param int $fetchMode
     * @param string $charset
     * @param array $options
     *
     * @throws PostgresException
     */
    public function __construct($host, $user, $password, $database, $port = 5432, $fetchMode = \PDO::FETCH_ASSOC, $charset = 'utf8', array $options = array())
    {
        try
        {
            // setup dns
            $dns = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';options=--client_encoding=\'' . $charset . '\'';

            // create PDO instance
            $this->setDbh(
                new \PDO($dns, $user, $password)
            );

            // set fetchMode
            $this->setFetchMode($fetchMode);
        }
        catch (\PDOException $e)
        {
            throw new PostgresException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $dbh
     *
     * @return Postgres
     */
    protected function setDbh($dbh)
    {
        $this->dbh = $dbh;

        return $this;
    }

    /**
     * @return \PDO
     */
    protected function getDbh()
    {
        return $this->dbh;
    }

    /**
     * @return Postgres
     */
    public function close()
    {
        $this->dbh = null;

        return $this;
    }

    /**
     * @param int $fetchMode
     *
     * @return Postgres
     */
    protected function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * @return int
     */
    protected function getFetchMode()
    {
        return (int)$this->fetchMode;
    }

    /**
     * @param array $errorInfo
     *
     * @return array
     */
    protected function prepareErrorInfo(array $errorInfo)
    {
        return array(
            'sqlStateCode' => $errorInfo[0],
            'code'         => $errorInfo[1],
            'message'      => $errorInfo[2],
        );
    }

    /**
     * @param \PDOStatement $cursor
     *
     * @return Postgres
     */
    protected function setLastStatement(\PDOStatement $cursor)
    {
        $this->lastStatement = $cursor;

        return $this;
    }

    /**
     * @return \PDOStatement
     */
    protected function getLastStatement()
    {
        return $this->lastStatement;
    }

    /**
     * @return bool
     */
    protected function hasLastStatement()
    {
        return $this->lastStatement ? true : false;
    }

    /**
     * @return Postgres
     */
    protected function clearLastStatement()
    {
        $this->lastStatement = null;

        return $this;
    }

    /**
     * @param $paramValue
     *
     * @return int
     * @throws PostgresException
     */
    protected function getParamType($paramValue)
    {
        switch ($paramValue)
        {
            case is_int($paramValue):
                return \PDO::PARAM_INT;

            case is_bool($paramValue):
                return \PDO::PARAM_INT;

            case is_string($paramValue):
                return \PDO::PARAM_STR;

            case is_float($paramValue):
                return \PDO::PARAM_STR;

            case is_double($paramValue):
                return \PDO::PARAM_STR;

            case is_null($paramValue):
                return \PDO::PARAM_NULL;

            default:
                throw new PostgresException("Invalid param type: {$paramValue} with type {gettype($paramValue)}");
        }
    }

    /**
     * @param \PDOStatement $pdoStatement
     * @param array $params
     *
     * @return \PDOStatement
     */
    protected function setParams(\PDOStatement $pdoStatement, array $params)
    {
        foreach ($params as $key => &$val)
        {
            $pdoStatement->bindParam($key, $val, $this->getParamType($val));
        }

        return $pdoStatement;
    }

    /**
     * @param $query
     * @param $params
     *
     * @return bool
     */
    protected function handleInCondition(&$query, &$params)
    {
        if (empty($params))
        {
            return true;
        }

        // --------------------------------------

        foreach ($params as $key => $val)
        {
            if (is_array($val))
            {
                $keys = array();

                foreach ($val as $k => $v)
                {
                    // new param name
                    $keyString = ':' . $key . $k;

                    // cache new params
                    $keys[] = $keyString;

                    // add new params
                    $params[$keyString] = $v;
                }

                // include new params
                $query = str_replace(':' . $key, join(',', $keys), $query);

                // remove actual param
                unset($params[$key]);
            }
        }

        return true;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return \PDOStatement
     * @throws PostgresException
     */
    protected function prepareSelect($query, array $conds)
    {
        // clear last statement
        $this->clearLastStatement();

        // handle "in" condition
        $this->handleInCondition($query, $conds);

        // set query
        $pdoStatement = $this->getDbh()->prepare($query);

        // bind named params
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // execute
        $pdoStatement->execute();

        // check for errors
        if ($pdoStatement->errorCode() === '00000')
        {
            // cache statement
            $this->setLastStatement($pdoStatement);

            return $pdoStatement;
        }

        // ----------------------------------

        $error = array(
            'query'     => $query,
            'params'    => $conds,
            'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
        );

        $errorInfo = json_encode($error);

        throw new PostgresException($errorInfo);
    }

    /**
     * @param string $query
     * @param array $rowsMany
     *
     * @return array
     * @throws PostgresException
     */
    protected function prepareInsert($query, array $rowsMany)
    {
        $dbh = $this->getDbh();
        $responses = array();

        // clear last statement
        $this->clearLastStatement();

        // set query
        $pdoStatement = $dbh->prepare($query);

        // loop through rows
        while ($row = array_shift($rowsMany))
        {
            // bind params
            $pdoStatement = $this->setParams($pdoStatement, $row);

            // execute
            $pdoStatement->execute();

            // throw errors
            if ($pdoStatement->errorCode() !== '00000')
            {
                $error = array(
                    'query'     => $query,
                    'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
                );

                $errorInfo = json_encode($error);

                throw new PostgresException($errorInfo);
            }

            // last insert|null
            $lastInsert = $pdoStatement->fetch(\PDO::FETCH_NUM);

            // cache response
            $responses[] = $lastInsert !== false ? (int)$lastInsert[0] : true;
        }

        return $responses;
    }

    /**
     * @param $query
     * @param array $conds
     * @param array $data
     *
     * @return bool
     * @throws PostgresException
     */
    protected function prepareUpdate($query, array $conds, array $data)
    {
        // clear last statement
        $this->clearLastStatement();

        // handle "in" condition
        $this->handleInCondition($query, $conds);

        // set query
        $pdoStatement = $this->getDbh()->prepare($query);

        // bind conds params
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // bind data params
        $pdoStatement = $this->setParams($pdoStatement, $data);

        // execute
        $pdoStatement->execute();

        // cache statement
        $this->setLastStatement($pdoStatement);

        // throw errors
        if ($pdoStatement->errorCode() !== '00000')
        {
            $error = array(
                'query'     => $query,
                'conds'     => $conds,
                'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
            );

            $errorInfo = json_encode($error);

            throw new PostgresException($errorInfo);
        }

        if ($this->getRowCount() > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return bool
     * @throws PostgresException
     */
    protected function prepareDelete($query, array $conds)
    {
        // clear last statement
        $this->clearLastStatement();

        // handle "in" condition
        $this->handleInCondition($query, $conds);

        // set query
        $pdoStatement = $this->getDbh()->prepare($query);

        // bind conds params
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // execute
        $pdoStatement->execute();

        // cache statement
        $this->setLastStatement($pdoStatement);

        // throw errors
        if ($pdoStatement->errorCode() !== '00000')
        {
            $error = array(
                'query'     => $query,
                'conds'     => $conds,
                'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
            );

            $errorInfo = json_encode($error);

            throw new PostgresException($errorInfo);
        }

        if ($this->getRowCount() > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * @return bool|int
     */
    public function getRowCount()
    {
        if ($this->hasLastStatement() !== false)
        {
            return $this->getLastStatement()->rowCount();
        }

        return false;
    }

    /**
     * @param $query
     *
     * @return bool
     * @throws PostgresException
     */
    public function executeSql($query)
    {
        $dbh = $this->getDbh();

        $response = $dbh->exec($query);

        if ($response !== false)
        {
            return true;
        }

        $error = array(
            'query'     => $query,
            'errorInfo' => $this->prepareErrorInfo($dbh->errorInfo()),
        );

        $errorInfo = json_encode($error);

        throw new PostgresException($errorInfo);
    }

    /**
     * @param $dbName
     *
     * @return bool
     * @throws PostgresException
     */
    public function selectDb($dbName)
    {
        return $this->executeSql('use ' . $dbName);
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return false|string
     */
    public function fetchColumn($query, array $conds = array())
    {
        $response = $this->prepareSelect($query, $conds)->fetchColumn();

        if ($response !== false)
        {
            return (string)$response;
        }

        return false;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return array|bool
     */
    public function fetchColumnMany($query, array $conds = array())
    {
        $responsesMany = array();
        $pdoStatment = $this->prepareSelect($query, $conds);

        while ($response = $pdoStatment->fetchColumn())
        {
            $responsesMany[] = $response;
        }

        if (!empty($responsesMany))
        {
            return (array)$responsesMany;
        }

        return false;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return PostgresQueryIterator
     */
    public function fetchColumnManyCursor($query, array $conds = array())
    {
        $this->prepareSelect($query, $conds);

        // ----------------------------------

        return new PostgresQueryIterator($this->getLastStatement(), 'fetchColumn');
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return array|bool
     */
    public function fetchRow($query, array $conds = array())
    {
        $response = $this->prepareSelect($query, $conds)->fetch($this->getFetchMode());

        if ($response !== false)
        {
            return (array)$response;
        }

        return false;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return array|bool
     */
    public function fetchRowMany($query, array $conds = array())
    {
        $responsesMany = array();
        $pdoStatment = $this->prepareSelect($query, $conds);

        while ($response = $pdoStatment->fetch($this->getFetchMode()))
        {
            $responsesMany[] = $response;
        }

        if (!empty($responsesMany))
        {
            return (array)$responsesMany;
        }

        return false;
    }

    /**
     * @param $query
     * @param array $conds
     *
     * @return PostgresQueryIterator
     */
    public function fetchRowManyCursor($query, array $conds = array())
    {
        $this->prepareSelect($query, $conds);

        // ----------------------------------

        return new PostgresQueryIterator($this->getLastStatement(), 'fetch');
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param string $pkName
     * @param bool $insertIgnore
     *
     * @return int|bool
     * @throws PostgresException
     */
    public function insert($tableName, array $data, $pkName = null, $insertIgnore = false)
    {
        if (isset($data[0]))
        {
            throw new PostgresException("Multi-dimensional datasets are not allowed. Use 'Postgres::insertMany()' instead");
        }

        $response = $this->insertMany($tableName, array($data), $pkName, $insertIgnore);

        if ($response !== false)
        {
            return array_pop($response);
        }

        return false;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param string $pkName
     * @param bool $insertIgnore
     *
     * @return array|bool
     * @throws PostgresException
     */
    public function insertMany($tableName, array $data, $pkName = null, $insertIgnore = false)
    {
        if (!isset($data[0]))
        {
            throw new PostgresException("One-dimensional datasets are not allowed. Use 'Postgres::insert()' instead");
        }

        $query = 'INSERT' . ($insertIgnore === true ? ' IGNORE ' : null) . ' INTO ' . $tableName . ' (:COLUMN_NAMES) VALUES (:PARAM_NAMES)';

        // enable returning insert id
        if ($pkName !== null)
        {
            $query .= ' RETURNING ' . $pkName;
        }

        $placeholder = array(
            'column_names' => array(),
            'param_names'  => array(),
        );

        foreach ($data[0] as $columnName => $value)
        {
            $placeholder['column_names'][] = $columnName;
            $placeholder['param_names'][] = ':' . $columnName;
        }

        $query = str_replace(':COLUMN_NAMES', join(', ', $placeholder['column_names']), $query);
        $query = str_replace(':PARAM_NAMES', join(', ', $placeholder['param_names']), $query);

        // ----------------------------------

        $response = $this->prepareInsert($query, $data);

        if (!empty($response))
        {
            return (array)$response;
        }

        return false;
    }

    /**
     * @param $tableName
     * @param array $conds
     * @param array $data
     * @param null $condsQuery
     *
     * @return bool
     * @throws PostgresException
     */
    public function update($tableName, array $conds, array $data, $condsQuery = null)
    {
        if (isset($data[0]))
        {
            throw new PostgresException("Multi-dimensional datasets are not allowed.");
        }

        $query = 'UPDATE ' . $tableName . ' SET :PARAMS WHERE :CONDS';

        $placeholder = array(
            'params' => array(),
            'conds'  => array(),
        );

        foreach ($data as $columnName => $value)
        {
            $placeholder['params'][] = $columnName . ' = :' . $columnName;
        }

        $query = str_replace(':PARAMS', join(', ', $placeholder['params']), $query);

        // ----------------------------------

        if (!empty($conds))
        {
            if ($condsQuery === null)
            {
                $placeholder = array();

                foreach ($conds as $columnName => $value)
                {
                    $placeholder[] = $columnName . '= :' . $columnName;
                }

                $query = str_replace(':CONDS', join(' AND ', $placeholder), $query);
            }
            else
            {
                $query = str_replace(':CONDS', $condsQuery, $query);
            }
        }
        else
        {
            $query = str_replace(' WHERE :CONDS', '', $query);
        }

        // ----------------------------------

        $response = $this->prepareUpdate($query, $conds, $data);

        if ($response === true)
        {
            return true;
        }

        return false;
    }

    /**
     * @param $tableName
     * @param array $conds
     * @param null $condsQuery
     *
     * @return bool
     */
    public function delete($tableName, array $conds = array(), $condsQuery = null)
    {
        $query = 'DELETE FROM ' . $tableName . ' WHERE :CONDS';

        if (!empty($conds))
        {
            if ($condsQuery === null)
            {
                $placeholder = array();

                foreach ($conds as $columnName => $value)
                {
                    $placeholder[] = $columnName . '= :' . $columnName;
                }

                $query = str_replace(':CONDS', join(' AND ', $placeholder), $query);
            }
            else
            {
                $query = str_replace(':CONDS', $condsQuery, $query);
            }
        }
        else
        {
            $query = str_replace(' WHERE :CONDS', '', $query);
        }

        // ----------------------------------

        $response = $this->prepareDelete($query, $conds);

        if ($response === true)
        {
            return true;
        }

        return false;
    }
}