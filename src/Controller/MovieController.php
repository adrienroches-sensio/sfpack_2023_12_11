<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Form\MovieType;
use App\Model\MagicMovieRepository;
use App\Model\Movie;
use App\Omdb\Client\ApiConsumerInterface;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly ApiConsumerInterface $omdbApiConsumer,
        private readonly MagicMovieRepository $magicMovieRepository,
    ) {
    }

    #[Route(
        path: '/movies',
        name: 'app_movies_list',
        methods: ['GET'],
    )]
    public function list(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => Movie::fromEntities($movieRepository->listAll()),
        ]);
    }

    #[Route(
        path: '/movies/{type}/{identifier}',
        requirements: [
            'type' => 'omdb|doctrine|database',
        ],
        name: 'app_movies_details_magic',
        methods: ['GET'],
    )]
    public function magicDetails(string $type, string $identifier): Response
    {
        dd($this->magicMovieRepository->get($type, $identifier));
    }

    #[Route(
        path: '/movies/{slug}',
        requirements: [
            'slug' => Movie::SLUG_FORMAT,
        ],
        name: 'app_movies_details',
        methods: ['GET'],
    )]
    public function detailsFromDatabase(MovieRepository $movieRepository, string $slug): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => Movie::fromEntity($movieRepository->getBySlug($slug)),
            'can_edit' => true,
        ]);
    }

    #[Route(
        path: '/movies/imdb-{imdbId}',
        requirements: [
            'imdbId' => 'tt.{1,50}',
        ],
        name: 'app_movies_details_omdb',
        methods: ['GET'],
    )]
    public function detailsFromOmdb(string $imdbId): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => Movie::fromOmdb($this->omdbApiConsumer->getByImdbId($imdbId)),
            'can_edit' => false,
        ]);
    }

    #[Route(
        path: '/movies/new',
        name: 'app_movies_new',
        methods: ['GET', 'POST'],
    )]
    #[Route(
        '/movies/{slug}/edit',
        name: 'app_movies_edit',
        requirements: [
            'slug' => Movie::SLUG_FORMAT,
        ],
        methods: ['GET', 'POST']
    )]
    public function newOrEdit(
        Request $request,
        string|null $slug = null,
        MovieRepository $movieRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $movieEntity = new MovieEntity();
        if (null !== $slug) {
            $movieEntity = $movieRepository->getBySlug($slug);
        }

        $movieForm = $this->createForm(MovieType::class, $movieEntity);
        $movieForm->handleRequest($request);

        if ($movieForm->isSubmitted() && $movieForm->isValid()) {
            $entityManager->persist($movieEntity);
            $entityManager->flush();

            return $this->redirectToRoute('app_movies_details', ['slug' => $movieEntity->getSlug()]);
        }

        return $this->render('movie/new_or_edit.html.twig', [
            'movie_form' => $movieForm->createView(),
            'editing_movie' => null !== $slug ? Movie::fromEntity($movieEntity) : null,
        ]);
    }
}
