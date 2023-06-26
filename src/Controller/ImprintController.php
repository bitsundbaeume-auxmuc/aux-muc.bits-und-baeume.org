<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImprintController extends AbstractController
{
    #[Route('/impressum', options: ['_menu' => 'main', '_menu_title' => 'Impressum'])]
    public function index() : Response {
        return $this->render('impressum.html.twig');
    }
}