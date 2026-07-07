<?php
declare(strict_types=1);

/**
 * Dependency-free A4 portrait PDF renderer for equipment booking handover sheets.
 * It is intentionally small and self-contained so PDF exports keep working even
 * when Composer packages are unavailable on the CT.
 */
final class EquipmentBookingSummaryPdf
{
    private const PAGE_W = 595.28;
    private const PAGE_H = 841.89;
    private const MARGIN = 36.00;

    /** @param array<string,mixed> $booking @param list<array<string,mixed>> $items */
    public static function render(array $booking, array $items, string $groupName, string $logoPath): string
    {
        $items = array_values($items);
        $pages = self::paginate($items, trim((string)($booking['holder_name'] ?? '')) !== '');
        $totalPages = max(1, count($pages));
        $streams = [];

        foreach ($pages as $index => $page) {
            $streams[] = self::pageStream(
                $booking,
                $page['items'],
                $groupName,
                $index,
                $totalPages,
                (bool)$page['first'],
                (bool)$page['final']
            );
        }

        return self::assemble($streams, $logoPath);
    }

    /** @param list<array<string,mixed>> $items @return list<array{items:list<array<string,mixed>>,first:bool,final:bool}> */
    private static function paginate(array $items, bool $hasHolder): array
    {
        $firstCap = $hasHolder ? 7 : 8;
        $finalCap = 9;
        $middleCap = 12;

        if (count($items) <= $firstCap) {
            return [[
                'items' => $items,
                'first' => true,
                'final' => true,
            ]];
        }

        $pages = [[
            'items' => array_splice($items, 0, $firstCap),
            'first' => true,
            'final' => false,
        ]];

        while (count($items) > $finalCap) {
            $take = min($middleCap, count($items) - $finalCap);
            $pages[] = [
                'items' => array_splice($items, 0, $take),
                'first' => false,
                'final' => false,
            ];
        }

        $pages[] = [
            'items' => $items,
            'first' => false,
            'final' => true,
        ];

        return $pages;
    }

    /** @param array<string,mixed> $booking @param list<array<string,mixed>> $items */
    private static function pageStream(array $booking, array $items, string $groupName, int $pageIndex, int $totalPages, bool $isFirst, bool $isFinal): string
    {
        $out = "q\n";
        $top = self::PAGE_H - self::MARGIN;
        $contentX = self::MARGIN;
        $contentW = self::PAGE_W - (self::MARGIN * 2);
        $red = [0.929, 0.247, 0.137];
        $navy = [0.000, 0.224, 0.510];
        $ink = [0.114, 0.106, 0.102];
        $muted = [0.370, 0.353, 0.341];
        $border = [0.765, 0.745, 0.729];
        $soft = [0.957, 0.949, 0.945];

        $out .= self::fillRect($contentX, $top - 67, $contentW, 3, $red);
        $out .= self::image('Im1', $contentX, $top - 45, 112, 31);
        $out .= self::text($contentX + 122, $top - 16, self::short($groupName, 48), 7.4, 'F2', $red);
        $out .= self::text($contentX + 122, $top - 36, $isFirst ? 'Equipment Booking Summary' : 'Equipment Booking - continued', $isFirst ? 16.5 : 14.2, 'F2', $ink);
        $out .= self::text($contentX + 122, $top - 51, 'Kit issue and return checklist', 8.3, 'F1', $muted);
        $out .= self::rightText($contentX + $contentW, $top - 16, 'BOOKING REFERENCE', 6.8, 'F2', $muted);
        $out .= self::rightText($contentX + $contentW, $top - 35, self::short((string)($booking['reference'] ?? ''), 28), 10.2, 'F2', $navy);

        $tableTop = $top - 92;
        if ($isFirst) {
            $metaY = $top - 153;
            $out .= self::metaGrid($booking, $contentX, $metaY, $contentW, $border, $soft, $ink, $muted);
            $tableTop = $metaY - 14;
            $holder = trim((string)($booking['holder_name'] ?? ''));
            if ($holder !== '') {
                $holderY = $tableTop - 27;
                $out .= self::fillStrokeRect($contentX, $holderY, $contentW, 21, [0.976, 0.972, 0.969], $border, 0.7);
                $out .= self::text($contentX + 7, $holderY + 12.5, 'EQUIPMENT CURRENTLY ISSUED TO', 6.6, 'F2', $muted);
                $holderText = $holder;
                $holderEmail = trim((string)($booking['holder_email'] ?? ''));
                if ($holderEmail !== '') {
                    $holderText .= ' - ' . $holderEmail;
                }
                $out .= self::text($contentX + 166, $holderY + 11.8, self::short($holderText, 62), 8.4, 'F2', $ink);
                $tableTop = $holderY - 14;
            }
        }

        $out .= self::tableHeader($contentX, $tableTop, $contentW, $border, $soft, $ink);
        $rowY = $tableTop - 57;
        foreach ($items as $item) {
            $out .= self::tableRow($item, $contentX, $rowY, $contentW, $border, $ink, $muted);
            $rowY -= 37;
        }

        if ($isFinal) {
            $notesTop = max(142.0, $rowY - 10);
            $out .= self::notesAndSignatures($contentX, $notesTop, $contentW, $border, $ink, $muted);
        }

        $pageLabel = 'Page ' . ($pageIndex + 1) . ' of ' . $totalPages;
        $out .= self::line($contentX, 30, $contentX + $contentW, 30, 0.6, $border);
        $out .= self::text($contentX, 18, 'Please check each item before it leaves and again on return. Report missing, damaged or unsafe equipment through the Hut Management System.', 6.8, 'F1', $muted);
        $out .= self::rightText($contentX + $contentW, 18, $pageLabel, 6.8, 'F1', $muted);
        return $out . "Q\n";
    }

