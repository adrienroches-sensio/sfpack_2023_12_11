<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class MovieController extends AbstractController
{
    #[Route(
        path: '/movies',
        name: 'app_movies_list',
        methods: ['GET'],
    )]
    public function list(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => $movieRepository->listAll(),
        ]);
    }

    #[Route(
        path: '/movies/{slug}',
        requirements: [
            'slug' => '\d{4}-'.Requirement::ASCII_SLUG,
        ],
        name: 'app_movies_details',
        methods: ['GET'],
    )]
    public function details(MovieRepository $movieRepository, string $slug): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => $movieRepository->getBySlug($slug),
        ]);
    }
}
