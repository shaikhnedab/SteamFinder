{{-- Copyable value row. Expected vars: $label, $id, $v ; optional: $mono, $link
     Rendered as a readout so it matches the instrument-panel display.
     The data-copy-row / data-copy / data-copy-label hooks are load-bearing
     for public/js/script.js — keep them intact. --}}
<div class="readout copy-row" data-copy-row>
    <span class="r-label">{{ $label }}</span>
    <div class="copy-row-value">
        @if ($v !== '' && $v !== null)
            {{-- dir=ltr: IDs, hex and URLs are LTR runs. Without it an RTL
                 locale reorders trailing neutrals, e.g. the closing slash of
                 ".../GabeLoganNewell/" jumps to the front. All bundled
                 locales (en/he/es/ru/zh) are LTR scripts. --}}
            <code class="copy-value r-value" id="{{ $id }}" dir="ltr">{{ $v }}</code>
            @if (!empty($link))
                <a class="copy-link" href="{{ $link }}" target="_blank" rel="noopener" aria-label="{{ __('trans.open_profile') }}">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
            @endif
            <button class="copy-btn" type="button" data-copy="#{{ $id }}" data-copy-label="{{ __('trans.copy_action', ['label' => $label]) }}" aria-label="{{ __('trans.copy_action', ['label' => $label]) }}">
                <svg class="icon icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                <svg class="icon icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                <span class="copy-btn-text">{{ __('trans.copied') }}</span>
            </button>
        @else
            <span class="copy-empty">&mdash;</span>
        @endif
    </div>
</div>
