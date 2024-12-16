<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Posts;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;


class PostFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        // for ($i=0; $i < 20; $i++) { 
        //         $post = new Posts();
        //         $post->setTitle($faker->word());
        //         $post->setContent($faker->paragraph());
        //         $post->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));
        //         $imagePath = __DIR__ . '/../../public/build/images/img/blog/main-blog/m-blog-5.jpg';
        //         $post->setImageFile(new File($imagePath));
        //         $manager->persist($post);

        //     }
        
        //         $manager->flush();
    }
}
