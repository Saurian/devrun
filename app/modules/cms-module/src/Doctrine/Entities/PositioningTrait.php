<?php
/**
 *
 * This file is part of the smart-up
 *
 * Copyright (c) 2015
 *
 * @file PositioningTrait.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Doctrine\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PositioningTrait
 *
*@todo not complete yet

 * @package CmsModule\Doctrine\Entities
 */
trait PositioningTrait {




    /**
     * @var ArrayCollection|RulesEntity[] , fetch="EAGER"
     * @ORM\OneToMany(targetEntity="RulesEntity", mappedBy="previous")
     */
    protected $next;


    /**
     * @var RulesEntity , fetch="EAGER"
     * @ORM\ManyToOne(targetEntity="RulesEntity", inversedBy="next")  # ManyToOne is hack for prevent '1062 Duplicate entry update'
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $previous;


    /**
     * @var RulesEntity
     * @ORM\ManyToOne(targetEntity="RulesEntity", inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $parent;


    /**
     * @var ArrayCollection|RulesEntity[]
     * @ORM\OneToMany(targetEntity="RulesEntity", mappedBy="parent", cascade={"persist", "remove", "detach"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $children;


    /**
     * @var integer
     * @ORM\Column(type="smallint", options={"default"=1})
     */
    protected $position = 1;


    /** @ORM\Column(type="string", length=3) */
    protected $positionString = '';



    /**
     * @return ArrayCollection|RulesEntity[]
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return RulesEntity
     */
    public function getPrevious()
    {
        return $this->previous;
    }


    public function setNext(RulesEntity $next = NULL, $recursively = TRUE)
    {
        if ($next === $this) {
            throw new InvalidArgumentException("Next rule is the same as current rule.");
        }

        $this->next = $next;

        if ($recursively && $next) {
            $next->setPrevious($this, FALSE);
        }
    }


    public function setPrevious(RulesEntity $previous = NULL, $recursively = TRUE)
    {
        if ($previous === $this) {
            throw new InvalidArgumentException("Previous rule is the same as current rule.");
        }

        $this->previous = $previous;

        if ($recursively && $previous) {
            $previous->setNext($this, FALSE);
        }
    }


    /**
     * @todo not yet
     */
    public function setUp()
    {
        $oldPrevious = $this->getPrevious();
        $oldNext = $this->getNext()->first();

        dump("oldPrevious", $oldPrevious);
        dump("oldNext", $oldNext);

        /** @var RulesEntity $next */
        $next = $this->getNext()->first();
        $previous = $this->getPrevious();

        if ($next) {
            $next->setPrevious($previous, FALSE);
        }

        if ($previous) {
            $this->setPrevious($previous->getPrevious(), FALSE);
            $previous->setPrevious($this, FALSE);
        }

        if ($previous) {
            $previous->generatePosition();
        }
//        if ($next) {
//            $next->generatePosition();
//        }

        dump("this", $this);
        dump("previous", $this->getPrevious());
        dump("next", $this->getNext()->first());




    }






    /**
     * @return RulesEntity
     */
    public function getParent()
    {
        return $this->parent;
    }



    public function setParent(RulesEntity $parent = NULL, $setPrevious = NULL, RulesEntity $previous = NULL)
    {
        if ($parent == $this->getParent() && !$setPrevious) {
            return $this;
        }

        if (!$parent && !$this->getNext() && !$this->getPrevious() && !$this->getParent() && !$setPrevious) {
            return $this;
        }

        if ($setPrevious && $previous === $this) {
            throw new InvalidArgumentException("Previous rule is the same as current rule.");
        }

        $oldParent = $this->getParent();
        $oldPrevious = $this->getPrevious();
        $oldNext = $this->getNext();

        $this->removeFromPosition();

        if ($parent) {
            $this->parent = $parent;

            if ($setPrevious) {
                if ($previous) {
                    $this->setNext($previous->next);
                    $this->setPrevious($previous);
                } else {
                    $this->setNext($parent->getChildren()->first() ? : NULL);
                }
            } else {
                $this->setPrevious($parent->getChildren()->last() ? : NULL);
            }

            $parent->children[] = $this;
        } else {
            if ($setPrevious) {
                if ($previous) {
                    $this->setNext($previous->next);
                    $this->setPrevious($previous);
                } else {
                    $this->setNext($this->getRoot($oldNext ? : ($oldParent ? : ($oldPrevious))));
                }
            } else {
                $this->parent = NULL;
                $this->previous = NULL;
                $this->next = NULL;
            }
        }

        $this->generatePosition();
        return $this;
    }



    public function getRoot(RulesEntity $entity = NULL)
    {
        $entity = $entity ? : $this;

        while ($entity->getParent()) {
            $entity = $entity->parent;
        }

        while ($entity->getPrevious()) {
            $entity = $entity->previous;
        }

        return $entity;
    }



    /**
     * @param bool $recursively
     */
    public function generatePosition($recursively = TRUE)
    {
        $position = $this->getPrevious() ? $this->getPrevious()->position + 1 : 1;

        $this->position = $position;
        $this->positionString = ($this->parent ? $this->parent->positionString . ';' : '') . str_pad($this->position, 3, '0', STR_PAD_LEFT);



        if ($recursively) {
            /** @var RulesEntity $entity */
            if ($this->getNext() && $entity = $this->getNext()->first()) {
                $entity->generatePosition();
            }

//            foreach ($this->children as $item) {
//                $item->generatePosition();
//            }
        }
    }


    public function removeFromPosition()
    {
        if (!$this->getPrevious() && !$this->getNext()) {
            return;
        }

        $next = $this->getNext();
        $previous = $this->getPrevious();

        if ($next) {
            $next->setPrevious($previous, FALSE);
        }

        if ($previous) {
            $previous->setNext($next, FALSE);
        }

        if ($next) {
            $next->generatePosition();
        }

        $this->setPrevious(NULL);
        $this->parent = NULL;
        $this->setNext(NULL);
    }


} 