<?php

namespace Unit;


use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetEmail()
    {
        $user = new User();
        $email = 'test@example.com';

        $user->setEmail($email);

        $this->assertEquals($email, $user->getEmail());
    }

    /**
     * @return void
     */
    public function testSetRoles()
    {
        $user = new User();
        $roles = ['ROLE_USER'];

        $user->setRoles($roles);

        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @return void
     */
    public function testSetPassword()
    {
        $user = new User();
        $password = 'azazazaz';

        $user->setPassword($password);

        $this->assertEquals($password, $user->getPassword());
    }



}
