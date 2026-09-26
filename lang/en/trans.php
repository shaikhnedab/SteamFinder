<?php

return [

    'title' => 'Steam Finder',
    'steam_finder' => 'Steam Finder',

    'enter_steamid' => 'Enter SteamID',
    'search_placeholder' => 'SteamID / SteamID3 / SteamID64 / Custom URL / Complete URL',
    'search' => 'Search',
    'search_button' => 'Search',

    'result' => 'Result',
    'player_name' => 'Player Name',
    'steamid' => 'SteamID',
    'steamid64' => 'SteamID64',
    'steamid3' => 'SteamID3',
    'custom_url' => 'Custom URL',
    'profile_url' => 'Profile URL / Permanent Profile link',
    'fivem_hex' => 'FiveM, HEX',
    'account_id' => 'Account ID',
    'real_name' => 'Real Name',
    'profile_state' => 'Profile State',
    'profile_created' => 'Profile Created',
    'vacbanned' => 'VACBanned',
    'last_ban' => 'Days Since Last Ban',
    'invite_url' => 'Invite URL',
    'csgo' => 'CSGO',
    'extra' => 'Extra',

    'search_alert' => 'This app will extract your steam details by entering any steamid format.',

    /* Field-instrument UI */
    'kicker' => 'Steam identity utilities',
    'hero_title' => 'Resolve any Steam ID into every format it can be.',
    'hero_text' => 'Paste a SteamID, SteamID3, SteamID64, custom URL or full profile link and get back every identifier, profile state, VAC status and invite link — one click to copy each.',
    'search_note' => 'Accepts SteamID, SteamID3, SteamID64, custom URLs and full steamcommunity.com links.',
    'accepts_label' => 'Accepted input formats',
    'result_label' => 'Fields resolved for every profile',
    'identifiers' => 'Identifiers',
    'identity' => 'Identity',
    'account_safety' => 'Account Safety',
    'playtime' => 'Playtime',

    /* badge values (previously hardcoded English in the view) */
    'state_online' => 'Online',
    'state_offline' => 'Offline',
    'state_busy' => 'Busy',
    'state_away' => 'Away',
    'banned' => 'Banned',
    'no_bans' => 'No Bans',
    'ban_days' => '{0} No Bans|{1} 1 day since last ban|[2,*] :count days since last ban',

    'open_profile' => 'Open profile',
    'copy_action' => 'Copy :label',
    'copied' => 'Copied',
    'copy_failed' => 'Copy failed — select and copy manually',
    'theme_toggle' => 'Toggle theme',
    'dismiss' => 'Dismiss',

    /* controller messages */
    'profile_found' => 'Profile Found!',
    'error_invalid_input' => 'Please enter a valid search input.',
    'error_invalid_format' => 'Unrecognised ID format. Use a SteamID, SteamID3, SteamID64, custom URL, or a full steamcommunity.com link.',
    'error_check_id' => 'Failed to get data, please check the ID!',
    'error_no_api_key' => 'Steam API key is not configured.',
    'error_api_unreachable' => 'Failed to connect to Steam API. Please try again later.',

    /* error pages */
    'back_home' => 'Back to search',
    'error_404_title' => 'That page does not exist',
    'error_404_text' => 'The address you followed is not a Steam profile. Head back and try a SteamID, a custom URL, or a full profile link.',
    'error_500_title' => 'Something went wrong',
    'error_500_text' => 'The server hit an unexpected error. This has been logged — please try again in a moment.',

];
