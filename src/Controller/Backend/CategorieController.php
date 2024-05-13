<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('admin/categories', name :'admin.categories')]
class CategorieController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {}

    #[Route('', name:".index", methods:['GET'])]
    public function index (CategorieRepository $categorieRepo): Response
    {
        return $this->render ('Backend/Categories/index.html.twig',[
            'categories' => $categorieRepo->findAllOrderByName()
        ]);
    }

    #[Route('/create', name: '.create', methods:['POST','GET'])]
    public function create (Request $request): Response
    {
        $categorie = new Categorie();


        $form = $this ->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($categorie);
            $this->em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');
            return $this->redirectToRoute('admin.categories.index');
        }
        return $this->render('Backend/Categories/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET','POST'])]
    public function update (?Categorie $categorie, Request $request): Response | RedirectResponse 
    {
        if (!$categorie){
            $this->addFlash('error', 'Catégorie non trouvée');
            return $this->redirectToRoute('admin.categories.index');
        }
        $form= $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($categorie);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès');
            return $this->redirectToRoute('admin.categories.index');
        }
        return $this->render('Backend/Categories/update.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name:'.delete', methods: ['POST'])]
    public function delete (?Categorie $categorie, Request $request): RedirectResponse
    {
        if(!$categorie){
            $this->addFlash('error', 'Catégorie non trouvée');

            return $this->redirectToRoute('admin.categories.index');
        }
        if($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('token')))
        {
            $this->em->remove($categorie);
            $this->em->flush();

            $this->addFlash('success', 'Catégorie supprimée avec succès');
            return $this->redirectToRoute('admin.categories.index');
        } else{
            $this->addFlash('error', 'Token CSRF invlaid');
        }
        return $this->redirectToRoute('admin.categories.index');

    }

    #[Route('/{id}/switch', name: '.switch', methods: ['GET'])]
    public function switch(?Categorie $categorie): JsonResponse
    {
        if (!$categorie) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'categorie not found',
            ], 404);
        }

        $categorie->setEnable(!$categorie->isEnable());
        $this->em->persist($categorie);
        $this->em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'message' => 'Visibility changed',
            'enable' => $categorie->isEnable(),
        ]);
    }


}
