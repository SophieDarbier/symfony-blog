<?php

namespace App\Command;

use App\Entity\Article;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:CreateArticles',
    description: 'Create a new article',
)]
class CreateArticleCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nb_articles', InputArgument::OPTIONAL, 'Nombre d\'articles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nbArticles = $input->getArgument('nb_articles');
        if($nbArticles < 1) return Command::FAILURE;

        for($compteur =0; $compteur < $nbArticles; $compteur++) {
            $io->warning('Creation article '.$compteur);
            $article = new Article();
            $article->setTitle("Article numero ".$compteur);
            $article->setText("Ceci est le texte de l'article ".$compteur);
            $article->setDescription("Ceci est la description de l'article ".$compteur);
            $article->setDate(new \DateTime);
            $article->setAuthor('Sophie');
            // tell Doctrine you want to (eventually) save the Article (no queries yet)
            $this->entityManager->persist($article);
        }

        // actually executes the queries (ex : the INSERT query)
        $this->entityManager->flush();
        $io->success($compteur.' articles crees !');

        return Command::SUCCESS;
    }
}


