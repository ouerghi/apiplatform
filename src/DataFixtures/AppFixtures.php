<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var \Faker\Factory $faker
     */
    private $faker;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker =  \Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
         $this->user($manager);
         $this->loadBlogPost($manager);
         $this->comment($manager);
    }
    public function loadBlogPost(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference('admin');
        for ($i=0; $i<100;$i++)
        {
            $post = new \App\Entity\BlogPost();
            $post->setTitle($this->faker->realText(30));
            $post->setSlug($this->faker->slug);
            $post->setContent($this->faker->realText());
            $post->setAuthor($user);
            $post->setPublished( $this->faker->dateTimeThisYear);

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }
      $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function comment(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference('admin');
        for ($i=0; $i<100; $i++)
        {
            for ($j=0;$j<rand(1,10); $j++)
            {
                $comment = new Comment();
                $comment->setAuthor($user);
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setContent($this->faker->realText());
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
    public function user(ObjectManager $manager)
    {
         $user = new User();
         $user->setUsername('admin');
         $user->setPassword($this->encoder->encodePassword($user, '123456'));
         $user->setEmail('admin@gmail.com');
         $user->setName('ouerghi mahdi');

         $this->addReference('admin', $user);
         $manager->persist($user);
         $manager->flush();
    }
}
