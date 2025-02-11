<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setEmail('admin@mail.com');
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, 'Password98$'));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user
                  -> setEmail('mail' . $i . '@mail.com')
                  -> setPassword($this->userPasswordHasher->hashPassword(
                      $user,
                      plainPassword:'Password98$'
                  ))
                ;

                $manager->persist($user);
        }

        $manager->flush();
    }
}
