<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    #[Route('/list', name: 'wish_list')]
    public function list(WishRepository $repo): Response
    {
        $wishes = $repo ->findBy(['isPublished'=>true], ['dateCreated' => 'DESC']);
        return $this->render('wish/list.html.twig', ['wishes' =>$wishes]);
    }

    #[Route('/detail/{num}', name: 'wish_detail', requirements: ['num' => '\d+'])]
    public function detail($num, WishRepository $repo): Response
    {
        $wish = $repo->find($num);
        if(!$wish){
            throw new NotFoundHttpException();
        }
        return $this->render('wish/detail.html.twig', ['wish'=>$wish]);
    }

    #[Route('/ajouterJeuDEssai', name: 'wish_add')]
    public function ajouterJeuDEssai(EntityManagerInterface $em){
        $wish = new Wish();
        $wish ->setTitle("Aller en Nouvelle-Zélande")
                ->setAuthor("Claire")
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
            ->setIsPublished(true)
            ->setDateCreated(new \DateTime());
        $em->persist($wish);
        $em->flush();
        return $this ->render('wish/list.html.twig');
    }

}
