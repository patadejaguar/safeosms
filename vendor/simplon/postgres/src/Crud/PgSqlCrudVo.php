<?php

namespace Simplon\Postgres\Crud;

/**
 * PgSqlCrudVo
 * @package Simplon\Postgres\Crud
 * @author Tino Ehrich (tino@bigpun.me)
 */
abstract class PgSqlCrudVo implements PgSqlCrudInterface
{
    /**
     * @var string
     */
    private static $crudSource;

    /**
     * @var string
     */
    private $crudQuery;

    /**
     * @param string $query
     */
    public function __construct($query = null)
    {
        $this->crudQuery = $query;
    }

    /**
     * @return string
     */
    public static function crudGetSource()
    {
        if (self::$crudSource === null)
        {
            // remove "CrudVo"
            $class = str_replace('CrudVo', '', get_called_class());

            // transform from CamelCase and pluralise
            self::$crudSource = substr(strtolower(preg_replace('/([A-Z])/', '_\\1', $class)) . 's', 1);
        }

        return self::$crudSource;
    }

    /**
     * @return string|null
     */
    public function crudGetQuery()
    {
        return $this->crudQuery;
    }

    /**
     * @return array
     */
    public function crudIgnore()
    {
        return array();
    }

    /**
     * @return array
     */
    public function crudColumns()
    {
        return $this->crudParseVariables();
    }

    /**
     * @return string
     */
    public function crudPkName()
    {
        return 'id';
    }

    /**
     * @param bool $isCreateEvent
     *
     * @return bool
     */
    public function crudBeforeSave($isCreateEvent)
    {
        return true;
    }

    /**
     * @param bool $isCreateEvent
     *
     * @return bool
     */
    public function crudAfterSave($isCreateEvent)
    {
        return true;
    }

    /**
     * @return array
     */
    protected function crudParseVariables()
    {
        $variables = get_class_vars(get_called_class());
        $ignore = $this->crudIgnore();
        $columns = array();

        // remove this class's variables
        unset($variables['crudSource']);
        unset($variables['crudQuery']);

        // render column names
        foreach ($variables as $name => $value)
        {
            if (in_array($name, $ignore) === false)
            {
                $columns[$name] = strtolower(preg_replace('/([A-Z])/', '_\\1', $name));
            }
        }

        return $columns;
    }
}