<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\HpEnvyApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly HpEnvyApi $hpEnvyApi,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/scan')]
    public function scan() : Response
    {
        $file = $this->hpEnvyApi->scanRequest();

        $target = '/paperless-consumption/' . basename($file);

        $this->logger->debug('Move scanned file to target directory', ['targetFile' => $target]);
        rename($file, $target);

        return new Response('Done');
    }
}
