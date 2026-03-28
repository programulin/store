<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\Command;

use App\Domain\Product\Integration\Client\ProductClientInterface;
use App\Domain\Product\Service\SyncProductServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SyncProductsCommandTest extends KernelTestCase
{
    public function testExecuteSuccess(): void
    {
        // TODO Разобраться с self::bootKernel, когда нужен?

        $clientMock = $this->createMock(ProductClientInterface::class);
        $clientMock->expects($this->once())
            ->method('getLatestSnapshot')
            ->willReturn([]);

        $syncServiceMock = $this->createMock(SyncProductServiceInterface::class);
        $syncServiceMock->expects($this->once())
            ->method('execute');

        $container = self::getContainer();
        $container->set(ProductClientInterface::class, $clientMock);
        $container->set(SyncProductServiceInterface::class, $syncServiceMock);

        $application = new Application(self::$kernel);
        $command = $application->find('app:products:sync');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Запуск синхронизации товаров', $commandTester->getDisplay());
        $this->assertStringContainsString('Товары успешно отправлены в очередь', $commandTester->getDisplay());
    }

    public function testExecuteFailure(): void
    {
        $clientMock = $this->createMock(ProductClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('getLatestSnapshot')
            ->willThrowException(new Exception('API Offline'));

        self::getContainer()->set(ProductClientInterface::class, $clientMock);

        $application = new Application(self::$kernel);
        $command = $application->find('app:products:sync');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Ошибка: API Offline', $commandTester->getDisplay());
    }
}
