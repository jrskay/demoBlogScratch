<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Comment;
use App\Form\ContactType;
use App\Form\ArticleType;
use App\Form\PostCommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show(Article $article = null, Request $request, EntityManagerInterface $manager)
    //si l'id de l'article n'existe pas, $article = null 
    {
        if(!$article)
        {
            return $this->render("error/404.html.twig");
        }
        $comment = new Comment;
        $form = $this->createForm(PostCommentType:: class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été ajouté !');
            // addFlash est une méthode qui permet de stocker des messages de notification pour les afficher dans une vue
            return $this->redirectToRoute('blog_show',[
                'id' => $article->getId()
            ]);
            // permet de rechercger la page et vider le formulaire
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'postForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     *@Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        if(!$article)
        {
            $article = new Article;
            $article->setCreatedAt(new \DateTime());
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute("blog_show",[
                'id' => $article->getId()
            ]);
        }
        return $this->render("blog/form.html.twig", [
            'articleForm' => $form->createView(),
            'editMode' => $article->getId() !== NULL
        ]);
    }

    /**
     * @Route("/blog/contact", name="blog_contact")
     */
    public function contact(EntityManagerInterface $manager, Request $request, ContactNotification $notification)
    {
        $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($contact);
            $manager->flush();
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé !');
            // addFlash est une méthode qui permet de stocker des messages de notification pour les afficher dans une vue
            return $this->redirectToRoute('blog_contact');
            // permet de rechercger la page et vider le formulaire
        }
        return $this->render("blog/contact.html.twig",[
            'formContact' => $form->createView()
        ]);
    }
}
