<?php declare(strict_types=1);

namespace Rector\Tests\Rector\Dynamic\MethodNameReplacerRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class MethodNameReplacerRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideWrongToFixedFiles()
     */
    public function test(string $wrong, string $fixed): void
    {
        $this->doTestFileMatchesExpectedContent($wrong, $fixed);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedFiles(): array
    {
        return [
            [__DIR__ . '/Wrong/wrong.php.inc', __DIR__ . '/Correct/correct.php.inc'],
            [__DIR__ . '/Wrong/wrong2.php.inc', __DIR__ . '/Correct/correct2.php.inc'],
            [__DIR__ . '/Wrong/wrong3.php.inc', __DIR__ . '/Correct/correct3.php.inc'],
            [__DIR__ . '/Wrong/wrong4.php.inc', __DIR__ . '/Correct/correct4.php.inc'],
            [__DIR__ . '/Wrong/wrong5.php.inc', __DIR__ . '/Correct/correct5.php.inc'],
            [__DIR__ . '/Wrong/wrong6.php.inc', __DIR__ . '/Correct/correct6.php.inc'],
            [__DIR__ . '/Wrong/wrong7.php.inc', __DIR__ . '/Correct/correct7.php.inc'],
            [__DIR__ . '/Wrong/SomeClass.php', __DIR__ . '/Correct/SomeClass.php'],
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
