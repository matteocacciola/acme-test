<?php

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\VatClass;

class VatClassFixtures extends Fixture {

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $classes = array(
            array('vat' => 0.06),
            array('vat' => 0.21)
        );

        foreach ($classes as $class) {
            $entity = new VatClass($class['vat']);

            $manager->persist($entity);
        }

        $manager->flush();
    }

}
