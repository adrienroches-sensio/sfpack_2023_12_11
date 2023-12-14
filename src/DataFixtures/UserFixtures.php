<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'adrien',
            'password' => 'adrien',
            'birthdate' => '10 July',
            'age' => 35,
            'is_admin' => true,
        ],
        [
            'username' => 'max',
            'password' => 'max',
            'birthdate' => '3 Feb',
            'age' => 15,
            'is_admin' => false,
        ],
        [
            'username' => 'lou',
            'password' => 'lou',
            'birthdate' => '22 Dec',
            'age' => 5,
            'is_admin' => false,
        ],
        [
            'username' => 'john',
            'password' => 'john',
            'birthdate' => null,
            'age' => null,
            'is_admin' => false,
        ],
    ];

    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly ClockInterface $clock,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $userDetail) {
            $user = (new User())
                ->setUsername($userDetail['username'])
                ->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash($userDetail['password']))
            ;

            if (null !== $userDetail['age']) {
                $birthYear = $this->clock->now()->modify("-{$userDetail['age']} years")->format('Y');
                $birthdate = new DateTimeImmutable("{$userDetail['birthdate']} {$birthYear}");
                $user->setBirthdate($birthdate);
            }

            if (true === $userDetail['is_admin']) {
                $user->setRoles(['ROLE_ADMIN']);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
