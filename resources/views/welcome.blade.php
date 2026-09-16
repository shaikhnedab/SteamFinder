@extends('app')
@section('title', __('trans.title'))

@section('support')
<div class="container support-wrap">
    <div class="support-banner" role="alert">
        <span class="support-icon" aria-hidden="true">
            <svg class="icon-lg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        </span>
        <div>
            <h2 class="support-title">{{ __('trans.search') }} !</h2>
            <p class="support-text">{{ __('trans.search_alert') }}</p>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
    $formats = explode(' / ', __('trans.search_placeholder'));
    $fields = [
        __('trans.player_name'),
        __('trans.steamid'),
        __('trans.steamid64'),
        __('trans.steamid3'),
        __('trans.custom_url'),
        __('trans.profile_url'),
        __('trans.fivem_hex'),
        __('trans.account_id'),
        __('trans.real_name'),
        __('trans.profile_state'),
        __('trans.profile_created'),
        __('trans.vacbanned'),
        __('trans.last_ban'),
        __('trans.invite_url'),
        __('trans.csgo'),
    ];
@endphp

<section class="landing">
    <div class="card landing-card">
        <div class="landing-card-head">
            <h2>{{ __('trans.enter_steamid') }}</h2>
            <p>{{ __('trans.search_alert') }}</p>
        </div>
        <ul class="chip-list">
            @foreach ($formats as $format)
                <li class="chip">{{ $format }}</li>
            @endforeach
        </ul>
    </div>

    <div class="card landing-card">
        <div class="landing-card-head">
            <h2>{{ __('trans.result') }}</h2>
            <p>{{ __('trans.search_alert') }}</p>
        </div>
        <ul class="chip-list">
            @foreach ($fields as $field)
                <li class="chip-field">
                    <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $field }}
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endsection