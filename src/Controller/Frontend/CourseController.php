<?php

namespace App\Controller\Frontend;

use App\Entity\Enrollment;
use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Controller\Frontend\RedirectResponse;

#[Route('/course')]
final class CourseController extends AbstractController
{

    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route(name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('Frontend/course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, TokenStorageInterface $tokenStorage): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set the authenticated user
            $course->setUser($tokenStorage->getToken()->getUser());

            // Handle the file upload
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Failed to upload image: ' . $e->getMessage());
                    return $this->redirectToRoute('app_course_new');
                }

                // Store the new filename in the database
                $course->setImage($newFilename);
            }

            // Persist the course entity
            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index');
        }

        return $this->render('Frontend/course/new.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course, EnrollmentRepository $enrollmentRepository): Response
    {
        $user = $this->getUser();
        $isEnrolled = false;

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $isEnrolled = $enrollmentRepository->findOneBy([
                'user' => $user,
                'course' => $course,
            ]) !== null;
        }

        return $this->render('Frontend/course/show.html.twig', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Frontend/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/course/enroll/{id}', name: 'app_course_enroll', methods: ['POST'])]
    public function enroll(Course $course, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingEnrollment = $entityManager->getRepository(Enrollment::class)->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);

        if (!$existingEnrollment) {
            $enrollment = new Enrollment();
            $enrollment->setUser($user);
            $enrollment->setCourse($course);

            $entityManager->persist($enrollment);
            $entityManager->flush();

            $this->addFlash('success', 'You have successfully enrolled in the course!');
        } else {
            $this->addFlash('warning', 'You are already enrolled in this course.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }
}
