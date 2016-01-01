<?php
/**
 * This file is part of the smart-up
 * Copyright (c) 2015
 *
 * @file    DateTimeTrait.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Doctrine\Entities;


use Nette\Utils\DateTime;

trait DateTimeTrait
{


    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $inserted;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @return DateTime
     */
    public function getInserted()
    {
        return $this->inserted;
    }

    /**
     * @param DateTime $inserted
     */
    public function setInserted($inserted)
    {
        $this->inserted = $inserted;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }


}