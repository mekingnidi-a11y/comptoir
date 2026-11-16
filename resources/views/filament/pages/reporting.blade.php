<x-filament-panels::page>
    <x-filament::section class="mb-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <label class="block">
                <span class="text-sm font-medium">{!! \App\Support\FilamentBrand::columnLabel('Du', \Filament\Support\Icons\Heroicon::OutlinedCalendarDays) !!}</span>
                <input type="date" wire:model.live="du" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700" />
            </label>
            <label class="block">
                <span class="text-sm font-medium">{!! \App\Support\FilamentBrand::columnLabel('Au', \Filament\Support\Icons\Heroicon::OutlinedCalendarDays) !!}</span>
                <input type="date" wire:model.live="au" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700" />
            </label>
            @if (auth()->user()?->isProprietaire())
                <label class="block">
                    <span class="text-sm font-medium">{!! \App\Support\FilamentBrand::columnLabel('Gérant', \Filament\Support\Icons\Heroicon::OutlinedUser) !!}</span>
                    <select wire:model.live="gerant_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700">
                        <option value="">Tous</option>
                        @foreach ($this->gerants() as $gerant)
                            <option value="{{ $gerant->id }}">{{ $gerant->nom }}</option>
                        @endforeach
                    </select>
                </label>
            @endif
        </div>
    </x-filament::section>

    @php($t = $this->totaux())
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5 mb-6">
        <x-filament::section>
            <div class="text-sm text-gray-500">{!! \App\Support\FilamentBrand::columnLabel('Journées', \Filament\Support\Icons\Heroicon::OutlinedClipboardDocumentList) !!}</div>
            <div class="text-2xl font-semibold">{{ $t['n'] }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500">{!! \App\Support\FilamentBrand::columnLabel('Recette', \Filament\Support\Icons\Heroicon::OutlinedBanknotes) !!}</div>
            <div class="text-2xl font-semibold">{{ $this->money($t['recette']) }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500">{!! \App\Support\FilamentBrand::columnLabel('Dépenses', \Filament\Support\Icons\Heroicon::OutlinedArrowTrendingDown) !!}</div>
            <div class="text-2xl font-semibold">{{ $this->money($t['depenses']) }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500">{!! \App\Support\FilamentBrand::columnLabel('Salaires gérants', \Filament\Support\Icons\Heroicon::OutlinedUserGroup) !!}</div>
            <div class="text-2xl font-semibold">{{ $this->money($t['commission']) }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500">{!! \App\Support\FilamentBrand::columnLabel('Bénéfice', \Filament\Support\Icons\Heroicon::OutlinedChartBar) !!}</div>
            <div class="text-2xl font-semibold {{ $t['benefice'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $this->money($t['benefice']) }}</div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6">
        <x-filament::section :heading="\App\Support\FilamentBrand::heading('Par gérant', \Filament\Support\Icons\Heroicon::OutlinedUserGroup)">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">Gérant</th>
                            <th class="py-2 text-right">Recette</th>
                            <th class="py-2 text-right">Dépenses</th>
                            <th class="py-2 text-right">Salaire</th>
                            <th class="py-2 text-right">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->parGerant() as $ligne)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-2 font-medium">{{ $ligne->nom }}</td>
                                <td class="py-2 text-right">{{ $this->money($ligne->recette) }}</td>
                                <td class="py-2 text-right">{{ $this->money($ligne->depenses) }}</td>
                                <td class="py-2 text-right">{{ $this->money($ligne->commission) }}</td>
                                <td class="py-2 text-right">{{ $this->money($ligne->benefice) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-3 text-gray-500">Aucune donnée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section :heading="\App\Support\FilamentBrand::heading('Articles vendus', \Filament\Support\Icons\Heroicon::OutlinedCake)">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">Article</th>
                            <th class="py-2 text-right">Qté</th>
                            <th class="py-2 text-right">Recette</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->topArticles() as $ligne)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-2 font-medium">{{ $ligne->nom }}</td>
                                <td class="py-2 text-right">{{ rtrim(rtrim(number_format((float) $ligne->qte, 2, ',', ' '), '0'), ',') }}</td>
                                <td class="py-2 text-right">{{ $this->money($ligne->ca) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-3 text-gray-500">Aucune vente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    <x-filament::section :heading="\App\Support\FilamentBrand::heading('Détail des journées', \Filament\Support\Icons\Heroicon::OutlinedClipboardDocumentList)">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Date</th>
                        <th class="py-2">Gérant</th>
                        <th class="py-2">État</th>
                        <th class="py-2 text-right">Recette</th>
                        <th class="py-2 text-right">Dépenses</th>
                        <th class="py-2 text-right">Salaire</th>
                        <th class="py-2 text-right">Bénéfice</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->journees() as $j)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-2">{{ $j->date?->format('d/m/Y') }}</td>
                            <td class="py-2">{{ $j->gerant?->nom }}</td>
                            <td class="py-2">{{ $j->statut === 'cloturee' ? 'Clôturée' : 'Ouverte' }}</td>
                            <td class="py-2 text-right">{{ $this->money($j->recette) }}</td>
                            <td class="py-2 text-right">{{ $this->money($j->total_depenses) }}</td>
                            <td class="py-2 text-right">{{ $this->money($j->commission) }}</td>
                            <td class="py-2 text-right">{{ $this->money($j->benefice) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-3 text-gray-500">Aucune journée sur cette période.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
