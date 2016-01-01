<?php
/**
 *
 * This file is part of the CMS
 *
 * Copyright (c) 2015
 *
 * @file BaseRepository.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Repository;

use Kdyby\Doctrine\EntityManager;
use Nette\Object;

abstract class AbstractRepository extends Object {

    /** @var EntityManager */
    public $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    protected $repository;

    abstract function getRepository();




} 