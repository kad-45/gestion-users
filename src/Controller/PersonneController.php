<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {

        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0], 
            'ageMin' => $ageMin,
            'ageMax' => $ageMax
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
        
        $personne->setFirstname(firstname: 'Rachid');
        $personne->setName(name: 'AAXLT');
        $personne->setAge(age: '35');         
        
        $entityManager->persist($personne);       
        
        $entityManager->flush();
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }

    #[Route('/delete/{id<\d+>}', name:'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine):RedirectResponse {
        //Récupérer la personne
        if ($personne) {
            //Si la personne existe=>on va le supprimer et retourner un flashMessage de succssé
            $manager = $doctrine->getManager();
            //Ajouter la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Executer la transaction
            $manager->flush(); 
            $this->addFlash(type: 'success', message: 'La personne {{ name }} {{ firstname }} a été supprimé avec succé');
        
        }else{
            
            //sinon on va retourner un flashMessage d'erreur
            $this->addFlash(type: 'error', message: 'La personne innexistante');

        }
        return $this->redirectToRoute(route:'personne.list.alls');
    }

    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age):RedirectResponse
    {
        //Verifier que la personne à mettre à jour existe
        if ($personne) {
            // si la personne existe => mettre à jour notre personne + message de succes
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash(type:'success', message:'La personne a été mis à jour avec succé');
            
            } else{
            
                //sinon on va retourner un flashMessage d'erreur
                $this->addFlash(type: 'error', message: 'La personne innexistante');
    
            }
            return $this->redirectToRoute(route:'personne.list.alls');
    }
}