<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\HpEnvyApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly HpEnvyApi $hpEnvyApi,
    ) {
    }

    #[Route('/scan')]
    public function scan() : Response
    {
        $this->hpEnvyApi->scanRequest();

        return new Response('Done');
    }
}
