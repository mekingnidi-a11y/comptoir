<?php

namespace App\Support;

use BackedEnum;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

use function Filament\Support\generate_icon_html;

class FilamentBrand
{
    public static function heading(string $label, string|BackedEnum $icon): HtmlString
    {
        return self::labeled($label, $icon, IconSize::Large, 'bc-screen-heading');
    }

    public static function columnLabel(string $label, string|BackedEnum $icon): HtmlString
    {
        return self::labeled($label, $icon, IconSize::Small, 'bc-col-heading');
    }

    public static function line(TextColumn $column, string $label, string|BackedEnum $icon): TextColumn
    {
        return $column
            ->label(self::columnLabel($label, $icon))
            ->icon($icon)
            ->iconColor(self::color($icon))
            ->iconPosition(IconPosition::Before);
    }

    public static function booleanLine(string $name, string $label, string|BackedEnum $icon): TextColumn
    {
        return self::line(
            TextColumn::make($name)
                ->formatStateUsing(fn ($state): string => $state ? 'Oui' : 'Non')
                ->color(fn ($state): string => $state ? 'success' : 'gray'),
            $label,
            $icon,
        );
    }

    public static function color(string|BackedEnum $icon): string
    {
        $name = $icon instanceof BackedEnum ? $icon->name : (string) $icon;

        return match (true) {
            str_contains($name, 'UserGroup'), str_contains($name, 'Users') => 'pink',
            str_contains($name, 'User') => 'violet',
            str_contains($name, 'Phone') => 'emerald',
            str_contains($name, 'Envelope') => 'sky',
            str_contains($name, 'Calendar') => 'amber',
            str_contains($name, 'Banknotes'), str_contains($name, 'Currency') => 'orange',
            str_contains($name, 'Chart') => 'lime',
            str_contains($name, 'Cake') => 'pink',
            str_contains($name, 'Tag') => 'slate',
            str_contains($name, 'MapPin') => 'blue',
            str_contains($name, 'Home') => 'emerald',
            str_contains($name, 'Building') => 'cyan',
            str_contains($name, 'Clipboard'), str_contains($name, 'Document') => 'indigo',
            str_contains($name, 'Photo') => 'pink',
            str_contains($name, 'Lock') => 'gray',
            str_contains($name, 'Key') => 'rose',
            str_contains($name, 'Shield') => 'purple',
            str_contains($name, 'Star'), str_contains($name, 'Chat') => 'amber',
            str_contains($name, 'Hashtag') => 'slate',
            str_contains($name, 'Link') => 'sky',
            str_contains($name, 'Clock') => 'cyan',
            str_contains($name, 'Sparkles') => 'fuchsia',
            str_contains($name, 'Squares') => 'lime',
            str_contains($name, 'Wrench') => 'orange',
            str_contains($name, 'Identification') => 'indigo',
            str_contains($name, 'ArrowTrending') => 'rose',
            str_contains($name, 'Percent') => 'orange',
            default => 'primary',
        };
    }

    public static function labeled(string $label, string|BackedEnum $icon, IconSize $size, string $class): HtmlString
    {
        $color = self::color($icon);
        $iconHtml = generate_icon_html(
            $icon,
            attributes: (new ComponentAttributeBag)->class(['bc-heading-icon', 'bc-icon-'.$color]),
            size: $size,
        );

        return new HtmlString(
            '<span class="'.$class.'">'.
            ($iconHtml?->toHtml() ?? '').
            '<span>'.e($label).'</span></span>'
        );
    }

    public static function pageIconFromLivewire(object $livewire): string|BackedEnum|null
    {
        if (method_exists($livewire, 'getResource')) {
            try {
                $resource = $livewire::getResource();

                return $resource::getNavigationIcon() ?: Heroicon::OutlinedSquares2x2;
            } catch (\Throwable) {
                //
            }
        }

        if (method_exists($livewire, 'getNavigationIcon')) {
            return $livewire::getNavigationIcon();
        }

        return Heroicon::OutlinedSquares2x2;
    }
}
