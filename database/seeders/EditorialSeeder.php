<?php

namespace Database\Seeders;

use App\Models\Slide;
use Database\Seeders\Support\PlaceholderArt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Brings the generated placeholder visuals in line with the editorial design.
 * Safe on a live site (php artisan db:seed --class=EditorialSeeder --force):
 * only the SVG placeholders created by the demo seeder are touched, never the
 * photos uploaded from the admin.
 */
class EditorialSeeder extends Seeder
{
    private const SLIDE_SHAPES = [
        ['boubou', 'tunique'],
        ['enfant', 'tunique'],
        ['costume', 'boubou'],
        ['chaussure', 'sac'],
    ];

    /** Colours of the first-generation placeholders. */
    private const LEGACY_PRODUCT_PALETTES = [
        ['#FDEBF0', '#F7D3DE', '#E0144F'],
        ['#E8F4FD', '#D2E9FA', '#0B6CB5'],
        ['#F4F1EA', '#E8E1D2', '#8A6A3B'],
        ['#EEF2F6', '#DCE3EC', '#0B2A4A'],
        ['#FFF4E5', '#FCE3C3', '#C46A0B'],
        ['#EAF7F1', '#D2EEE1', '#1E7D55'],
        ['#F3EEFB', '#E3D8F5', '#5B3A9B'],
        ['#FBEFEF', '#F3D9D9', '#7A1F2B'],
    ];

    private const LEGACY_CATEGORY_COLORS = ['#E0144F', '#0B6CB5', '#8A6A3B', '#0B2A4A', '#C46A0B', '#1E7D55', '#5B3A9B', '#7A1F2B', '#2C7DA0', '#B5179E'];

    private const LEGACY_STORE_COLORS = ['#E0144F', '#0B6CB5', '#0B2A4A', '#1E7D55', '#8A6A3B', '#5B3A9B', '#C46A0B', '#7A1F2B'];

    public function run(): void
    {
        // 1. Home banners: redrawn for the split-screen hero.
        foreach (Slide::orderBy('position')->get()->values() as $i => $slide) {
            if (str_starts_with((string) $slide->image, 'slides/slide-')) {
                PlaceholderArt::hero($i, self::SLIDE_SHAPES[$i % count(self::SLIDE_SHAPES)], $slide->image);
            }
        }

        // 2. Product, category and showroom placeholders: same drawing, editorial colours.
        $productMap = ['#3FA9F5' => '#B08D57', '#F5C542' => '#D6124A'];
        foreach (self::LEGACY_PRODUCT_PALETTES as $i => $legacy) {
            foreach ($legacy as $j => $color) {
                $productMap[$color] = PlaceholderArt::PALETTES[$i][$j];
            }
        }

        $count = $this->recolor('products', $productMap)
            + $this->recolor('categories', array_combine(self::LEGACY_CATEGORY_COLORS, PlaceholderArt::CATEGORY_COLORS))
            + $this->recolor('showrooms', array_combine(self::LEGACY_STORE_COLORS, PlaceholderArt::STORE_COLORS));

        $this->command?->info("{$count} visuels provisoires mis aux couleurs éditoriales.");
    }

    /**
     * @param  array<string, string>  $map
     */
    private function recolor(string $directory, array $map): int
    {
        $disk = Storage::disk('public');
        $count = 0;

        foreach ($disk->files($directory) as $file) {
            if (! str_ends_with($file, '.svg')) {
                continue;
            }
            $svg = $disk->get($file);
            // Only our generated placeholders carry this signature.
            if (! str_contains($svg, 'MOMBEYA GALY') && ! str_contains($svg, 'patternUnits')) {
                continue;
            }
            $recolored = strtr($svg, $map);
            if ($recolored !== $svg) {
                $disk->put($file, $recolored);
                $count++;
            }
        }

        return $count;
    }
}
