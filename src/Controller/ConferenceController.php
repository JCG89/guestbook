<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\CommentRepository;


use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    #[Route('/', name: 'app_conference')]
    public function index(Environment $twig, ConferenceRepository $confRep): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $confRep->findAll(),
        ]);
    }
    #[Route('/conference/{id}', name: 'conference')]
    public function show(Environment $twig, Conference $conf, CommentRepository $commentRep): Response
    {
        return new Response($twig->render('conference/show.html.twig', [

            'conference' => $conf,
            'comments' => $commentRep->findBy(['conference' => $conf], ['createdAt' => 'DESC'])
        ]));
    }
}