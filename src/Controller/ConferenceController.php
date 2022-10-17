<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Comment;
use App\Repository\ConferenceRepository;
use App\Repository\CommentRepository;
use App\Form\CommentFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    private $twig;
    private $em;
    public function __construct(Environment $twig, EntityManagerInterface  $em)
    {
        $this->twig = $twig;
        $this->em = $em;
    }
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $confRep): Response
    {
        return  new Response($this->twig->render('conference/index.html.twig', []));
    }
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(Request $request, Conference $conf, CommentRepository $commentRep, ConferenceRepository $confRep, string $photoDir): Response
    {
        $comment = new Comment;

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setConference($conf);
            // if ($photo = $form['photo']->getData()) {
            //     $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
            //     try {
            //         $photo->move($photoDir, $filename);
            //     } catch (FileException $e) {
            //         // unable to upload the photo, give up
            //     }
            //     $comment->setPhotoFilename($filename);
            // }
            $this->em->persist($comment);
            $this->em->flush();
            return $this->redirectToRoute('conference', ['slug' => $conf->getSlug()]);
        }


        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRep->getCommentPaginator($conf, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conferences' => $confRep->findAll(),
            'conference' => $conf,
            'comments' => $paginator,
            'form' => $form->createView(),
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => $offset + CommentRepository::PAGINATOR_PER_PAGE
        ]));
    }
}