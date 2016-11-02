<?php

namespace Library\Model\Crud;

use Library\Construct\Model\Db\Db;
use Library\Model\MysqlException;

/**
 * Interface CrudStoreInterface
 * @package Library\Model\Crud
 */
interface CrudStoreInterface
{
    /**
     * @param Mysql $mysql
     * @param CrudManager $crudManager
     */
    public function __construct(Db $dbh, CrudManager $crudManager);

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @return CrudModelInterface
     */
    public function getModel();

    /**
     * @param CrudModelInterface $model
     *
     * @return CrudModelInterface
     */
    public function crudCreate(CrudModelInterface $model);

    /**
     * @param array $conds
     * @param array $sorting
     *
     * @return null|CrudModelInterface[]
     */
    public function crudRead(array $conds = [], array $sorting = []);

    /**
     * @param array $conds
     *
     * @return null|CrudModelInterface
     */
    public function crudReadOne(array $conds);

    /**
     * @param CrudModelInterface $model
     * @param array $conds
     *
     * @return CrudModelInterface
     */
    public function crudUpdate(CrudModelInterface $model, array $conds);

    /**
     * @param array $conds
     *
     * @throws MysqlException
     */
    public function crudDelete(array $conds);
}