    /** @param array<string,mixed> $booking */
    private static function metaGrid(array $booking, float $x, float $y, float $w, array $border, array $soft, array $ink, array $muted): string
    {
        $h = 55.0;
        $half = $w / 2;
        $row = $h / 3;
        $printed = date('d M Y, H:i');
        $event = self::short((string)($booking['title'] ?? ''), 45);
        $status = self::short((string)($booking['status'] ?? ''), 28);
        $requester = self::short((string)($booking['requester_name'] ?? ''), 42);
        $starts = self::formatDate((string)($booking['starts_at'] ?? ''));
        $ends = self::formatDate((string)($booking['ends_at'] ?? ''));

        $out = self::fillStrokeRect($x, $y, $w, $h, [1, 1, 1], $border, 0.7);
        $out .= self::fillRect($x, $y + $h - $row, $w, $row, $soft);
        $out .= self::line($x + $half, $y, $x + $half, $y + $h, 0.6, $border);
        $out .= self::line($x, $y + $row, $x + $w, $y + $row, 0.6, $border);
        $out .= self::line($x, $y + ($row * 2), $x + $w, $y + ($row * 2), 0.6, $border);

        $cells = [
            [$x + 7, $y + $h - 10, 'EVENT', $event],
            [$x + $half + 7, $y + $h - 10, 'STATUS', $status],
            [$x + 7, $y + $row + 8, 'BOOKING USER', $requester],
            [$x + $half + 7, $y + $row + 8, 'DATE PRINTED', $printed],
            [$x + 7, $y + 8, 'COLLECTION FROM', $starts],
            [$x + $half + 7, $y + 8, 'RETURN BY', $ends],
        ];
        foreach ($cells as [$cx, $cy, $label, $value]) {
            $out .= self::text($cx, $cy, $label, 6.2, 'F2', $muted);
            $out .= self::text($cx, $cy - 8.8, self::short($value, 42), 8.2, 'F2', $ink);
        }
        return $out;
    }

    private static function tableHeader(float $x, float $y, float $w, array $border, array $soft, array $ink): string
    {
        $out = self::fillStrokeRect($x, $y - 20, $w, 20, $soft, $border, 0.8);
        foreach (self::columns($x) as $column) {
            if ($column['x'] !== $x) {
                $out .= self::line($column['x'], $y - 20, $column['x'], $y, 0.6, $border);
            }
        }
        $out .= self::centerText($x + 14, $y - 12.5, 'CHECK', 6.4, 'F2', $ink);
        $out .= self::text($x + 32, $y - 12.5, 'ASSET ID', 6.4, 'F2', $ink);
        $out .= self::text($x + 112, $y - 12.5, 'EQUIPMENT ITEM', 6.4, 'F2', $ink);
        $out .= self::centerText($x + 394, $y - 12.5, 'QTY', 6.4, 'F2', $ink);
        $out .= self::text($x + 420, $y - 12.5, 'STATUS / CONDITION OUT', 6.4, 'F2', $ink);
        return $out;
    }

