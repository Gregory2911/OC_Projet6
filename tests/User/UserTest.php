<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{

    public function getEntity()
    {
        return (new User())
            ->setUsername("Murielle")
            ->setEmail('mumu@domaine.fr')
            ->setPassword('12345678')
            ->setCreatedAt(new \DateTime());
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
        $messages = [];
        /**@var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername("M"), 1);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail("admin@snowtrick"), 1);
    }

    public function testInvalidPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword("1234"), 1);
    }

    public function testInvalidBlankUserName()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(""), 2);
    }

    public function testInvalidBlankEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(""), 1);
    }

    public function testInvalidBlankPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword(""), 2);
    }
}
