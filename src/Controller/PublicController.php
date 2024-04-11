<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Repository\ArticleRepository;
use App\Entity\Commentary;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $entityManager){
        // parent::__construct();
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
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
    public function article(int $id,Request $request): Response
    {
        $article = $this->articleRepository->find($id);

        $user = $this->getUser();

        if($user){
            $commentary = new Commentary();
            $commentary->setArticle($article);
            $commentary->setDate(new \DateTime());
            $commentary->setAuthor(explode('@',$user->getEmail())[0]);
            $form = $this->createFormBuilder($commentary)
                ->add('content', TextType::class)
                ->add('author', HiddenType::class)
                ->add('submit', SubmitType::class, ['label' => 'Ajouter un commentaire'])
                ->getForm();
    
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $commentary = $form->getData();

                $this->entityManager->persist($commentary);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_article',['id'=>$id]);
            }
        

            return $this->render('public/article.html.twig', [
                'article' => $article,
                'form' => $form,
            ]);
        }

        return $this->render('public/article.html.twig', [
            'article' => $article,
        ]);      
    }
}
