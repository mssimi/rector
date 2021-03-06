<?php

declare(strict_types=1);

namespace Rector\Symfony\Tests\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector;

use Iterator;
use Rector\Core\Testing\PHPUnit\AbstractRectorTestCase;
use Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ChangeFileLoaderInExtensionAndKernelRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            ChangeFileLoaderInExtensionAndKernelRector::class => [
                ChangeFileLoaderInExtensionAndKernelRector::FROM => 'xml',
                ChangeFileLoaderInExtensionAndKernelRector::TO => 'yaml',
            ],
        ];
    }
}
