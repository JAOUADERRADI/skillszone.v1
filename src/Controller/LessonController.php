<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\User;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/lesson')]
final class LessonController extends AbstractController
{
    #[Route(name: 'app_lesson_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        // Get the currently logged-in user
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            // Verify that the user is the creator of the course
            $course = $lesson->getCourse();
            if ($course->getUser() !== $user) {
                $this->addFlash('danger', 'You are not allowed to add lessons to this course.');
                return $this->redirectToRoute('app_lesson_index');
            }

            // Handle the video upload
            /** @var UploadedFile $videoFile  */
            $videoFile = $form->get('video')->getData();

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    $videoFile->move(
                        $this->getParameter('videos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Failed to upload video: ' . $e->getMessage());
                    return $this->redirectToRoute('app_lesson_new');
                }

                // Store the new filename in the database
                $lesson->setVideo($newFilename);

            }

            // Persist the lesson entity
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_show', methods: ['GET'])]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
    }
}
