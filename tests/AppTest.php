<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppTest extends KernelTestCase
{
    public function testApplicationIsRunning()
    {
        $this->assertTrue(true);
    }

    public function testDatabaseConnection()
    {
        $conn = $this->getContainer()
            ->get('doctrine')
            ->getConnection();

        $result = $conn->fetchOne('SELECT 1');

        $this->assertEquals(1, $result);
    }
}
