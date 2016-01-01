<?php

namespace CmsModule\Security;

use CmsModule\Facades\UserFacade;
use CmsModule\Repository\UserRepository;
use Nette;

/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
    const
        COLUMN_ID = 'id',
        COLUMN_NAME = 'username',
        COLUMN_ROLE = 'role',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_NEW_PASSWORD_HASH = 'newPassword';

    /** @var UserFacade */
    private $userFacade;

    /** @var UserRepository */
    private $userRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * Performs an authentication.
     *
     * @param array $credentials
     *
     * @throws \Nette\Security\AuthenticationException
     * @return Nette\Security\Identity
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;


        $row = $this->userRepository->findByUsername($username);

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::IDENTITY_NOT_FOUND);

        } elseif ($username !== $row[self::COLUMN_NAME]) {
            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::INVALID_CREDENTIAL);

        } elseif (md5($username . $password) !== $row[self::COLUMN_PASSWORD_HASH]) {
            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::INVALID_CREDENTIAL);
        }

        $arr = $row;
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        unset($arr[self::COLUMN_NEW_PASSWORD_HASH]);
        return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }


    /**
     * Adds new user.
     * @todo exchange to doctrine
     *
     * @param       $username
     * @param       $password
     * @param array $additionalData
     *
     * @throws DuplicateNameException
     * @return void
     */
    public function add($username, $password, $additionalData = array())
    {
        try {
            $this->database->table(self::SOUTEZ_TABLE_NAME)->insert(array(
                self::SOUTEZ_COLUMN_NAME          => $username,
                self::SOUTEZ_COLUMN_PASSWORD_HASH => md5($password),
            ));

            $user = $this->database->table(self::SOUTEZ_TABLE_NAME)->where(self::SOUTEZ_COLUMN_NAME . " = ?", $username)->fetch();
            $user->update($additionalData);

        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
        }
    }


}

class DuplicateNameException extends \Exception
{
}