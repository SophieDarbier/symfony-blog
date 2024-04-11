<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:CreateAdminCommand',
    description: 'Create an admin',
)]

class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository ,EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email pour administrateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        if(!$email) return Command::FAILURE;

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if(!$user) 
        {
            $io->success("L'email ne correspond pas a un compte existant");
            return Command::FAILURE;
        }

        $user->setRoles(["ROLE_ADMIN"]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("L'email $email a été ajoutée aux administrateurs");

        return Command::SUCCESS;
    }

}