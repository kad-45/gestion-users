<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\NullDumper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $personnes = $repository->findAll();
        
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
        
    }

    #[Route('/alls/{page?1}/{nbr?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbr): Response
    {
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrPage = ceil(num:$nbPersonne / $nbr);
        $personnes = $repository->findBy([], [], $nbr, offset:($page - 1) * $nbr);
        
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrPage' => $nbrPage,
            'page' => $page,
            'nbr' => $nbr
        ]);
        
    }


    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null, $id): Response
    {
        //2ème méthode
        //Dans les parametres de la fonction detail on met (ManagerRegistry $repository, $id) 
        // $repository = $doctrine->getRepository(persistentObject:Personne::class);
        // $personne = $repository->find($id);
        if (!$personne) {
            $this->addFlash(type:'error', message:"La personne d'id : $id n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
        
    }
    
    
    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        //$this->getDoctrine();version<=5
        $entityManager= $doctrine->getManager();
        
        $personne = new Personne();
        $personne->setFirstname(firstname:'Mehdi');
        $personne->setName(name:'NASSIRI');
        $personne->setAge(age:'25'); 
        
        // $personne2 = new Personne();
        // $personne2->setFirstname(firstname:'Louis');
        // $personne2->setName(name:'lave');
        // $personne2->setAge(age:'30'); 
        
        //Ajouter l'operation d'insertion de la personne dans ma transaction
        $entityManager->persist($personne);
        //$entityManager->persist($personne2);

        
        //Exécute la transaction Todo
        $entityManager->flush();
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
            //'personne2' =>$personne2
        ]);
    }

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine):RedirectResponse
    {
        //Récupérer la personne
        if ($personne) {
            //Si la personne existe=>on va le supprimer et retourner un flashMessage de succssé
            $manager = $doctrine->getManager();
            //Ajouter la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Executer la transaction
            $manager->flush(); 
            $this->addFlash(type: 'success', message: 'La personne a été supprimé avec succé');
        
        }else{
            
            //sinon on va retourner un flashMessage d'erreur
            $this->addFlash(type: 'error', message: 'La personne innexistante');

        }
        return $this->redirectToRoute(route:'personne.list.alls');
    }
}