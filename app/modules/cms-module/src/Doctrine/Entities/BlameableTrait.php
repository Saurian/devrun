<?php
/**
 * This file is part of the smart-up
 * Copyright (c) 2015
 *
 * @file    BlameableTrait.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Doctrine\Entities;


use CmsModule\Entities\UserEntity;

trait BlameableTrait
{

    /**
     * @var UserEntity
     *
     * Will be mapped to either string or user entity
     * by BlameableSubscriber
     *
     * @ORM\ManyToOne(targetEntity="UserEntity")
     */
    protected $createdBy;

    /**
     * @var UserEntity
     *
     * Will be mapped to either string or user entity
     * by BlameableSubscriber
     *
     * @ORM\ManyToOne(targetEntity="UserEntity")
     */
    protected $updatedBy;

    /**
     * @var UserEntity
     *
     * Will be mapped to either string or user entity
     * by BlameableSubscriber
     *
     * @ORM\ManyToOne(targetEntity="UserEntity")
     */
    protected $deletedBy;





    /**
     * @return UserEntity
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return UserEntity
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @return UserEntity
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param UserEntity $createdBy
     *
     * @return $this
     */
    public function setCreatedBy(UserEntity $createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @param UserEntity $deletedBy
     *
     * @return $this
     */
    public function setDeletedBy(UserEntity $deletedBy)
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

    /**
     * @param UserEntity $updatedBy
     *
     * @return $this
     */
    public function setUpdatedBy(UserEntity $updatedBy)
    {
        if ($updatedBy) {
            if (($meUpdatedById = $this->updatedBy ? $this->updatedBy->getId() : null) != $updatedBy->getId()) {
                $this->updatedBy = $updatedBy;
            }
        }

        return $this;
    }


}