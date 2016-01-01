<?php
/**
 * This file is part of the CMS
 * Copyright (c) 2015
 *
 * @file    UserFacade.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace CmsModule\Facades;

use CmsModule\Entities\UserEntity;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityDao;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Object;
use Nette\Utils\Random;

class UserFacade extends Object
{

    const TABLE_NAME = 'user';
    const DEFAULT_PASSWORD_CHARS = 6;

    /** @var EntityDao|UserEntity */
    private $userDao;

    /** @var array Subsciber */
    public $onUpdate;


    function __construct()
    {

    }

    /**
     * @return \Kdyby\Doctrine\EntityDao
     */
    public function getUserDao()
    {
        return $this->userDao;
    }




    public function findByLogin($email)
    {
        return $this->userDao->createQueryBuilder('e')
            ->where("e.username = :username")
            ->setParameter('username', $email)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }


    public function regeneratePassword($login, $sendEmail = true)
    {
        if ($record = $this->userDao->findOneBy(array('email' => $login))) {
            $length           = isset($this->contest['email']['passwordChars']) ? $this->contest['email']['passwordChars'] : static::DEFAULT_PASSWORD_CHARS;
            $sender           = $this->translator->translate("messages.email.sender");
            $header           = $this->translator->translate("messages.email.header");
            $random           = Random::generate($length);
            $record->password = $random;
            $this->userDao->save($record);

            if ($sendEmail) {
                $mail = new Message();
                $mail->setFrom($sender)
                    ->addTo($login)
                    ->setSubject($header)
                    ->setBody($this->translator->translate("vaše_nové_heslo", null, array('d' => $random)));
                $mailer = new SendmailMailer();
                $mailer->send($mail);
            }
        }

        return $record;
    }


    public function deleteAccount(UserEntity $user)
    {
        return $user
            ? $this->getUserDao()->delete($user)
            : false;
    }


}