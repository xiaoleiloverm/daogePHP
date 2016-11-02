<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心MODEL curd模型接口
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Model\Crud;

/**
 * Interface CrudModelInterface
 * @package Library\Model\Crud
 */
interface CrudModelInterface
{
    /**
     * @return CrudModelInterface
     */
    public function beforeSave();

    /**
     * @return CrudModelInterface
     */
    public function beforeUpdate();

    /**
     * @param array $data
     *
     * @return CrudModelInterface
     */
    public function fromArray(array $data);

    /**
     * @return array
     */
    public function toArray();
}
