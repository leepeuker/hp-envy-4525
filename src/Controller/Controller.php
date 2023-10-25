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
        private readonly array $scanTargets,
    ) {
    }

    #[Route('/')]
    public function dashboard() : Response
    {
        return $this->render('dashboard.html.twig', ['scanTargets' => $this->scanTargets]);
    }

    #[Route('/scan')]
    public function scan(Request $request) : Response
    {
        $fileFormat = $request->query->get('format', 'pdf');
        $scanTarget = $request->query->get('target', 'paperless');
        $file = $this->hpEnvyApi->scanRequest($fileFormat);

        $target = '/scans-finished/' . $this->scanTargets[$scanTarget - 1] . '/' . basename($file);

        $this->logger->debug('Move scanned file to target directory', ['targetFile' => $target]);

        if (file_exists(dirname($target)) === false) {
            mkdir(dirname($target), 0777, true);
        }

        rename($file, $target);

        return new Response('Done');
    }
}
