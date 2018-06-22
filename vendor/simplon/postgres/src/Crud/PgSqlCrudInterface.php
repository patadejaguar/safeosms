<?php

namespace Simplon\Postgres\Crud;

/**
 * Interface SqlCrudInterface
 * @package Simplon\Postgres\Crud
 * @author Tino Ehrich (tino@bigpun.me)
 */
interface PgSqlCrudInterface
{
    /**
     * @return string
     */
    public static function crudGetSource();

    /**
     * @return string|null
     */
    public function crudGetQuery();

    /**
     * @return array
     */
    public function crudColumns();

    /**
     * @return array
     */
    public function crudIgnore();

    /**
     * @return string
     */
    public function crudPkName();

    /**
     * @param bool $isCreateEvent
     *
     * @return bool
     */
    public function crudBeforeSave($isCreateEvent);

    /**
     * @param bool $isCreateEvent
     *
     * @return bool
     */
    public function crudAfterSave($isCreateEvent);
} 