<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\CourseCategory;
use App\Entity\Course;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $courses = $entityManager->getRepository(Course::class)->findAll();
        $categories = $entityManager->getRepository(CourseCategory::class)->findAll();

        return $this->render('Frontend/home/index.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
        ]);
    }
}
