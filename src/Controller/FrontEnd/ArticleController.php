<?php

namespace App\Controller\FrontEnd;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles', name:'app.articles')]
class ArticleController extends AbstractController
{
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('FrontEnd/Articles/index.html.twig', [
            'articles'=>$articleRepo->findEnable(),
        ]);
    }
}
