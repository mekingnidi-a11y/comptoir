<x-filament-widgets::widget>
    <x-filament::section :heading="\App\Support\FilamentBrand::heading('Meilleures ventes du mois', \Filament\Support\Icons\Heroicon::OutlinedCake)">
        @php($articles = $this->getArticles())
        @if (count($articles) === 0)
            <p class="text-sm text-gray-500">Aucune vente ce mois-ci.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">{!! \App\Support\FilamentBrand::columnLabel('Article', \Filament\Support\Icons\Heroicon::OutlinedCake) !!}</th>
                            <th class="py-2">{!! \App\Support\FilamentBrand::columnLabel('Qté', \Filament\Support\Icons\Heroicon::OutlinedHashtag) !!}</th>
                            <th class="py-2 text-right">{!! \App\Support\FilamentBrand::columnLabel('Recette', \Filament\Support\Icons\Heroicon::OutlinedBanknotes) !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $ligne)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-2 font-medium">{{ $ligne->nom }}</td>
                                <td class="py-2">{{ rtrim(rtrim(number_format((float) $ligne->qte, 2, ',', ' '), '0'), ',') }}</td>
                                <td class="py-2 text-right">{{ \App\Support\Money::format($ligne->ca) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
