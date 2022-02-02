<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\CategoryRepository;
use App\Repository\WishRepository;
use App\Service\Censurator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    #[Route('/list', name: 'wish_list')]
    public function list(WishRepository $repo): Response
    {
        $wishes = $repo ->findPublishedWishesWithCategory();
        return $this->render('wish/list.html.twig', ['wishes' =>$wishes]);
    }

    #[Route('/detail/{id}', name: 'wish_detail', requirements: ['id' => '\d+'])]
    public function detail(Wish $wish): Response
    {
        return $this->render('wish/detail.html.twig', ['wish'=>$wish]);
    }

    /* #[Route('/detail/{id}', name: 'wish_detail', requirements: ['id' => '\d+'])]
     public function detail($id, WishRepository $repo): Response
     {
         $wish = $repo->find($id);
         if(!$wish){
             throw new NotFoundHttpException();
         }
         return $this->render('wish/detail.html.twig', ['wish'=>$wish]);
     }*/

    #[Route('/detail/{title}', name: 'wish_detailbytitle')]
    public function detailByTitle(Wish $wish): Response
    {
        return $this->render('wish/detail.html.twig', ['wish'=>$wish]);
    }


    #[Route('/ajouterJeuDEssai', name: 'wish_ajout')]
    public function ajouterJeuDEssai(EntityManagerInterface $em, CategoryRepository $categoryRepository){
        $wish = new Wish();

        $wish ->setTitle("Aller en Nouvelle-Zélande")
                ->setAuthor("Claire")
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
            ->setIsPublished(true)
            ->setDateCreated(new \DateTime());
        $cat = $categoryRepository->findByName('Travel and Adventure')[0];
        $wish ->setCategory($cat);
        $em->persist($wish);
        $em->flush();
        return new Response("Jeu d'essai inséré");
    }

    #[Route('/add', name: 'wish_add')]
    public function add(Request $request, EntityManagerInterface $em, Censurator $censurator){
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('notice', 'please login...');
            return $this->redirectToRoute('app_login');
        }
        $wish = new Wish();
        $wish->setIsPublished(true)
            ->setDateCreated(new \DateTime());
        $wish->setAuthor($this->getUser()->getUserIdentifier());
        $formbuilder = $this->createForm(WishType::class, $wish);

        $formbuilder->handleRequest($request);
        if($formbuilder->isSubmitted() && $formbuilder->isValid()) {
            $descriptionAVerifier = $wish->getDescription();
            $wish->setDescription($censurator->purify($descriptionAVerifier));
            $em->persist($wish);
            $em->flush();
            $this->addFlash('success', "Idea successfully added!");
            return  $this->redirectToRoute('wish_detail', ['num'=>$wish->getId()]);
        }
        $wishform = $formbuilder->createView();
        return $this ->render('wish/add.html.twig', ['wishform'=>$wishform]);
    }

}
