<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\CommentRepository;


use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    #[Route('/', name: 'app_conference')]
    public function index(ConferenceRepository $confRep): Response
    {
        return  new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $confRep->findAll(),
        ]));
    }
    #[Route('/conference/{id}', name: 'conference')]
    public function show(Request $request, Environment $twig, Conference $conf, CommentRepository $commentRep): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRep->getCommentPaginator($conf, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [

            'conference' => $conf,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => $offset + CommentRepository::PAGINATOR_PER_PAGE
        ]));
    }
}