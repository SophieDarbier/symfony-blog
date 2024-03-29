<?php

namespace App\Command;

use App\Entity\Commentary;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Article;
use App\Repository\ArticleRepository;

#[AsCommand(
    name: 'app:CreateCommentaries',
    description: 'Create a new commentary on a given article',
)]
class CreateCommentaryCommand extends Command{

    private ArticleRepository $articleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ArticleRepository $articleRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nb_commentaries', InputArgument::OPTIONAL, 'Number of commentaries to create')
            ->addArgument('id_article', InputArgument::OPTIONAL, 'Id de l\'articles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $idArticle = $input->getArgument('id_article');
        $article = $this->articleRepository->find($idArticle);

        if (!$article) {
            $io->error('Impossible de trouver l\'article'.$idArticle);
            return Command::FAILURE;
        }

        $nbCommentaries = $input->getArgument('nb_commentaries');

        for($compteur =0; $compteur < $nbCommentaries; $compteur++) {
            $io->comment('Creation commentaire '.$compteur);
            $commantaire = new Commentary();
            $commantaire->setContent("Commentaire  ".$compteur);
            $commantaire->setAuthor('Nicolas');
            $commantaire->setDate(new \DateTime);
            $commantaire->setArticle($article);
            // tell Doctrine you want to (eventually) save the Article (no queries yet)
            $this->entityManager->persist($commantaire);
        }

        $this->entityManager->flush();
        $io->success($compteur.' commentaires crées !');

        return Command::SUCCESS;
    }
}
