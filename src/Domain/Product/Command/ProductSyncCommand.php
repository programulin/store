<?php

declare(strict_types=1);

namespace App\Domain\Product\Command;

use App\Domain\Product\Service\ProductSync\ProductSyncStarter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:product:sync',
    description: 'Запускает синхронизацию товаров с внешним API',
)]
final class ProductSyncCommand extends Command
{
    public function __construct(
        private readonly ProductSyncStarter $productSyncStarter,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->makeSymfonyStyle($input, $output);

        try {
            $this->productSyncStarter->execute();

            $io->success('Синхронизация товаров успешно запущена.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $io->error(sprintf('Ошибка: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }

    private function makeSymfonyStyle(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        return new SymfonyStyle($input, $output);
    }
}