    /** @param array<string,mixed> $item */
    private static function tableRow(array $item, float $x, float $y, float $w, array $border, array $ink, array $muted): string
    {
        $h = 37.0;
        $out = self::strokeRect($x, $y, $w, $h, $border, 0.6);
        foreach (self::columns($x) as $column) {
            if ($column['x'] !== $x) {
                $out .= self::line($column['x'], $y, $column['x'], $y + $h, 0.6, $border);
            }
        }
        $out .= self::strokeRect($x + 9, $y + 12, 10, 10, $ink, 0.9);
        $asset = self::short((string)($item['asset_id'] ?? ''), 18);
        $name = self::short((string)($item['name'] ?? ''), 46);
        $category = self::short((string)($item['category'] ?? ''), 48);
        $condition = trim((string)($item['condition_out'] ?? ''));
        if ($condition === '') {
            $condition = (($item['quantity_issued'] ?? 0) > 0) ? 'Not recorded' : 'To confirm at issue';
        }
        $quantity = (int)($item['quantity_issued'] ?? 0);
        if ($quantity <= 0) { $quantity = (int)($item['quantity_approved'] ?? 0); }
        if ($quantity <= 0) { $quantity = (int)($item['quantity_requested'] ?? 0); }

        $out .= self::text($x + 32, $y + 21, $asset, 7.7, 'F2', [0.000, 0.224, 0.510]);
        $out .= self::text($x + 112, $y + 22, $name, 8.2, 'F2', $ink);
        if ($category !== '') {
            $out .= self::text($x + 112, $y + 11, $category, 6.8, 'F1', $muted);
        }
        $out .= self::centerText($x + 394, $y + 18, (string)$quantity, 9.4, 'F2', $ink);
        $out .= self::text($x + 420, $y + 20, self::short($condition, 22), 7.7, 'F2', $ink);
        $out .= self::line($x + 420, $y + 10, $x + $w - 8, $y + 10, 0.5, $border, [3, 2]);
        return $out;
    }

    private static function notesAndSignatures(float $x, float $top, float $w, array $border, array $ink, array $muted): string
    {
        $leftW = ($w - 10) / 2;
        $rightX = $x + $leftW + 10;
        $out = self::text($x, $top, 'ISSUE NOTES', 6.5, 'F2', $muted);
        $out .= self::text($rightX, $top, 'RETURN NOTES / DAMAGE', 6.5, 'F2', $muted);
        $out .= self::strokeRect($x, $top - 58, $leftW, 51, $border, 0.7);
        $out .= self::strokeRect($rightX, $top - 58, $leftW, 51, $border, 0.7);
        $sigY = $top - 87;
        $sigW = ($w - 20) / 3;
        foreach ([['ISSUED BY', $x], ['RECEIVED BY', $x + $sigW + 10], ['RETURNED TO', $x + (($sigW + 10) * 2)]] as [$label, $sx]) {
            $out .= self::text($sx, $sigY, $label, 6.5, 'F2', $muted);
            $out .= self::line($sx, $sigY - 17, $sx + $sigW, $sigY - 17, 0.7, $ink);
        }
        return $out;
    }

    /** @return list<array{x:float}> */
    private static function columns(float $x): array
    {
        return [
            ['x' => $x],
            ['x' => $x + 28],
            ['x' => $x + 108],
            ['x' => $x + 377],
            ['x' => $x + 412],
        ];
    }

    /** @param list<string> $streams */
    private static function assemble(array $streams, string $logoPath): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $imageId = 5;
        $jpeg = self::jpegObject($logoPath);
        if ($jpeg === null) {
            $imageId = 0;
            $nextId = 5;
        } else {
            $objects[$imageId] = $jpeg;
            $nextId = 6;
        }

