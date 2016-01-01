<?php
/**
 * This file is part of the CMS
 * Copyright (c) 2015
 *
 * @file    Authorizator.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Security;

use Nette\Security\Permission;
use Nette\Security\User;

class Authorizator extends Permission
{
    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERUSER = 'superuser';
    const ROLE_SUPERVISOR = 'supervisor';

    /** @var User */
    private $user;


    public function __construct(/*MemberManager $memberManager*/)
    {
        // roles
        $this->addRole(self::ROLE_GUEST);
        $this->addRole(self::ROLE_MEMBER, self::ROLE_GUEST);
        $this->addRole(self::ROLE_ADMIN);
        $this->addRole(self::ROLE_SUPERUSER);
        $this->addRole(self::ROLE_SUPERVISOR);

        // resources
        $this->addResource('Front:Homepage');
        $this->addResource('Front:Registration');
        $this->addResource('Front:Error');
        $this->addResource('Cms:Login');
        $this->addResource('Cms:Dashboard');

        // forms resource
        $this->addResource('FrontModule\Forms\ContactTeamForm');
        $this->addResource('FrontModule\Forms\TeamYoungForm');
        $this->addResource('FrontModule\Forms\ProjectForm');


        $this->setAllow();
    }


    public function setAllow()
    {
        // privileges quest
        $this->deny(self::ROLE_GUEST, Permission::ALL);
        $this->allow(self::ROLE_GUEST, 'Front:Error', Permission::ALL);
        $this->allow(self::ROLE_GUEST, 'Front:Homepage', Permission::ALL);
        $this->allow(self::ROLE_GUEST, 'Cms:Login', 'default');


        // forms
        $this->allow(self::ROLE_GUEST, 'FrontModule\Forms\ContactTeamForm', 'needRules');
        $this->allow(self::ROLE_GUEST, 'FrontModule\Forms\TeamYoungForm', 'needRules');


        // privileges member
        $this->allow(self::ROLE_MEMBER, 'Front:Registration', Permission::ALL);


        // privileges admin
        $this->allow(self::ROLE_ADMIN, Permission::ALL, Permission::ALL);




    }






}

