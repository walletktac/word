<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['seed-users'];
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            email: 'trzcinskikar@gmail.com',
            password: 'test123',
            name: 'Admin User',
            roles: ['ROLE_ADMIN']
        );

        $hashed = $this->hasher->hashPassword($user, 'test123');
        $user->setPassword($hashed);

        $manager->persist($user);
        $manager->flush();
    }
}