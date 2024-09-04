<?php

namespace App\Controller\Frontend;

use App\Entity\InfoUser;
use App\Form\InfoUserType;
use App\Repository\InfoUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/profile')]
final class InfoUserController extends AbstractController
{

    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route(name: 'app_info_user_index', methods: ['GET'])]
    public function index(InfoUserRepository $infoUserRepository): Response
    {
        return $this->render('Frontend/info_user/index.html.twig', [
            'info_users' => $infoUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_info_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infoUser = new InfoUser();
        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set the authenticated user
            $infoUser->setUser($this->tokenStorage->getToken()->getUser());

            $entityManager->persist($infoUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_info_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Frontend/info_user/new.html.twig', [
            'info_user' => $infoUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_user_show', methods: ['GET'])]
    public function show(InfoUser $infoUser): Response
    {
        return $this->render('Frontend/info_user/show.html.twig', [
            'info_user' => $infoUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_info_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoUser $infoUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfoUserType::class, $infoUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_info_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Frontend/info_user/edit.html.twig', [
            'info_user' => $infoUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_user_delete', methods: ['POST'])]
    public function delete(Request $request, InfoUser $infoUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$infoUser->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($infoUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_info_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
