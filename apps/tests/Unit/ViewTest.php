<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ViewTest extends TestCase
{
    public function testEscapesHtmlSpecialCharacters(): void
    {
        self::assertSame('&lt;a &quot;x&quot;&gt;', \e('<a "x">'));
    }

    public function testRenderExtractsDataAndReturnsBuffer(): void
    {
        $html = \render(
            dirname(__DIR__).'/Fixtures/partial.php',
            ['value' => '<strong>ok</strong>'],
        );

        self::assertSame('&lt;strong&gt;ok&lt;/strong&gt;', $html);
    }

    public function testViewWrapsContentInSharedLayout(): void
    {
        $html = \view('login', ['token' => 'test-token']);

        self::assertStringContainsString('<!DOCTYPE html>', $html);
        self::assertStringContainsString('name="_token" value="test-token"', $html);
        self::assertStringContainsString('Masuk', $html);
    }
}
