@extends('app')
@section('title', $data['pn'].' - '.__('trans.steam_finder'))

@php
    /* Normalize persona state — controller passes a string, but stay
       defensive so a numeric value still renders a sensible badge. */
    $psMap = [
        'Offline' => 'Offline', 'Online' => 'Online',
        'Busy' => 'Busy', 'Away' => 'Away',
        '0' => 'Offline', '1' => 'Online', '2' => 'Busy', '3' => 'Away',
    ];
    $ps = $psMap[$data['ps'] ?? ''] ?? 'Offline';
    $psBadge = [
        'Online' => 'is-online',
        'Offline' => 'is-offline',
        'Busy' => 'is-busy',
        'Away' => 'is-away',
    ];
    $psClass = $psBadge[$ps] ?? 'is-offline';
    /* $ps stays the canonical English key that drives $psClass; only the
       displayed string is localized. */
    $psText = __('trans.state_'.strtolower($ps));

    /* VAC status — API returns a boolean, but stay defensive so
       numeric/string values still render a sensible badge. */
    $vbRaw = $bans['vb'] ?? false;
    $isBanned = $vbRaw === true || $vbRaw === 1 || $vbRaw === '1'
        || $vbRaw === 'Banned'
        || (is_numeric($vbRaw) && (int) $vbRaw !== 0)
        || (!empty($bans['novb']) && (int) $bans['novb'] > 0);
    $vbText = $isBanned ? __('trans.banned') : __('trans.no_bans');
    $vbClass = $isBanned ? 'badge-bad' : 'badge-good';

    /* Days since last ban — trans_choice handles the 0/1/N plural forms.
       A non-numeric value (e.g. the empty string the controller stores when
       the ban lookup fails) is treated as "never banned", as before. */
    $dslbRaw = $bans['dslb'] ?? 0;
    $dslbDays = is_numeric($dslbRaw) ? (int) $dslbRaw : 0;
    $dslbText = trans_choice('trans.ban_days', $dslbDays, ['count' => $dslbDays]);
    $dslbClass = $dslbDays > 0 ? 'badge-warn' : 'badge-good';

    /* Derived values */
    $created   = !empty($data['createdat']) ? date('F j, Y', $data['createdat']) : '';
    /* CS:GO hours. The controller normalises the owned-games payload to
       ['playtime_forever' => minutes]; 0 / absent renders the em-dash. */
    $csgoMinutes = $hours['csgo']['playtime_forever'] ?? 0;
    $csgoHours   = $csgoMinutes > 0
        ? number_format(round($csgoMinutes / 60, 0)).' Hours'
        : '';
    $hex    = 'STEAM:'.strtoupper(dechex($data['si64']));
    $invite = !empty($data['invite_url']) ? 'https://s.team/p/'.$data['invite_url'] : '';
    $extra  = !empty($data['extras']) ? $data['extras'] : ('// '.$data['pn'].' '.date('d-F-Y'));

    /* Row configs for the copy-row partial (label, id, v, optional link) */
    $identifierRows = [
        ['label' => __('trans.steamid'),       'id' => 'field-steamid32', 'v' => (string) $data['steam32']],
        ['label' => __('trans.steamid64'),     'id' => 'field-steamid64', 'v' => (string) $data['si64']],
        ['label' => __('trans.steamid3'),      'id' => 'field-steamid3',  'v' => (string) $data['steam3']],
        ['label' => __('trans.account_id'),    'id' => 'field-account',   'v' => (string) $data['account_id']],
        ['label' => __('trans.custom_url'),    'id' => 'field-customurl', 'v' => (string) $data['purl'], 'link' => $data['purl']],
        ['label' => __('trans.fivem_hex'),     'id' => 'field-hex',       'v' => $hex],
    ];

    $profileRows = [
        ['label' => __('trans.real_name'),     'id' => 'field-realname',  'v' => (string) $data['rn']],
        ['label' => __('trans.profile_created'),'id' => 'field-created',  'v' => $created],
        ['label' => __('trans.invite_url'),    'id' => 'field-invite',    'v' => $invite, 'link' => $invite],
    ];

    $playRows = [
        ['label' => __('trans.csgo'),          'id' => 'field-csgo',      'v' => $csgoHours],
        ['label' => __('trans.extra'),         'id' => 'field-extra',     'v' => $extra],
    ];
@endphp

@section('content')

<section class="readout-panel">
    <div class="profile-head">
        <a class="avatar-link" href="{{ $data['purl'] }}" target="_blank" rel="noopener" aria-label="{{ __('trans.open_profile') }} — {{ $data['pn'] }}">
            <img class="avatar" src="{{ $data['avf'] }}" alt="{{ $data['pn'] }} avatar" width="68" height="68">
        </a>
        <div class="profile-meta">
            <div class="profile-title-row">
                <h1 class="profile-name">
                    <a class="profile-name-link" href="{{ $data['purl'] }}" target="_blank" rel="noopener">
                        {{ $data['pn'] }}
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                </h1>
                <span class="badge {{ $psClass }}">
                    <span class="status-dot" aria-hidden="true"></span>{{ $psText }}
                </span>
            </div>
            <p class="profile-realname">{{ !empty($data['rn']) ? $data['rn'] : '—' }}</p>
        </div>
        <div class="profile-actions">
            <a class="btn btn-ghost" href="{{ $data['purl'] }}" target="_blank" rel="noopener">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                {{ __('trans.open_profile') }}
            </a>
        </div>
    </div>
</section>

<div class="results-grid">

    <section class="panel">
        <div class="panel-title">
            <div>
                <h2>
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                    {{ __('trans.identifiers') }}
                </h2>
            </div>
        </div>
        <div class="readout-grid">
            @foreach ($identifierRows as $row)
                @include('partials.copy-row', $row)
            @endforeach
        </div>
    </section>

    <div class="results-col">

        <section class="panel">
            <div class="panel-title">
                <div>
                    <h2>
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ __('trans.identity') }}
                    </h2>
                </div>
            </div>
            <div class="readout-grid">
                <div class="badge-row">
                    <span class="badge-row-label">{{ __('trans.profile_state') }}</span>
                    <span class="badge {{ $psClass }}">
                        <span class="status-dot" aria-hidden="true"></span>{{ $psText }}
                    </span>
                </div>
                @foreach ($profileRows as $row)
                    @include('partials.copy-row', $row)
                @endforeach
            </div>
        </section>

        <section class="panel">
            <div class="panel-title">
                <div>
                    <h2>
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ __('trans.account_safety') }}
                    </h2>
                </div>
            </div>
            <div class="badge-list">
                <div class="badge-row">
                    <span class="badge-row-label">{{ __('trans.vacbanned') }}</span>
                    <span class="badge {{ $vbClass }}">{{ $vbText }}</span>
                </div>
                <div class="badge-row">
                    <span class="badge-row-label">{{ __('trans.last_ban') }}</span>
                    <span class="badge {{ $dslbClass }}">{{ $dslbText }}</span>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-title">
                <div>
                    <h2>
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ __('trans.playtime') }}
                    </h2>
                </div>
            </div>
            <div class="readout-grid">
                @foreach ($playRows as $row)
                    @include('partials.copy-row', $row)
                @endforeach
            </div>
        </section>

    </div>
</div>

@endsection
