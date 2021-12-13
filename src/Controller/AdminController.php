<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     */
    public function admin_Articles(ArticleRepository $repo, EntityManagerInterface $manager)
    {
        // Cette methode va afficher la liste des articles presents en BDD
        // Un affiche ca veut dire =W repository

        // le manager va servir à recuperer le nom des champs 
        
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        // dd($colonnes);
        $articles = $repo->findAll();
        return $this->render('admin/admin_articles.html.twig', [
            'articles' => $articles,
            'colonnes' => $colonnes
        ]);
        
    }

    /**
     * @Route("/admin/articles/new", name="admin_new_article")
     * @Route("/admin/articles/edit/{id}", name="admin_edit_article")
     */
    public function formArticle(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        if(!$article)
        {
            $article = new Article;
            $article->setCreatedAt(new \DateTime());
            
            // dans le cas ou $article est null, je crée un objet vide de la class Article
            // puis je set les dates de création et de mise à jour à aujourd'hui
        }

            $form = $this->createForm(ArticleType::class, $article);
            // Je recupere le formulaire ArticleType
            $form->handleRequest($request);
            // Permet de verifier des infos sur les superglobales (est-ce un formulaire POST ou GET? est-ce tous les champs sont remplis ? ect ect)

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setUpdatedAt(new \DateTime());
            // dans tous les cas, que l'article soit crée ou edit", on set la date de mise à jour à aujourd'hui 
            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', 'succès !');
            return $this->redirectToRoute('admin_articles');

            // si le formulaire est soumis est valide, je crée ou je met à jour l'article, je crée un message de notification puis je redirige vers la liste des articles de l'admin ou j'afficherais le messag de notification donc le flash !
        }
        return $this->render('admin/form_article.html.twig',[
            'articleForm' => $form->createView(),
            'editMode' => $article->getId() !== NULL
        ]);
    }

    /**
     * @Route("/admin/articles/delete/{id}", name="admin_delete_article")
     */
    public function deleteArticle(Article $article, EntityManagerInterface $manager)
    {
    	$manager->remove($article);
    	// remove() prépare la suppression
    	$manager->flush();
    	$this->addFlash('success',"L'article a bien été supprimé !");
    	return $this->redirectToRoute('admin_articles');
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function admin_Categories(CategoryRepository $repo, EntityManagerInterface $manager)
    {
       
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();
        // dd($colonnes);
        $categories = $repo->findAll();
        return $this->render('admin/admin_categories.html.twig', [
            'categories' => $categories,
            'colonnes' => $colonnes
        ]);
        
    }

    /**
     * @Route("/admin/category/new", name="admin_new_category")
     * @Route("/admin/category/edit/{id}", name="admin_edit_category")
     */
    public function formCategory(Request $request, EntityManagerInterface $manager, Category $category = null)
    {
        if(!$category)
        {
            $category = new Category;
        }

        $form = $this->createForm(CategoryType::class, $category);
        // Je recupere le formulaire ArticleType
        $form->handleRequest($request);
        // Permet de verifier des infos sur les superglobales (est-ce un formulaire POST ou GET? est-ce tous les champs sont remplis ? ect ect)

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a été ajouter avec succès !");
            return $this->redirectToRoute('admin_categories');

            // si le formulaire est soumis est valide, je crée ou je met à jour l'article, je crée un message de notification puis je redirige vers la liste des articles de l'admin ou j'afficherais le messag de notification donc le flash !
        }
        return $this->render('admin/form_category.html.twig',[
            'categoryForm' => $form->createView(),
            'editMode' => $category->getId() !== NULL
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_delete_category")
     */
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {
        $nomCategory = $category->getTitle();
        $manager->remove($category);
        // remove() prépare la suppression
        $manager->flush();
        $this->addFlash('success','La catégorie '. $nomCategory .' a bien été supprimé !');
        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/admin/comments", name="admin_comments")
     */
    public function admin_Comments(CommentRepository $repo, EntityManagerInterface $manager)
    {
       
        $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();
        // dd($colonnes);
        $comments = $repo->findAll();
        return $this->render('admin/admin_comments.html.twig', [
            'comments' => $comments,
            'colonnes' => $colonnes
        ]);        
    }

    /**
     * @Route("/admin/comment/new", name="admin_new_comment")
     * @Route("/admin/comment/edit/{id}", name="admin_edit_comment")
     */
    public function formComment(Request $request, EntityManagerInterface $manager, Comment $comment = null)
    {
        if(!$comment)
        {
            $comment = new Comment;
            $comment->setCreatedAt(new \DateTime());
        }

        $form = $this->createForm(CommentType::class, $comment);
        // Je recupere le formulaire ArticleType
        $form->handleRequest($request);
        // Permet de verifier des infos sur les superglobales (est-ce un formulaire POST ou GET? est-ce tous les champs sont remplis ? ect ect)

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', "Le commentaire de " . $comment->getAuthor()->getPrenom() . " a été ajouter avec succès !");
            return $this->redirectToRoute('admin_comments');

            // si le formulaire est soumis est valide, je crée ou je met à jour l'article, je crée un message de notification puis je redirige vers la liste des articles de l'admin ou j'afficherais le messag de notification donc le flash !
        }
        return $this->render('admin/form_comment.html.twig',[
            'commentForm' => $form->createView(),
            'editMode' => $comment->getId() !== NULL
        ]);
    }

    /**
     * @Route("/admin/comment/delete/{id}", name="admin_delete_comment")
     */
    public function deleteComment(Comment $comment = null, EntityManagerInterface $manager)
    {
        // Si n'y a pas de commentaire lié à cet id à supprimer, je redirige vers la liste des commentaires
        if (!$comment) {
            return $this->redirectToRoute('admin_comment');
        }
        $nomCommentaire = $comment->getAuthor()->getPrenom();
        $manager->remove($comment);
        // remove() prépare la suppression
        $manager->flush();
        $this->addFlash('success', "Le commentaire de " . $nomCommentaire . " a été supprimé !");
        return $this->redirectToRoute('admin_comments');
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function admin_Users(UserRepository $repo, EntityManagerInterface $manager)
    {
       
        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();
        // dd($colonnes);
        $users = $repo->findAll();
        return $this->render('admin/admin_users.html.twig', [
            'users' => $users,
            'colonnes' => $colonnes
        ]);        
    }

    /**
     * @Route("/admin/user/new", name="admin_new_user")
     * @Route("/admin/user/edit/{id}", name="admin_edit_user")
     */
    public function formUser(Request $request, EntityManagerInterface $manager, User $user = null)
    {
        if(!$user)
        {
            $user = new User;
        }

        $form = $this->createForm(UserType::class, $user);
        // Je recupere le formulaire ArticleType
        $form->handleRequest($request);
        // Permet de verifier des infos sur les superglobales (est-ce un formulaire POST ou GET? est-ce tous les champs sont remplis ? ect ect)

        if($form->isSubmitted() && $form->isValid())
        {
            if (!$user->getId() || $form->get('plainPassword')->getData()) {
                $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
            }
            // la condition avant fait que lorsque l'on fait une modification on n'est pas obligé de changer le mdp aussi 
            // si c'est un nouvel utilisateur ou qu'on modifie le mdp d'un utilisateur existant alors on le hash le nouveau mdp
            // si on édite un utilisateur sans toucher à son mdp il gardera l'ancien mdp
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "L'utilisateur " . $user->getPrenom() . " a été ajouter avec succès !");
            return $this->redirectToRoute('admin_users');

            // si le formulaire est soumis est valide, je crée ou je met à jour l'article, je crée un message de notification puis je redirige vers la liste des articles de l'admin ou j'afficherais le messag de notification donc le flash !
        }
        return $this->render('admin/form_user.html.twig',[
            'userForm' => $form->createView(),
            'editMode' => $user->getId() !== NULL
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_delete_user")
     */
    public function deleteUser(User $user, EntityManagerInterface $manager)
    {
        $nomUser = $user->getPrenom();
        $manager->remove($user);
        // remove() prépare la suppression
        $manager->flush();
        $this->addFlash('success', "L'utilisateur  " . $nomUser . " a été supprimé !");
        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/admin/contacts", name="admin_contacts")
     */
    public function admin_Contacts(ContactRepository $repo, EntityManagerInterface $manager)
    {
    
        $colonnes = $manager->getClassMetadata(Contact::class)->getFieldNames();
        $contacts = $repo->findAll();
        return $this->render('admin/admin_contacts.html.twig', [
            'contacts' => $contacts,
            'colonnes' => $colonnes
        ]);
    }

    /**
     * @Route("/admin/contact/new", name="admin_new_contact")
     * @Route("/admin/contact/edit/{id}", name="admin_edit_contact")
     */
    public function formContact(Contact $contact = null, Request $rq, EntityManagerInterface $manager)
    {
        if(!$contact)
            $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($rq);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash('success', 'Le contact ' . $contact->getFirstname() . ' a bien été crée / modifié !');
            // j'affiche le nom de la nouvelle catgorie dans le flash
            return $this->redirectToRoute('admin_contacts');
        }
        return $this->render('admin/form_contact.html.twig', [
            'form' => $form->createView(),
            'editMode' => $contact->getId() != NULL
        ]);
    }

    /**
     * @Route("/admin/contact/delete/{id}", name="admin_delete_contact")
     */
    public function deleteContact(Contact $contact, EntityManagerInterface $manager) 
    {
        $nomContact = $contact->getFirstname();
        $manager->remove($contact);
        $manager->flush();

        $this->addFlash('success', "Le membre '$nomContact' a bien été supprimé !");
        return $this->redirectToRoute('admin_contacts');
    }

}

