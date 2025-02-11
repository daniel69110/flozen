<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin')]
class CreateAdminCommand extends Command
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $entityManager)
    {
        parent::__construct();
        
    }

    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a new admin user.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user with role admin...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $questionEmail = new Question('Please enter the email:');
        $email = $helper->ask($input, $output, $questionEmail);

        $password = null;
        while(!password){
        $questionPassword = new Question('Please enter the password:');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);
        $password =$helper->ask($input, $output, $questionPassword);

        $questionPasswordRepeat = new Question('Please repeat the password:');
        $questionPasswordRepeat->setHidden(true);
        $questionPasswordRepeat->setHiddenFallback(false);
        $passwordRepeat =$helper->ask($input, $output, $questionPasswordRepeat);
        if ($passwordRepeat != $password){
            $io->error('Password doesnt match !');
            $password = null;
        }
        }
        $user = new User();
        $user
        ->setEmail($email)
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword(
            $this->passwordHasher->hashPassword($user, $password)
        )
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created');
        
        return Command::SUCCESS;

        
    }

}