        $pageIds = [];
        foreach ($streams as $stream) {
            $contentId = $nextId++;
            $pageId = $nextId++;
            $objects[$contentId] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
            $resources = '<< /Font << /F1 3 0 R /F2 4 0 R >>';
            if ($imageId > 0) {
                $resources .= ' /XObject << /Im1 ' . $imageId . ' 0 R >>';
            }
            $resources .= ' >>';
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::fmt(self::PAGE_W) . ' ' . self::fmt(self::PAGE_H) . '] /Resources ' . $resources . ' /Contents ' . $contentId . ' 0 R >>';
            $pageIds[] = $pageId;
        }
        $kids = implode(' ', array_map(static fn(int $id): string => $id . ' 0 R', $pageIds));
        $objects[2] = '<< /Type /Pages /Count ' . count($pageIds) . ' /Kids [ ' . $kids . ' ] >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $max = max(array_keys($objects));
        $pdf .= 'xref' . "\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
        for ($id = 1; $id <= $max; $id++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id] ?? 0) . "\n";
        }
        $pdf .= 'trailer' . "\n<< /Size " . ($max + 1) . ' /Root 1 0 R >>' . "\nstartxref\n" . $xref . "\n%%EOF\n";
        return $pdf;
    }

    private static function jpegObject(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path) || !function_exists('getimagesize')) {
            return null;
        }
        $info = @getimagesize($path);
        $bytes = @file_get_contents($path);
        if ($info === false || $bytes === false || ($info[2] ?? 0) !== IMAGETYPE_JPEG) {
            return null;
        }
        $width = (int)($info[0] ?? 0);
        $height = (int)($info[1] ?? 0);
        if ($width <= 0 || $height <= 0) {
            return null;
        }
        return '<< /Type /XObject /Subtype /Image /Width ' . $width . ' /Height ' . $height . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($bytes) . " >>\nstream\n" . $bytes . "\nendstream";
    }

    private static function text(float $x, float $y, string $text, float $size, string $font, array $colour): string
    {
        return sprintf("BT /%s %s Tf %s %s %s rg 1 0 0 1 %s %s Tm (%s) Tj ET\n", $font, self::fmt($size), self::fmt($colour[0]), self::fmt($colour[1]), self::fmt($colour[2]), self::fmt($x), self::fmt($y), self::escape($text));
    }

    private static function rightText(float $right, float $y, string $text, float $size, string $font, array $colour): string
    {
        return self::text($right - self::width($text, $size), $y, $text, $size, $font, $colour);
    }

    private static function centerText(float $center, float $y, string $text, float $size, string $font, array $colour): string
    {
        return self::text($center - (self::width($text, $size) / 2), $y, $text, $size, $font, $colour);
    }

    private static function width(string $text, float $size): float
    {
        return strlen(self::latin($text)) * $size * 0.51;
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], self::latin($text));
    }

    private static function latin(string $value): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value);
        return $converted === false ? preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '' : $converted;
    }

    private static function short(string $value, int $max): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');
        if (strlen(self::latin($value)) <= $max) {
            return $value;
        }
        $cut = substr(self::latin($value), 0, max(1, $max - 3));
        return rtrim($cut) . '...';
    }

    private static function formatDate(string $value): string
    {
        if ($value === '') { return '-'; }
        $stamp = strtotime($value);
        return $stamp === false ? self::short($value, 30) : date('d M Y, H:i', $stamp);
    }

    private static function fmt(float $number): string
    {
        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }

    private static function fillRect(float $x, float $y, float $w, float $h, array $colour): string
    {
        return sprintf("%s %s %s rg %s %s %s %s re f\n", self::fmt($colour[0]), self::fmt($colour[1]), self::fmt($colour[2]), self::fmt($x), self::fmt($y), self::fmt($w), self::fmt($h));
    }

    private static function strokeRect(float $x, float $y, float $w, float $h, array $colour, float $lineWidth): string
    {
        return sprintf("%s %s %s RG %s w %s %s %s %s re S\n", self::fmt($colour[0]), self::fmt($colour[1]), self::fmt($colour[2]), self::fmt($lineWidth), self::fmt($x), self::fmt($y), self::fmt($w), self::fmt($h));
    }

    private static function fillStrokeRect(float $x, float $y, float $w, float $h, array $fill, array $stroke, float $lineWidth): string
    {
        return sprintf("%s %s %s rg %s %s %s RG %s w %s %s %s %s re B\n", self::fmt($fill[0]), self::fmt($fill[1]), self::fmt($fill[2]), self::fmt($stroke[0]), self::fmt($stroke[1]), self::fmt($stroke[2]), self::fmt($lineWidth), self::fmt($x), self::fmt($y), self::fmt($w), self::fmt($h));
    }

    private static function line(float $x1, float $y1, float $x2, float $y2, float $width, array $colour, ?array $dash = null): string
    {
        $dashCommand = $dash === null ? '[] 0 d' : '[' . implode(' ', array_map([self::class, 'fmt'], $dash)) . '] 0 d';
        return sprintf("%s\n%s %s %s RG %s w %s %s m %s %s l S [] 0 d\n", $dashCommand, self::fmt($colour[0]), self::fmt($colour[1]), self::fmt($colour[2]), self::fmt($width), self::fmt($x1), self::fmt($y1), self::fmt($x2), self::fmt($y2));
    }

    private static function image(string $name, float $x, float $y, float $w, float $h): string
    {
        return sprintf("q %s 0 0 %s %s %s cm /%s Do Q\n", self::fmt($w), self::fmt($h), self::fmt($x), self::fmt($y), $name);
    }
}
