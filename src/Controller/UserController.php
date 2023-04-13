<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
Use Knp\Component\Pager\PaginatorInterface;
Use Doctrine\ORM\EntityManagerInterface;
Use App\Entity\User;
Use App\Form\UserType;

class UserController extends AbstractController
{
    #[Route('/user/select', name: 'user_select')]
    public function select(UserRepository $repository,
     PaginatorInterface $paginator,
      Request $request,
      ): Response
    {
        $users = $paginator->paginate(
            $repository -> findAll(),
            $request->query->getInt('page', 1), 
            5
        );
        
        return $this->render('user/read.html.twig', ['users' => $users]);
    }

    #[Route('/user/select/{id}', name: 'user_select_id')]
    public function selectById(UserRepository $repository,
     int $id
      ): Response
    {
        $user =  $repository ->findOneBy(['id' => $id]);
        
        return $this->render('user/select.html.twig', ['user' => $user]);
    }

    #[Route('/user/new', name: 'user_create')]
    public function create(Request $request,
    EntityManagerInterface $manager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($user);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/update/{id}', name: 'user_update')]
    public function edit(
    User $user,
    Request $request,
    EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(
    User $user,
    EntityManagerInterface $manager
    ): Response
    {
       $manager ->remove($user);
       $manager ->flush();

       return $this->redirectToRoute('user_select');
    }
}
