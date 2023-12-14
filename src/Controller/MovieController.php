<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Event\MovieCreatedEvent;
use App\Form\MovieType;
use App\Model\Movie;
use App\Model\Security;
use App\Omdb\Client\ApiConsumerInterface;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly ApiConsumerInterface $omdbApiConsumer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ClockInterface $clock,
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
        path: '/movies/{slug}',
        requirements: [
            'slug' => Movie::SLUG_FORMAT,
        ],
        name: 'app_movies_details',
        methods: ['GET'],
    )]
    public function detailsFromDatabase(MovieRepository $movieRepository, string $slug): Response
    {
        $movie = Movie::fromEntity($movieRepository->getBySlug($slug));

        $this->denyAccessUnlessGranted(Security::MOVIE_VIEW_DETAILS, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
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
        $movie = Movie::fromOmdb($this->omdbApiConsumer->getByImdbId($imdbId));

        $this->denyAccessUnlessGranted(Security::MOVIE_VIEW_DETAILS, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
            'can_edit' => false,
        ]);
    }

    #[Route(
        path: '/admin/movies/new',
        name: 'app_movies_new',
        methods: ['GET', 'POST'],
    )]
    #[Route(
        '/admin/movies/{slug}/edit',
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

        $editingMovie = null !== $slug ? Movie::fromEntity($movieEntity) : null;

        if ($movieForm->isSubmitted() && $movieForm->isValid()) {
            $entityManager->persist($movieEntity);
            $entityManager->flush();

            if (null === $editingMovie) {
                $this->eventDispatcher->dispatch(new MovieCreatedEvent(
                    movie: $movieEntity,
                    user: $this->getUser(),
                    at: $this->clock->now(),
                ));
            }

            return $this->redirectToRoute('app_movies_details', ['slug' => $movieEntity->getSlug()]);
        }

        return $this->render('movie/new_or_edit.html.twig', [
            'movie_form' => $movieForm->createView(),
            'editing_movie' => $editingMovie,
        ]);
    }
}
