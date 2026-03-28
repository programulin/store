<?php

declare(strict_types=1);

namespace App\Domain\Product\Service\ProductSnapshot;

use App\Domain\Product\Integration\Client\ProductClientInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class SnapshotDownloader
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Target('monolog.logger.product_snapshot')]
        private LoggerInterface $snapshotLogger,
        private ProductClientInterface $api,
    ) {}

    public function execute(): string
    {
        // TODO мб лучше иметь сущность Snapshot и возвращать её

        $url = $this->api->getLatestSnapshotUrl();
        $path = $this->makeNewFile();
        $fileHandler = fopen($path, 'w');

        if ($fileHandler === false) {
            throw new Exception('Unable to open temporary file: ' . $path);
        }

        try {
            $response = $this->httpClient->request('GET', $url, [
                'buffer' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('Unexpected HTTP status code ' . $response->getStatusCode());
            }

            foreach ($this->httpClient->stream($response) as $chunk) {
                $result = fwrite($fileHandler, $chunk->getContent());

                if ($result === false) {
                    throw new Exception('Unable to write to file: ' . $path);
                }
            }
        } catch (Throwable $e) {
            $this->snapshotLogger->error('Error on downloading products snapshot', [
                'exception' => $e,
                'response' => $response ?? null,
            ]);

            throw $e;
        } finally {
            if (isset($fileHandler)) {
                fclose($fileHandler);
            }
        }

        return $path;
    }

    private function makeNewFile(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'product_snapshot_');

        if ($path === false) {
            throw new Exception('Cant create temporary file');
        }

        // TODO полноценное удаление временных файлов + мб хранение в /var
        register_shutdown_function(function() use ($path) {
            if (file_exists($path)) {
                unlink($path);
            }
        });

        return $path;
    }
}
