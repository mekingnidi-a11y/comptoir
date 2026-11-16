<x-filament-widgets::widget>
    <x-filament::section heading="Meilleures ventes du mois">
        @php($articles = $this->getArticles())
        @if (count($articles) === 0)
            <p class="text-sm text-gray-500">Aucune vente ce mois-ci.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">Article</th>
                            <th class="py-2">Qté</th>
                            <th class="py-2 text-right">Recette</th>
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
