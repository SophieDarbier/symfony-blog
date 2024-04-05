<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Repository\ArticleRepository;
use App\Entity\Commentary;

class PublicController extends AbstractController
{
    // 1 Article Repository à ajouter en auto-wiring
    // 1.5 on crée une route accueil qui va afficher les articles
    // 2 On charge les articles
    // 3 On passe les articles à la vue TWIG
    // 4 On modifie la vue TWIG pour avoir les articles visibles.

    // 5 On crée une autre Route Article (qui va afficher un article et ses commentaires)
    // 6 On charge un article et ses commentaires avec ArticleRepository
    // 7 On passe les infos à la vue TWIG
    // 8 On modifie cette vue TWIG

    // 9 On crée un lien dans la vue TWIG acceuil, pour aller vers la route Article

    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository){
        // parent::__construct();
        $this->articleRepository = $articleRepository;
    }
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();

        return $this->render('public/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function article(int $id): Response
    {
        $article = $this->articleRepository->find($id);

        $user = $this->getUser();
        $commentary = new Commentary();
        $commentary->setArticle($article);
        $commentary->setDate(new \DateTime());
        $commentary->setAuthor($user->getUserIdentifier());
        $form = $this->createFormBuilder($commentary)
            ->add('content', TextType::class)
            ->add('date', DateType::class)
            ->add('author', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un commentaire'])
            ->getForm();

        return $this->render('public/article.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);

        
    }
}
