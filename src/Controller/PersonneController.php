<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    public function __construct(private LoggerInterface $logger, private Helpers $helpers)
    {
        
    }
    
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
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response 
    {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response 
    {

        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0], 
            'ageMin' => $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    #[Route('/alls/{page?1}/{nbr?36}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbr): Response
    {
        
        echo $this->helpers->sayCc();
        
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
    
    
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(Personne $personne = null, ManagerRegistry $doctrine, Request $request, UploaderService $uploaderService, MailerService $mailer): Response
    {
        $new = false;
        //$this->getDoctrine();version<=5
        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }
        //$personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove(name:'createdAt');
        $form->remove(name:'updatedAt');
        //Mon formulaire va traiter la requete
        $form->handleRequest($request);
        //Est ce que le formulaire a été soumis
        if ($form->isSubmitted() && $form->isValid()) {
            //si oui 
                //on va ajouter l'objet personne dans la BD
                
                $photo = $form->get('photo')->getData();

            // this condition is needed because the 'photo' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personne_directory');
            
                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }
                $manager = $doctrine->getManager();
                $manager->persist($personne);
                
                $manager->flush();
                //Afficher un message de succés

                if ($new) {
                    $message = "a été ajouté avec succès!";
                    
                } else {
                    $message = "a été mis à jour avec succès!";
  
                }
                $mailMessage = $personne->getFirstname(). ' '. $personne->getName(). ' '. $message;
                $this->addFlash(type:'success', message: $personne->getName(). " ". $personne->getFirstname()." ". $message);
                $mailer->sendEmail(content: $mailMessage);

                //Rediriger vers la liste des personnes
                return $this->redirectToRoute('personne.list');
            
        } else {
            //si non 
                //On affiche notre formulaire 
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView() 
                ]);
         
        }
        
    }

    #[Route('/delete/{id<\d+>}', name:'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine, $id):RedirectResponse {
        //Récupérer la personne
        if ($personne) {
            //Si la personne existe=>on va le supprimer et retourner un flashMessage de succssé
            $manager = $doctrine->getManager();
            //Ajouter la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Executer la transaction
            $manager->flush(); 
            $this->addFlash(type: 'success', message: 'La personne: '. $personne->getFirstname(). ' '.$personne->getName(). ' '. 'a été supprimé avec succé');
        
        }else{
            
            //sinon on va retourner un flashMessage d'erreur
            $this->addFlash(type: 'error', message:"La personne d'id : $id n'existe pas");

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
            $this->addFlash(type:'success', message:'La personne: '. $personne->getFirstname(). ' '. $personne->getName(). ' '. 'a été mis à jour avec succé');
            
            } else{
            
                //sinon on va retourner un flashMessage d'erreur
                $this->addFlash(type: 'error', message: 'La personne innexistante');
    
            }
            return $this->redirectToRoute(route:'personne.list.alls');
    }
}