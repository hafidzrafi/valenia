<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class QrCodeTest extends TestCase
{
    public function testTicketTokenRendersAsSvg(): void
    {
        $svg = \ticket_qr_svg('019a2f4e-8f4b-7c31-9a2e-5d7f0b1c2d3e', 120);

        self::assertNotNull($svg);
        self::assertStringContainsString('<svg', $svg);
        self::assertStringContainsString('width="120"', $svg);
    }

    public function testEmptyTokenHasNoQr(): void
    {
        self::assertNull(\ticket_qr_svg(''));
        self::assertNull(\ticket_qr_png(''));
    }

    public function testTicketTokenRendersAsPng(): void
    {
        $png = \ticket_qr_png('019a2f4e-8f4b-7c31-9a2e-5d7f0b1c2d3e', 120);

        self::assertNotNull($png);
        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", $png);

        $size = unpack('Nwidth/Nheight', substr($png, 16, 8));

        self::assertIsArray($size);
        self::assertGreaterThan(0, $size['width']);
        self::assertSame($size['width'], $size['height']);
    }

    public function testPngMatchesTheMatrixUsedByTheSvg(): void
    {
        $token = '019a2f4e-8f4b-7c31-9a2e-5d7f0b1c2d3e';
        $qrCode = \ticket_qr_object($token);

        self::assertNotNull($qrCode);

        $matrix = $qrCode->getMatrix();
        $modules = $matrix->getWidth();
        $png = \ticket_qr_png($token, 220);

        self::assertNotNull($png);

        [$width, $height, $pixels] = $this->decodePng($png);
        $margin = 4;
        $scale = intdiv($width, $modules + ($margin * 2));

        self::assertGreaterThan(0, $scale);
        self::assertSame($height, $width);

        for ($y = 0; $y < $modules; ++$y) {
            for ($x = 0; $x < $modules; ++$x) {
                $pixelX = (($margin + $x) * $scale) + intdiv($scale, 2);
                $pixelY = (($margin + $y) * $scale) + intdiv($scale, 2);
                $expected = 1 === $matrix->get($x, $y) ? 0 : 255;

                self::assertSame($expected, ord($pixels[($pixelY * $width) + $pixelX]));
            }
        }
    }

    /**
     * @return array{0: int, 1: int, 2: string}
     */
    private function decodePng(string $png): array
    {
        $offset = 8;
        $idat = '';
        $width = 0;
        $height = 0;
        $length = strlen($png);

        while ($offset < $length) {
            $chunkLength = unpack('N', substr($png, $offset, 4))[1];
            $offset += 4;
            $type = substr($png, $offset, 4);
            $offset += 4;
            $data = substr($png, $offset, $chunkLength);
            $offset += $chunkLength + 4;

            if ('IHDR' === $type) {
                $header = unpack('Nwidth/Nheight', $data);
                $width = (int) $header['width'];
                $height = (int) $header['height'];
            } elseif ('IDAT' === $type) {
                $idat .= $data;
            } elseif ('IEND' === $type) {
                break;
            }
        }

        $raw = gzuncompress($idat);
        self::assertIsString($raw);

        $pixels = '';

        for ($y = 0; $y < $height; ++$y) {
            $row = substr($raw, $y * ($width + 1), $width + 1);
            self::assertSame("\x00", $row[0]);
            $pixels .= substr($row, 1);
        }

        return [$width, $height, $pixels];
    }
}
