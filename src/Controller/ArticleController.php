<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireForm;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(Article $article, CommentaireRepository $commentaireRepository, EntityManagerInterface $entityManager, Request $request): Response
    {

        $commentaire = new Commentaire();
        $commentForm = $this->createForm(CommentaireForm::class, $commentaire);

        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $commentaire->setArticle($article);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        $comments = $commentaireRepository->findBy(['article' => $article], ['id' => 'DESC']);

        $category = $article->getCategory();
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'categorys' => $category,
            'comments' => $comments,
            'commentForm' => $commentForm,
        ]);
    }
}
