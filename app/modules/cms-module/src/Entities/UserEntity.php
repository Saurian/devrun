<?php
/**
 * This file is part of the CMS
 * Copyright (c) 2015
 *
 * @file    UserEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * Class UserEntity
 *
 * @ORM\Entity
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="user")
 * @ORM\Table(name="users",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="username_mail_idx", columns={"username", "mail"}),
 *      @ORM\UniqueConstraint(name="username_idx", columns={"username"})
 * },
 *  indexes={
 *      @ORM\Index(name="newPassword_idx", columns={"new_password"}),
 *      @ORM\Index(name="first_last_name_idx", columns={"first_name", "last_name"}),
 *      @ORM\Index(name="active_idx", columns={"active"}),
 *      @ORM\Index(name="role_idx", columns={"role"}),
 *      @ORM\Index(name="nickname_idx", columns={"nickname"}),
 *      @ORM\Index(name="mail_idx", columns={"mail"}),
 *  })
 * })
 * @package CmsModule\Entities
 */
class UserEntity
{
    use MagicAccessors;
    use \CmsModule\Doctrine\Entities\DateTimeTrait;
    use \CmsModule\Doctrine\Entities\IdentifiedEntityTrait;

    const ROLE_MEMBER = 'member';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERUSER = 'superuser';
    const ROLE_SUPERVISOR = 'supervisor';


    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $lastName;

    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthDay;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $mail;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $nickname;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $newPassword;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $active = false;

    /**
     * @var string enum
     * @ORM\Column(type="string")
     */
    protected $role;




    /**
     * @return string
     */
    public function getName()
    {
        return "{$this->firstName} {$this->lastName}";
    }


    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = md5($this->username . $password);
        return $this;
    }









    public function getFullName()
    {
        return "$this->firstName $this->lastName";
    }


    /**
     * @param string $newPassword
     *
     * @return $this
     */
    public function generateNewPassword($newPassword)
    {
        $this->newPassword = md5($this->username . $newPassword);
        return $this;
    }

    /**
     * @return $this
     */
    public function activateNewPassword()
    {
        if ($this->newPassword) {
            $this->password    = $this->newPassword;
            $this->newPassword = null;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }


    /**
     * @return string
     */
    public function getBirthDayToString()
    {
        return $this->birthDay ? $this->birthDay->format('Y-m-d') : NULL;
    }

    /**
     * @param string $birthDay
     */
    public function setBirthDay($birthDay)
    {
        if (is_string($birthDay)) {
            $birthDay = DateTime::from($birthDay);
        }
        if ($birthDay) {
            $birthDay->setTime(0, 0, 0);
            if ($birthDay != $this->birthDay) $this->birthDay = $birthDay;
        }
    }







    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

}