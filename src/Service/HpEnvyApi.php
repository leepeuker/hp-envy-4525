<?php declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HpEnvyApi
{
    private const REQUEST_HEADERS = [
        'Accept' => '*/*',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'en,en-US;q=0.7,de;q=0.3',
        'Content-Type' => 'text/xml',
        'Sec-GPC' => '1',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly string $tmpDirectory,
    ) {
    }

    public function scanRequest() : string
    {
        $response = $this->client->request(
            'POST',
            'http://printer.home/eSCL/ScanJobs', [
            'headers' => self::REQUEST_HEADERS,
            'body' => '<scan:ScanSettings xmlns:scan="http://schemas.hp.com/imaging/escl/2011/05/03"
                           xmlns:pwg="http://www.pwg.org/schemas/2010/12/sm">
                        <pwg:Version>2.1</pwg:Version>
                        <scan:Intent>Document</scan:Intent>
                        <pwg:ScanRegions>
                            <pwg:ScanRegion>
                                <pwg:Height>3507</pwg:Height>
                                <pwg:Width>2481</pwg:Width>
                                <pwg:XOffset>0</pwg:XOffset>
                                <pwg:YOffset>0</pwg:YOffset>
                            </pwg:ScanRegion>
                        </pwg:ScanRegions>
                        <pwg:InputSource>Platen</pwg:InputSource>
                        <scan:DocumentFormatExt>application/pdf</scan:DocumentFormatExt>
                        <scan:XResolution>300</scan:XResolution>
                        <scan:YResolution>300</scan:YResolution>
                        <scan:ColorMode>RGB24</scan:ColorMode>
                        <scan:CompressionFactor>15</scan:CompressionFactor>
                        <scan:Brightness>1000</scan:Brightness>
                        <scan:Contrast>1000</scan:Contrast>
                    </scan:ScanSettings>',
        ],
        );

        $this->ensureValidStatusCode($response);

        $headers = $response->getHeaders();
        if (empty($headers['location'][0]) === true) {
            throw new \RuntimeException('Missing location header in response');
        }

        $this->logger->debug('Successfully started scan', ['locationUrl' => $headers['location'][0]]);

        sleep(5);

        $filename = $this->tmpDirectory . '/test.pdf';

        $resource = fopen($headers['location'][0] . '/NextDocument', 'r');
        if ($resource === false) {
            throw new \RuntimeException('Could not open scanned document stream');
        }

        file_put_contents($filename, stream_get_contents($resource));
        fclose($resource);

        return $filename;
    }

    private function ensureValidStatusCode(ResponseInterface $response) : void
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 201) {
            throw new \RuntimeException('Invalid response status code: ' . $statusCode);
        }
    }
}
