{{-- Size charts. $only = homme|femme|enfant|chaussures to show a single chart, null for all. --}}
@php
    $charts = [
        'homme' => [
            'title' => 'Homme',
            'head' => ['Taille', 'Poitrine', 'Taille (ceinture)', 'Hanches', 'Stature'],
            'rows' => [
                ['S', '88 – 96', '76 – 84', '90 – 98', '165 – 172'],
                ['M', '96 – 104', '84 – 92', '98 – 106', '170 – 177'],
                ['L', '104 – 112', '92 – 100', '106 – 114', '175 – 182'],
                ['XL', '112 – 120', '100 – 108', '114 – 122', '178 – 186'],
                ['XXL', '120 – 128', '108 – 116', '122 – 130', '180 – 190'],
            ],
        ],
        'femme' => [
            'title' => 'Femme',
            'head' => ['Taille', 'Poitrine', 'Taille (ceinture)', 'Hanches', 'Équivalence'],
            'rows' => [
                ['S', '82 – 88', '64 – 70', '88 – 94', '36 – 38'],
                ['M', '88 – 94', '70 – 76', '94 – 100', '38 – 40'],
                ['L', '94 – 100', '76 – 82', '100 – 106', '40 – 42'],
                ['XL', '100 – 108', '82 – 90', '106 – 114', '42 – 44'],
            ],
        ],
        'enfant' => [
            'title' => 'Enfant',
            'head' => ['Âge', 'Stature', 'Poitrine', 'Tour de taille'],
            'rows' => [
                ['2 ans', '86 – 92', '52 – 54', '50 – 52'],
                ['4 ans', '98 – 104', '55 – 57', '52 – 54'],
                ['6 ans', '110 – 116', '58 – 60', '54 – 56'],
                ['8 ans', '122 – 128', '62 – 64', '56 – 58'],
                ['10 ans', '134 – 140', '66 – 68', '58 – 61'],
                ['12 ans', '146 – 152', '70 – 74', '61 – 64'],
            ],
        ],
        'chaussures' => [
            'title' => 'Chaussures',
            'head' => ['Pointure (EU)', 'UK', 'US', 'Longueur du pied'],
            'rows' => [
                ['40', '6,5', '7,5', '25,4'],
                ['41', '7,5', '8', '26,0'],
                ['42', '8', '9', '26,7'],
                ['43', '9', '10', '27,3'],
                ['44', '9,5', '10,5', '28,0'],
                ['45', '10,5', '11,5', '28,6'],
            ],
        ],
    ];
    $only ??= null;
    $selected = $only && isset($charts[$only]) ? [$only => $charts[$only]] : $charts;
@endphp

<div class="space-y-10">
    @foreach ($selected as $key => $chart)
        <div>
            <h3 class="mb-3 font-display text-2xl text-ink">{{ $chart['title'] }}</h3>
            <div class="overflow-x-auto border border-line bg-white">
                <table class="w-full min-w-[480px] text-left text-sm">
                    <thead class="bg-sand text-[.68rem] tracking-[.14em] text-ink uppercase">
                        <tr>
                            @foreach ($chart['head'] as $heading)
                                <th class="px-4 py-3 font-medium">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($chart['rows'] as $row)
                            <tr>
                                @foreach ($row as $i => $cell)
                                    <td @class(['px-4 py-3', 'font-medium text-ink' => $i === 0, 'text-body' => $i > 0])>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-muted">{{ $key === 'chaussures' ? 'Longueur du pied en centimètres.' : 'Mesures du corps en centimètres.' }}</p>
        </div>
    @endforeach
</div>
