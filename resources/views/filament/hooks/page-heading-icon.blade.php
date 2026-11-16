@php
    $livewire = \Livewire\Livewire::current();
    $icon = $livewire ? \App\Support\FilamentBrand::pageIconFromLivewire($livewire) : null;
    $color = $icon ? \App\Support\FilamentBrand::color($icon) : 'primary';
@endphp
@if ($icon)
    {{ \Filament\Support\generate_icon_html($icon, attributes: (new \Filament\Support\View\ComponentAttributeBag)->class(['fi-page-heading-icon', 'bc-icon-'.$color]), size: \Filament\Support\Enums\IconSize::Large) }}
@endif
