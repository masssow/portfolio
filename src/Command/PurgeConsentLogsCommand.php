<?php

// src/Command/PurgeConsentLogsCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\{Attribute\AsCommand, Command\Command, Input\InputInterface, Output\OutputInterface};

#[AsCommand(name: 'app:consent:purge', description: 'Supprime les logs de consentement > 13 mois')]
final class PurgeConsentLogsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $date = (new \DateTimeImmutable('now'))->modify('-13 months');
        $qb = $this->em->createQueryBuilder()
            ->delete('App\Entity\ConsentLog', 'c')
            ->where('c.createdAt < :d')->setParameter('d', $date);
        $out->writeln('Supprimés: ' . $qb->getQuery()->execute());
        return Command::SUCCESS;
    }
}
