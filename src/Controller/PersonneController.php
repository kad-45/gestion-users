<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne'), IsGranted('ROLE_USER')]
class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger, 
        private Helpers $helpers,
        private EventDispatcherInterface $dispacher)
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

    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf)
    {
        $html = $this->render('personne/detail.html.twig', [
        'personne' => $personne
        ]);
        $pdf->showPdfFile($html);
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

    #[Route('/alls/{page?1}/{nbr?36}', name: 'personne.list.alls'), IsGranted("ROLE_USER")]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbr): Response
    {
        
        //echo $this->helpers->sayCc();
        
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrPage = ceil(num:$nbPersonne / $nbr);
        $personnes = $repository->findBy([], [], $nbr, offset:($page - 1) * $nbr);
        //On istancier L'objet qu'on va dispatcher
        $listAllPersonneEvent = new ListAllPersonnesEvent(count($personnes));
        //On dispatch l'objet qui est notre evenement
        $this->dispacher->dispatch($listAllPersonneEvent, eventName: ListAllPersonnesEvent::LIST_ALL_PERSONNE_EVENT); 
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
        //2??me m??thode
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
    
    
    #[Route('/edit/{id?0}', name: 'personne.edit'), IsGranted('ROLE_ADMIN')]
    public function addPersonne(Personne $personne = null, ManagerRegistry $doctrine, Request $request, UploaderService $uploaderService, MailerService $mailer): Response
    {
        $this->denyAccessUnlessGranted(attribute:'ROLE_ADMIN');
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
        //Est ce que le formulaire a ??t?? soumis
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
            if ($new) {
                $message = "a ??t?? ajout?? avec succ??s!";
                $personne->setCreatedBy($this->getUser());
                
            } else {
                $message = "a ??t?? mis ?? jour avec succ??s!";

            }
                $manager = $doctrine->getManager();
                $manager->persist($personne);
                
                $manager->flush();
                //Afficher un message de succ??s
                
                if ($new) {
                    //On a cr??er notre ??v??nement ?? dispatcher
                    $addPersonneEvent = new AddPersonneEvent($personne);
                    //On va maintenant dispatcher cet evenement
                    $this->dispacher->dispatch($addPersonneEvent, eventName:AddPersonneEvent::ADD_PERSONNE_EVENT);
                }
                
                $this->addFlash(type:'success', message: $personne->getName(). " ". $personne->getFirstname()." ". $message);

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

    #[Route('/delete/{id<\d+>}', name:'personne.delete'), IsGranted(attribute:'ROLE_ADMIN')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine, $id):RedirectResponse {
        //R??cup??rer la personne
        if ($personne) {
            //Si la personne existe=>on va le supprimer et retourner un flashMessage de succss??
            $manager = $doctrine->getManager();
            //Ajouter la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Executer la transaction
            $manager->flush(); 
            $this->addFlash(type: 'success', message: 'La personne: '. $personne->getFirstname(). ' '.$personne->getName(). ' '. 'a ??t?? supprim?? avec succ??');
        
        }else{
            
            //sinon on va retourner un flashMessage d'erreur
            $this->addFlash(type: 'error', message:"La personne d'id : $id n'existe pas");

        }
        return $this->redirectToRoute(route:'personne.list.alls');
    }

    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age):RedirectResponse
    {
        //Verifier que la personne ?? mettre ?? jour existe
        if ($personne) {
            // si la personne existe => mettre ?? jour notre personne + message de succes
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash(type:'success', message:'La personne: '. $personne->getFirstname(). ' '. $personne->getName(). ' '. 'a ??t?? mis ?? jour avec succ??');
            
            } else{
            
                //sinon on va retourner un flashMessage d'erreur
                $this->addFlash(type: 'error', message: 'La personne innexistante');
    
            }
            return $this->redirectToRoute(route:'personne.list.alls');
    }
}