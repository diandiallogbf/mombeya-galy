<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Generates branded SVG placeholder visuals (product silhouettes, banners…)
 * so the shop looks complete before real photos are uploaded from the admin.
 */
class PlaceholderArt
{
    public const CRIMSON = '#E0144F';

    public const BLUE = '#3FA9F5';

    public const NAVY = '#0B2A4A';

    /** Soft backgrounds paired with a strong garment colour. */
    private const PALETTES = [
        ['#FDEBF0', '#F7D3DE', '#E0144F'],
        ['#E8F4FD', '#D2E9FA', '#0B6CB5'],
        ['#F4F1EA', '#E8E1D2', '#8A6A3B'],
        ['#EEF2F6', '#DCE3EC', '#0B2A4A'],
        ['#FFF4E5', '#FCE3C3', '#C46A0B'],
        ['#EAF7F1', '#D2EEE1', '#1E7D55'],
        ['#F3EEFB', '#E3D8F5', '#5B3A9B'],
        ['#FBEFEF', '#F3D9D9', '#7A1F2B'],
    ];

    public static function disk()
    {
        return Storage::disk('public');
    }

    /**
     * Product picture (600x900). Returns the stored path.
     */
    public static function product(string $shape, int $variant, string $path): string
    {
        [$bg1, $bg2, $main] = self::PALETTES[$variant % count(self::PALETTES)];
        $accent = $variant % 2 ? self::BLUE : '#F5C542';
        $art = self::shape($shape, $main, $accent);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 900" width="600" height="900">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{$bg1}"/><stop offset="1" stop-color="{$bg2}"/>
    </linearGradient>
    <pattern id="tri" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M0 40 L20 10 L40 40 Z" fill="{$main}" opacity=".07"/>
    </pattern>
  </defs>
  <rect width="600" height="900" fill="url(#bg)"/>
  <rect y="800" width="600" height="100" fill="url(#tri)"/>
  <ellipse cx="300" cy="790" rx="210" ry="18" fill="#000" opacity=".06"/>
  {$art}
  <text x="300" y="865" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="20" letter-spacing="6" fill="{$main}" opacity=".55" font-weight="700">MOMBEYA GALY</text>
</svg>
SVG;
        self::disk()->put($path, $svg);

        return $path;
    }

    /**
     * Category tile (480x600) with a strong background and a white silhouette.
     */
    public static function category(string $shape, int $variant, string $path): string
    {
        $colors = [self::CRIMSON, '#0B6CB5', '#8A6A3B', self::NAVY, '#C46A0B', '#1E7D55', '#5B3A9B', '#7A1F2B', '#2C7DA0', '#B5179E'];
        $c1 = $colors[$variant % count($colors)];
        $art = self::shape($shape, '#FFFFFF', 'rgba(255,255,255,.55)');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 600" width="480" height="600">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c1}" stop-opacity=".78"/>
    </linearGradient>
    <pattern id="p" width="48" height="48" patternUnits="userSpaceOnUse">
      <path d="M24 4 L44 24 L24 44 L4 24 Z" fill="none" stroke="#fff" stroke-opacity=".12" stroke-width="2"/>
    </pattern>
  </defs>
  <rect width="480" height="600" fill="url(#g)"/>
  <rect width="480" height="600" fill="url(#p)"/>
  <g transform="translate(36 -18) scale(.68)" opacity=".95">{$art}</g>
</svg>
SVG;
        self::disk()->put($path, $svg);

        return $path;
    }

