<?php
/**
 *
 * This file is part of the smart-up
 *
 * Copyright (c) 2015
 *
 * @file EducationRepository.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Repository;


use CmsModule\Entities\UserEntity;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;

class UserRepository extends AbstractRepository {


    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository(UserEntity::getClassName());
    }


    public function findByUsername($username)
    {
        return $this->repository->createQueryBuilder('e')
            ->where("e.username = :username")
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }










    /**
     * @return UserRepository|\Kdyby\Doctrine\EntityDao
     */
    function getRepository()
    {
        return $this->repository;
    }
}