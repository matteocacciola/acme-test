<?php

namespace Acme\Bundle\UserBundle\DataFixtures\ORM;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\Bundle\UserBundle\Entity\User;

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
                'email' => 'test@acme.com',
                'password' => 'test'
            )
        );

        foreach ($users as $user) {
            $entity = new User($user['email']);

            $encodedPassword = $this->passwordEncoder->encodePassword(
                    $entity, $user['password']
            );

            $entity->update($encodedPassword);

            $manager->persist($entity);
        }

        $manager->flush();
    }

}
