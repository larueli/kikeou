<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetAdminCommand extends Command
{
    protected static $defaultName = 'app:admin:set';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription("Définition d'un administrateur")
            ->addArgument('username', InputArgument::REQUIRED, 'id cas')
            ->addArgument('status', InputArgument::REQUIRED, 'true or false')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $status = $input->getArgument('status');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(["username"=>$username]);
        if($user)
        {
            if($status === "true")
            {
                $user->setRoles(array_merge($user->getRoles(), ["ROLE_ADMIN"]));

            }
            else
            {
                $roles = $user->getRoles();
                if (($key = array_search('ROLE_ADMIN', $roles)) !== false) {
                    unset($roles[$key]);
                }
                $user->setRoles($roles);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success('Succès. Utilisateur modifié');

            return Command::SUCCESS;
        }
        else
        {
            $io->error("User non trouvé !");
            return Command::FAILURE;
        }
    }
}
