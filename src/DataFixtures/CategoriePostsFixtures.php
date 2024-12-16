<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Posts;
use App\Entity\Message;
use App\Entity\CategoriePosts;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;


class CategoriePostsFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $categorie = new CategoriePosts();
            $categorie->setName($faker->word());
            $categorie->setDescription($faker->sentence(4));
            // $categorie->setupdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));
            $imagePath = __DIR__ . '/../../public/images/categories/cat-post-1.jpg';
            $categorie->setImageName('cat-post-1.jpg');
            $categorie->setImageFile(new File($imagePath));
            $manager->persist($categorie);

            for ($j = 0; $j < mt_rand(5, 8); $j++) {
                $post = new Posts();
                $post->setTitle($faker->word());
                $post->setContent($faker->paragraph());
                $post->setCategoriePosts($categorie);
                $post->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));
                $imagePath = __DIR__ . '/../../public/images/posts/m-blog-5.jpg';
                $post->setImageName('m-blog-5.jpg');
                $post->setImageFile(new File($imagePath));
                $manager->persist($post);

                for($m = 0; $m < 5; $m++){
                    $message = new Message();
                    $message->setName($faker->name());
                    $message->setEmail($faker->email());
                    $message->setContent($faker->paragraph());
                    $message->setPost($post);
                    $message->setCreatedAt(\DateTimeImmutable::createFromMutable(($faker->dateTimeBetween('-6 months'))));
                    $manager->persist($message);
                }
            }

        }

        // $product = new Product();
        $manager->flush();
    }
}
