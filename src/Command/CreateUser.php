<?php

namespace App\Command;

use App\Entity\GlobaUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUser extends Command
{
    protected static $defaultName = 'create:user';

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    protected function configure()
    {
        $this->setDescription('Get word');
    }

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new GlobaUser();
        $user->setUsername('Teacher');
        $user->setRoles(['ROLE_ADMIN']);
        $hash = $this->passwordEncoder->encodePassword($user, 'Teacher1');
        $user->setPassword($hash);
        $this->em->persist($user);
        $this->em->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success('Success');

        return 0;
    }

}
