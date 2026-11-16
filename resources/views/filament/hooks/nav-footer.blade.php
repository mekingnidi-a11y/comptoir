@php
    if (! filament()->auth()->check()) {
        return;
    }

    $navItems = [];

    foreach (filament()->getNavigation() as $group) {
        foreach ($group->getItems() as $item) {
            if ($item->isHidden() || blank($item->getUrl()) || blank($item->getIcon())) {
                continue;
            }

            $navItems[] = $item;

            foreach ($item->getChildItems() as $child) {
                if ($child->isHidden() || blank($child->getUrl()) || blank($child->getIcon())) {
                    continue;
                }

                $navItems[] = $child;
            }
        }
    }
@endphp

@if (count($navItems))
    <nav
        class="bc-nav-footer"
        style="--bc-nav-n: {{ count($navItems) }};"
        aria-label="Menu principal"
    >
        <div class="bc-nav-footer-list">
            @foreach ($navItems as $item)
                @php
                    $label = $item->getLabel();
                    $label = $label instanceof \Illuminate\Contracts\Support\Htmlable ? strip_tags($label->toHtml()) : (string) $label;
                    $icon = ($item->isActive() && filled($item->getActiveIcon())) ? $item->getActiveIcon() : $item->getIcon();
                @endphp
                <a
                    {{ \Filament\Support\generate_href_html($item->getUrl(), $item->shouldOpenUrlInNewTab()) }}
                    class="bc-nav-footer-link{{ $item->isActive() ? ' is-active' : '' }}"
                    title="{{ $label }}"
                    aria-label="{{ $label }}"
                    @if ($item->isActive()) aria-current="page" @endif
                >
                    {{ \Filament\Support\generate_icon_html($icon, size: \Filament\Support\Enums\IconSize::Small) }}
                </a>
            @endforeach
        </div>
    </nav>
@endif
