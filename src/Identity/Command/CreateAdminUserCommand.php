<?php

declare(strict_types=1);

namespace App\Identity\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:user:create-admin', description: 'Creates or updates an admin user for local access.')]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email address')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password (required when creating a new user)')
            ->addOption('reset-password', null, InputOption::VALUE_NONE, 'Update password for an existing user');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = strtolower(trim((string) $input->getArgument('email')));
        $password = trim((string) $input->getArgument('password'));
        $resetPassword = (bool) $input->getOption('reset-password');

        if ('' === $email) {
            $io->error('Email must not be empty.');

            return Command::INVALID;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        $createdUser = false;
        if (!$user instanceof User) {
            $user = (new User())
                ->setEmail($email)
                ->setAvatarType(User::AVATAR_TYPE_DEFAULT)
                ->setAvatarPath(null);
            $this->entityManager->persist($user);
            $createdUser = true;
        }

        if ($createdUser && '' === $password) {
            $io->error('Password is required when creating a new user.');

            return Command::INVALID;
        }

        $passwordUpdated = false;
        if ($createdUser || $resetPassword) {
            if ('' === $password) {
                $io->error('Password is required when using --reset-password.');

                return Command::INVALID;
            }

            $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
            $passwordUpdated = true;
        }

        $role = $this->roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
        $createdRole = false;
        if (!$role instanceof Role) {
            $role = (new Role())
                ->setName('ROLE_ADMIN')
                ->setLabel('Administrator');
            $this->entityManager->persist($role);
            $createdRole = true;
        }

        $user->addRole($role);
        $this->entityManager->flush();

        $io->success(sprintf('Admin user is ready: %s', $email));
        $io->definitionList(
            ['created_user' => $createdUser ? 'yes' : 'no'],
            ['password_updated' => $passwordUpdated ? 'yes' : 'no'],
            ['created_role' => $createdRole ? 'yes' : 'no'],
            ['role' => 'ROLE_ADMIN'],
        );

        return Command::SUCCESS;
    }
}
