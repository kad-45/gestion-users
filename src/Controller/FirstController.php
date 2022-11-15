<?php

namespace App\Controller;

use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/order/{maVar}', name: 'test.order.route')]
    public function testOrderRoute($maVar)
    {
        return new Response(content: "<html><body>$maVar</body></html>");
    }
    
    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'name' => 'TAFTAF',
            'firstname' => 'Kad'
        ]);
    }

    #[Route('/sayHello/{name?World}/{firstname?!}', name: 'say.hello')]
    public function sayHello(Request $request, $name, $firstname): Response
    {
        //dd($request);
        return $this->render('first/hello.html.twig', [
            'name' => $name,
            'firstname' => $firstname,
            'path' => '           '
           
        ]);
    }

    #[Route('multi/{entier1<\d+>?0}/{entier2<\d+>?0}', name:'multi')]
    public function multiplication($entier1, $entier2)
    {
            $resultat = $entier1 * $entier2;
            return new Response(content:"<h1>$resultat</h1>");
    }
}