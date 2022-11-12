<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class TodoController extends AbstractController
{
    #[Route('/', name:'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        //Afficher le tableau todo
        //sinon j'initialise et j'affiche
        if (!$session->has(name:'todos')) {
            $todos = [
                'achat' => 'acheter clé usb',
                'cours' => 'Finaliser mon cours',
                'correction' => 'corriger mes examens'
            ];
            $session->set('todos', $todos);
            $this->addFlash(type:'info', message: "La liste des todos viens d'être initialisée");
        }
        //si j'ai mon tableau todo dans ma session je ne fait que l'afficher
        return $this->render(view:'todo/index.html.twig');
    }

    
     #[Route('/add/{name?Symfony6}/{content?Symfony6}', name:'todo.add')]
    public function addTodo(Request $request, $name, $content):RedirectResponse
     {
        $session = $request->getSession();
        // Vérifier si j ai mon tableau de todo dans la session
        if ($session->has(name:'todos')) {
            // si oui
            // Vérifier si on a déjà un todo avec le meme name
            $todos = $session->get(name:'todos');
            if (isset($todos[$name])) {
                // si oui afficher errerur
                $this->addFlash(type:'error', message:"Le todo d'id $name existe déjà dans la liste");
            } else {
                // si non on l'ajouter et on affiche un message de succès
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash(type:'success', message:"Le todo d'id $name a été ajouté avec succès");
            }
        } else {
            // si non
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash(type:'error',  message:"La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute(route:'todo');
    }

    #[Route("/update/{name}/{content}", name:"todo.update")]
    public function updateTodo(Request $request, $name, $content):RedirectResponse
     {
        $session = $request->getSession();
        // Vérifier si j ai mon tableau de todo dans la session
        if ($session->has(name:'todos')) {
            // si oui
            // Vérifier si le todo se trouve dans la liste
            $todos = $session->get(name:'todos');
            if (!isset($todos[$name])) {
                // si oui afficher errerur
                $this->addFlash(type:'error', message:"Le todo d'id $name n'existe pas dans la liste");
            } else {
                // si non on le modifie et on affiche un message de succès
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash(type:'success', message:"Le todo d'id $name a été modifié avec succès");
            }
        } else {
            // si non
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash(type:'error',  message:"La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute(route:'todo');
    }

    #[Route("/delete/{name}", name:"todo.delete")]
    public function deleteTodo(Request $request, $name):RedirectResponse
     {
        $session = $request->getSession();
        // Vérifier si j ai mon tableau de todo dans la session
        if ($session->has(name:'todos')) {
            // si oui
            // Vérifier si le todo existe dans la liste des todos
            $todos = $session->get(name:'todos');
            if (!isset($todos[$name])) {
                // si oui afficher errerur
                $this->addFlash(type:'error', message:"Le todo d'id $name n'existe pas dans la liste");
            } else {
                // si non on le supprime et on affiche un message de succès
                unset($todos[$name]) ;
                $session->set('todos', $todos);
                $this->addFlash(type:'success', message:"Le todo d'id $name a été supprimé avec succès");
            }
        } else {
            // si non
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash(type:'error',  message:"La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute(route:'todo');
    }

    #[Route("/reset", name:"todo.reset")]
    public function resetTodo(Request $request):RedirectResponse
     {
        $session = $request->getSession();
        $session->remove(name:'todos');
        return $this->redirectToRoute(route:'todo');
    }
}