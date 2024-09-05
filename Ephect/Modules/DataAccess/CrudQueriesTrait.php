<?php

namespace Ephect\Modules\DataAccess;

trait CrudQueriesTrait
{
    //put your code here

    private $_select = '';
    private $_insert = '';
    private $_update = '';
    private $_delete = '';
    private $_parameters = '';


    /**
     * SELECT query
     *
     * @param string $value SQL query
     * @param mixed array $params Set of values for the parametered query
     */
    public function setSelectQuery(string $value, array $params = []): void
    {
        $this->_parameters = $params;
        $this->_select = $value;
    }

    public function getSelectQuery(): object
    {
        return (object)['sql' => $this->_select, 'params' => $this->_parameters];
    }

    /**
     * INSERT query
     *
     * @param string $value SQL query
     * @param mixed array $params Set of values for the parametered query
     */
    public function setInsertQuery(string $value, array $params = []): void
    {
        $this->_parameters = $params;
        $this->_insert = $value;
    }

    public function getInsertQuery(): object
    {
        return (object)['sql' => $this->_insert, 'params' => $this->_parameters];
    }

    /**
     * UPDATE query
     *
     * @param string $value SQL query
     * @param mixed array $params Set of values for the parametered query
     */
    public function setUpdateQuery(string $value, array $params = []): void
    {
        $this->_parameters = $params;
        $this->_update = $value;
    }

    public function getUpdateQuery(): object
    {
        return (object)['sql' => $this->_update, 'params' => $this->_parameters];
    }

    /**
     * DELETE query
     *
     * @param string $value SQL query
     * @param mixed array $params Set of values for the parametered query
     */
    public function setDeleteQuery(string $value, array $params = []): void
    {
        $this->_parameters = $params;
        $this->_delete = $value;
    }

    public function getDeleteQuery(): object
    {
        return (object)['sql' => $this->_delete, 'params' => $this->_parameters];
    }

}
