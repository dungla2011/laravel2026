{{-- Language Switcher Component --}}
<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageSwitcher" data-bs-toggle="dropdown" aria-expanded="false">
            <span>{{ locale_flag(current_locale()) }}</span>
            <span class="ms-1">{{ locale_name(current_locale()) }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageSwitcher">
            @foreach(supported_locales() as $locale)
                <li>
                    <a class="dropdown-item {{ is_current_locale($locale) ? 'active' : '' }}" 
                       href="{{ switch_locale($locale) }}">
                        <span>{{ locale_flag($locale) }}</span>
                        <span class="ms-2">{{ locale_name($locale) }}</span>
                        @if(is_current_locale($locale))
                            <i class="fas fa-check ms-2 text-success"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<style>
.language-switcher .dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
}

.language-switcher .dropdown-item:hover {
    background-color: #f8f9fa;
}

.language-switcher .dropdown-item.active {
    background-color: #e9ecef;
    font-weight: 600;
}
</style>
