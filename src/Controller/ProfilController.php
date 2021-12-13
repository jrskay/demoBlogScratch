<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfilType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(CommentRepository $repo, EntityManagerInterface $manager): Response
    {
        // dd($colonnes);
        $comments = $repo->getCommentsByUser($this->getUser());
        return $this->render('profil/index.html.twig', [
            'comments' => $comments
        ]);
    }
    /**
     * @Route("/profil/edit", name="profil_edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user); // prépare l'insertion/la mise à jour des données
            $manager->flush(); // lance la requete SQL
            $this->addFlash('success', 'Vos informations ont bien été envoyé !');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/edit.html.twig', [
            'editForm' => $form->createView()
        ]);
    }
}



// Profil.php

// id 
// content 
// createdAt 
// article 
// User -> email, nom et prenom   
