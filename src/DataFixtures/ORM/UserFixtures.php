<?php

namespace App\DataFixtures\ORM;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture {

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $users = array(
            array(
                'email' => 'admin@acme.com',
                'password' => 'test',
                'roles' => array(User::ROLE_ADMIN)
            ),
            array(
                'email' => 'cashier@acme.com',
                'password' => 'test',
                'roles' => array(User::ROLE_CASH_REGISTER)
            )
        );

        foreach ($users as $user) {
            $entity = new User($user['email']);

            $encodedPassword = $this->passwordEncoder->encodePassword(
                    $entity, $user['password']
            );

            $entity
                    ->update($encodedPassword)
                    ->setRoles($user['roles'])
            ;

            $manager->persist($entity);
        }

        $manager->flush();
    }

}
