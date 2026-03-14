<?php

declare(strict_types=1);

namespace App\Domain\Product\Command;

use App\Domain\Product\Integration\Client\ProductClientInterface;
use App\Domain\Product\Service\SyncProductService;
use App\Domain\Product\Service\SyncProductServiceInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:products:sync',
    description: 'Запускает синхронизацию товаров из внешнего API',
)]
final class SyncProductsCommand extends Command
{
    public function __construct(
        private readonly ProductClientInterface $productClient,
        private readonly SyncProductServiceInterface $syncService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->makeSymfonyStyle($input, $output);
        $io->title('Запуск синхронизации товаров');

        try {
            $dtos = $this->productClient->getAllProducts();
            $this->syncService->execute($dtos);

            $io->success('Товары успешно отправлены в очередь.');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function makeSymfonyStyle(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        return new SymfonyStyle($input, $output);
    }
}
