<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Message;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class MessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create('fr', 'FR');


        //  $message = new Message();
        //  $message->setName($faker->name());
        //  $message->setEmail($faker->email());
        //  $message->setContent($faker->paragraph());
        //  $message->setCreatedAt($faker->dateTimeImmutable());
        // // $manager->persist($product);

        // $manager->flush();
    }
}