    /**
     * Home hero background (1900x900): brand gradient, pattern and silhouettes on the right.
     */
    public static function hero(int $variant, array $shapes, string $path): string
    {
        $gradients = [
            [self::CRIMSON, '#8E0B33'],
            ['#0B6CB5', self::NAVY],
            ['#1A1A2E', '#0B2A4A'],
            ['#8A6A3B', '#4A3415'],
        ];
        [$c1, $c2] = $gradients[$variant % count($gradients)];
        $items = '';
        foreach (array_values($shapes) as $i => $shape) {
            $x = 940 + $i * 390;
            $y = 10 + ($i % 2) * 50;
            $items .= '<g transform="translate('.$x.' '.$y.') scale(.92)" opacity="'.(0.95 - $i * 0.15).'">'.self::shape($shape, '#FFFFFF', 'rgba(255,255,255,.5)').'</g>';
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1900 900" width="1900" height="900" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c2}"/>
    </linearGradient>
    <pattern id="p" width="80" height="80" patternUnits="userSpaceOnUse">
      <path d="M0 80 L40 20 L80 80 Z" fill="#fff" opacity=".05"/>
      <circle cx="40" cy="10" r="3" fill="#fff" opacity=".12"/>
    </pattern>
  </defs>
  <rect width="1900" height="900" fill="url(#g)"/>
  <rect width="1900" height="900" fill="url(#p)"/>
  <circle cx="1450" cy="450" r="420" fill="#fff" opacity=".06"/>
  <circle cx="1450" cy="450" r="320" fill="#fff" opacity=".05"/>
  {$items}
</svg>
SVG;
        self::disk()->put($path, $svg);

        return $path;
    }

    /**
     * Illustrated showroom cover, 800x1000.
     */
    public static function cover(string $kind, int $variant, string $path): string
    {
        $colors = [self::CRIMSON, '#0B6CB5', self::NAVY, '#1E7D55', '#8A6A3B', '#5B3A9B', '#C46A0B', '#7A1F2B'];
        $c = $colors[$variant % count($colors)];
        $art = match ($kind) {
            'store' => self::storeArt($c),
            default => self::storeArt($c),
        };

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000" width="800" height="1000">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#F7F9FC"/><stop offset="1" stop-color="#E6ECF3"/>
    </linearGradient>
  </defs>
  <rect width="800" height="1000" fill="url(#g)"/>
  {$art}
</svg>
SVG;
        self::disk()->put($path, $svg);

        return $path;
    }

    private static function storeArt(string $c): string
    {
        $stripes = '';
        for ($i = 0; $i < 8; $i++) {
            $fill = $i % 2 ? '#FFFFFF' : $c;
            $stripes .= '<path d="M'.(110 + $i * 72.5).' 300 L'.(182.5 + $i * 72.5).' 300 L'.(182.5 + $i * 72.5).' 370 Q'.(146 + $i * 72.5).' 400 '.(110 + $i * 72.5).' 370 Z" fill="'.$fill.'"/>';
        }

        return <<<SVG
  <rect x="0" y="860" width="800" height="140" fill="#D5DDE7"/>
  <rect x="120" y="230" width="560" height="640" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="4"/>
  <rect x="120" y="200" width="560" height="100" fill="{$c}"/>
  <text x="400" y="265" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="40" font-weight="700" letter-spacing="8" fill="#FFFFFF">MOMBEYA GALY</text>
  {$stripes}
  <rect x="160" y="450" width="220" height="260" rx="6" fill="#DCEBF8" stroke="#9FB4C8" stroke-width="4"/>
  <path d="M200 700 L200 560 Q230 520 260 560 L260 700 Z" fill="{$c}" opacity=".7"/>
  <path d="M290 700 L290 540 Q320 500 350 540 L350 700 Z" fill="#0B6CB5" opacity=".6"/>
  <rect x="440" y="450" width="200" height="420" rx="6" fill="#B9CCDD" stroke="#9FB4C8" stroke-width="4"/>
  <circle cx="610" cy="670" r="10" fill="#6B7C8F"/>
  <rect x="140" y="860" width="520" height="20" fill="#AFBCCB"/>
SVG;
    }

    /**
     * SVG fragment of a garment / accessory silhouette, drawn in a 600x900 box.
     */
    public static function shape(string $shape, string $main, string $accent): string
    {
        return match ($shape) {
            'boubou' => <<<SVG
  <path d="M262 170 Q300 205 338 170 L385 176 L548 300 L566 525 L470 545 L470 770 L130 770 L130 545 L34 525 L52 300 L215 176 Z" fill="{$main}"/>
  <path d="M262 170 Q300 205 338 170 L338 182 Q300 240 262 182 Z" fill="{$accent}"/>
  <path d="M300 205 L300 470" stroke="{$accent}" stroke-width="6" stroke-dasharray="14 10"/>
  <path d="M240 200 Q300 330 360 200" fill="none" stroke="{$accent}" stroke-width="6"/>
  <path d="M130 740 L470 740" stroke="{$accent}" stroke-width="5" opacity=".7"/>
SVG,
            'tunique' => <<<SVG
  <path d="M265 170 Q300 205 335 170 L400 186 L472 232 L522 430 L468 446 L440 335 L440 770 L160 770 L160 335 L132 446 L78 430 L128 232 L200 186 Z" fill="{$main}"/>
  <path d="M265 170 Q300 205 335 170 L335 182 Q300 230 265 182 Z" fill="{$accent}"/>
  <path d="M300 205 L300 430" stroke="{$accent}" stroke-width="6" stroke-dasharray="12 9"/>
  <path d="M255 210 Q300 300 345 210" fill="none" stroke="{$accent}" stroke-width="5"/>
  <path d="M160 742 L440 742" stroke="{$accent}" stroke-width="5" opacity=".7"/>
SVG,
            'costume' => <<<SVG
  <path d="M250 170 L300 220 L350 170 L410 188 L478 236 L520 470 L466 482 L440 360 L440 760 L160 760 L160 360 L134 482 L80 470 L122 236 L190 188 Z" fill="{$main}"/>
  <path d="M250 170 L300 220 L350 170 L330 300 L300 260 L270 300 Z" fill="{$accent}"/>
  <path d="M300 230 L300 760" stroke="#000" stroke-opacity=".18" stroke-width="4"/>
  <circle cx="300" cy="420" r="9" fill="{$accent}"/><circle cx="300" cy="500" r="9" fill="{$accent}"/>
  <rect x="190" y="520" width="70" height="10" rx="3" fill="{$accent}" opacity=".8"/>
  <rect x="340" y="520" width="70" height="10" rx="3" fill="{$accent}" opacity=".8"/>
SVG,
            'chemise' => <<<SVG
  <path d="M245 182 L282 165 L300 200 L318 165 L355 182 L466 232 L516 402 L458 422 L430 322 L430 742 L170 742 L170 322 L142 422 L84 402 L134 232 Z" fill="{$main}"/>
  <path d="M245 182 L282 165 L300 200 L270 230 Z M355 182 L318 165 L300 200 L330 230 Z" fill="{$accent}"/>
  <circle cx="300" cy="260" r="7" fill="{$accent}"/><circle cx="300" cy="340" r="7" fill="{$accent}"/>
  <circle cx="300" cy="420" r="7" fill="{$accent}"/><circle cx="300" cy="500" r="7" fill="{$accent}"/>
  <circle cx="300" cy="580" r="7" fill="{$accent}"/><circle cx="300" cy="660" r="7" fill="{$accent}"/>
  <rect x="340" y="300" width="56" height="62" rx="4" fill="none" stroke="{$accent}" stroke-width="4"/>
SVG,
            'robe' => <<<SVG
  <path d="M262 170 Q300 200 338 170 L372 180 L404 262 L382 332 L478 770 L122 770 L218 332 L196 262 L228 180 Z" fill="{$main}"/>
  <path d="M218 332 L382 332 L376 360 L224 360 Z" fill="{$accent}"/>
  <path d="M170 600 Q300 640 430 600" fill="none" stroke="{$accent}" stroke-width="6"/>
  <path d="M150 690 Q300 730 450 690" fill="none" stroke="{$accent}" stroke-width="6"/>
SVG,
            'enfant' => '<g transform="translate(75 190) scale(.75)">'.self::shape('tunique', $main, $accent).'</g>',
            'pantalon' => <<<SVG
  <path d="M195 190 L405 190 L438 770 L332 770 L300 380 L268 770 L162 770 Z" fill="{$main}"/>
  <rect x="195" y="190" width="210" height="36" fill="{$accent}"/>
  <path d="M300 226 L300 330" stroke="#000" stroke-opacity=".2" stroke-width="4"/>
SVG,
            'chaussure' => <<<SVG
  <path d="M90 600 Q96 488 196 478 L292 466 Q342 460 366 500 Q404 556 480 566 Q540 576 540 626 L540 642 L84 642 Q76 622 90 600 Z" fill="{$main}"/>
  <rect x="80" y="642" width="466" height="30" rx="12" fill="#1F2937"/>
  <path d="M250 476 L300 520 M276 470 L326 514 M226 484 L276 528" stroke="{$accent}" stroke-width="6" stroke-linecap="round"/>
  <path d="M100 600 Q300 600 532 612" stroke="{$accent}" stroke-width="4" fill="none" opacity=".7"/>
SVG,
            'sandale' => <<<SVG
  <path d="M110 600 Q100 560 160 552 L470 552 Q540 560 530 600 Q520 640 460 640 L170 640 Q118 640 110 600 Z" fill="#1F2937"/>
  <path d="M120 590 Q118 566 170 562 L462 562 Q520 566 516 592 Q512 624 458 626 L172 626 Q126 626 120 590 Z" fill="{$main}"/>
  <path d="M240 560 Q300 440 380 560" fill="none" stroke="{$accent}" stroke-width="30" stroke-linecap="round"/>
  <path d="M200 560 Q236 500 262 560" fill="none" stroke="{$main}" stroke-width="18" stroke-linecap="round"/>
SVG,
            'bracelet' => self::beads($main, $accent),
            'lunettes' => <<<SVG
  <path d="M60 420 L140 400 M540 420 L460 400" stroke="#1F2937" stroke-width="14" stroke-linecap="round"/>
  <rect x="110" y="390" width="170" height="130" rx="50" fill="{$main}" opacity=".85"/>
  <rect x="320" y="390" width="170" height="130" rx="50" fill="{$main}" opacity=".85"/>
  <rect x="110" y="390" width="170" height="130" rx="50" fill="none" stroke="#1F2937" stroke-width="12"/>
  <rect x="320" y="390" width="170" height="130" rx="50" fill="none" stroke="#1F2937" stroke-width="12"/>
  <path d="M280 430 Q300 410 320 430" fill="none" stroke="#1F2937" stroke-width="12"/>
  <path d="M140 420 L180 410" stroke="#fff" stroke-width="10" stroke-linecap="round" opacity=".6"/>
SVG,
            'sac' => <<<SVG
  <path d="M220 360 Q220 230 300 230 Q380 230 380 360" fill="none" stroke="{$main}" stroke-width="22"/>
  <rect x="140" y="350" width="320" height="380" rx="28" fill="{$main}"/>
  <path d="M140 380 Q140 350 168 350 L432 350 Q460 350 460 380 L460 470 Q300 520 140 470 Z" fill="#000" opacity=".18"/>
  <rect x="280" y="470" width="40" height="46" rx="6" fill="{$accent}"/>
SVG,
            'manchette' => <<<SVG
  <circle cx="210" cy="460" r="90" fill="{$main}"/><circle cx="210" cy="460" r="58" fill="{$accent}"/>
  <circle cx="390" cy="460" r="90" fill="{$main}"/><circle cx="390" cy="460" r="58" fill="{$accent}"/>
  <circle cx="190" cy="440" r="14" fill="#fff" opacity=".6"/><circle cx="370" cy="440" r="14" fill="#fff" opacity=".6"/>
SVG,
            'tissu' => <<<SVG
  <rect x="130" y="560" width="340" height="110" rx="16" fill="{$main}" opacity=".75"/>
  <rect x="150" y="450" width="320" height="110" rx="16" fill="{$main}" opacity=".88"/>
  <rect x="120" y="340" width="340" height="110" rx="16" fill="{$main}"/>
  <path d="M140 370 Q290 340 440 370 M140 480 Q300 450 450 480 M150 590 Q300 560 450 590" stroke="{$accent}" stroke-width="6" fill="none" opacity=".8"/>
SVG,
            'montre' => <<<SVG
  <rect x="250" y="250" width="100" height="400" rx="30" fill="#1F2937"/>
  <circle cx="300" cy="450" r="120" fill="{$main}"/>
  <circle cx="300" cy="450" r="96" fill="#fff"/>
  <path d="M300 450 L300 385 M300 450 L345 470" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
  <circle cx="300" cy="450" r="9" fill="{$accent}"/>
SVG,
            default => '<circle cx="300" cy="450" r="160" fill="'.$main.'"/>',
        };
    }

    private static function beads(string $main, string $accent): string
    {
        $out = '<circle cx="300" cy="460" r="170" fill="none" stroke="#1F2937" stroke-width="4" opacity=".4"/>';
        for ($i = 0; $i < 20; $i++) {
            $a = 2 * M_PI * $i / 20;
            $x = round(300 + 170 * cos($a), 1);
            $y = round(460 + 170 * sin($a), 1);
            $fill = $i % 4 === 0 ? $accent : $main;
            $out .= '<circle cx="'.$x.'" cy="'.$y.'" r="26" fill="'.$fill.'"/>';
            $out .= '<circle cx="'.($x - 8).'" cy="'.($y - 8).'" r="7" fill="#fff" opacity=".45"/>';
        }

        return $out;
    }
}
