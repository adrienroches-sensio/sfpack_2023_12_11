<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route(
        path: '/hello/{name}',
        name: 'app_hello',
        methods: ['GET'],
        requirements: [
            'name' => '[a-zA-Z]+(-[a-zA-Z]+)?',
        ],
        defaults: [
            'name' => 'Adrien',
        ],
    )]
    public function index(string $name): Response
    {
        return new Response(
            content: <<<"HTML"
            <body>
                Hello {$name} !
            </body>
            HTML
        );
    }
}
