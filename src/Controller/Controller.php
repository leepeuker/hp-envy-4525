<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\HpEnvyApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function scan(Request $request) : Response
    {
        $fileFormat = $request->query->get('format', 'pdf');
        $file = $this->hpEnvyApi->scanRequest($fileFormat);

        $target = '/scans-finished/' . basename($file);

        $this->logger->debug('Move scanned file to target directory', ['targetFile' => $target]);
        rename($file, $target);

        return new Response('Done');
    }

    #[Route('/')]
    public function dashboard() : Response
    {
        return $this->render('dashboard.html.twig');
    }
}
