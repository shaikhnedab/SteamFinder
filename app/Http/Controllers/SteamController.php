<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use SteamID;

class SteamController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'steamid' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->backWithError($request, __('trans.error_invalid_input'));
        }

        $id = $this->normalizeInput($request->steamid);

        $kind = $this->classify($id);

        if ($kind === null) {
            return $this->backWithError($request, __('trans.error_invalid_format'));
        }

        /* Structured IDs resolve locally, so a numeric search costs no API call.
           Only a genuine custom-URL slug needs ResolveVanityURL. */
        if ($kind !== 'vanity') {
            try {
                $s = new SteamID($id);

                if ($s->IsValid()) {
                    return redirect('/'.$s->ConvertToUInt64())->with('from_search', true);
                }
            } catch (\InvalidArgumentException $e) {
                // fall through to error redirect
            }

            return $this->backWithError($request, __('trans.error_check_id'));
        }

        $szID = $this->VanityURL($id);
        if ($szID !== null) {
            return redirect('/'.$szID)->with('from_search', true);
        }

        return $this->backWithError($request, __('trans.error_check_id'));
    }

    /**
     * Redirect back to the page the search came from, carrying an error flash.
     *
     * Deliberately not redirect()->back(): that honours the raw Referer
     * header, so a crafted POST could bounce the visitor to an attacker's site
     * (open redirect). The referer is followed only when it points back at
     * this host; anything else falls back to the landing page.
     */
    private function backWithError(Request $request, string $message)
    {
        $target = '/';
        $referer = $request->headers->get('referer');

        if ($referer) {
            $host = parse_url($referer, PHP_URL_HOST);

            if (is_string($host) && $host !== '' && strcasecmp($host, $request->getHost()) === 0) {
                $target = $referer;
            }
        }

        return redirect($target)
            ->with('error', $message)
            ->withInput($request->only('steamid'));
    }

    /**
     * Classify a normalized search input before any network call is made.
     *
     * The point of this gate is to keep obviously-unusable input (spaces,
     * punctuation, absurd length, URLs that are not profiles) from costing a
     * ResolveVanityURL round-trip — not to second-guess what Steam considers a
     * valid custom URL. Real accounts do own vanity names like "0", "-1" and
     * "junk", so anything Steam could legitimately resolve is passed through.
     *
     * @return string|null 'id64' | 'id3' | 'id1' | 'vanity', or null if unrecognised
     */
    private function classify(string $id): ?string
    {
        // SteamID64 — always 7656119 followed by 10 digits.
        if (preg_match('/^7656119[0-9]{10}$/', $id) === 1) {
            return 'id64';
        }

        // SteamID3, e.g. [U:1:231702]
        if (preg_match('/^\[[AGMPCgcLTIUai]:[0-4]:[0-9]{1,10}(:[0-9]+)?\]$/i', $id) === 1) {
            return 'id3';
        }

        // SteamID (v1), e.g. STEAM_1:0:115851
        if (preg_match('/^STEAM_[0-4]:[01]:[0-9]{1,10}$/i', $id) === 1) {
            return 'id1';
        }

        /* Custom URL / vanity slug: the character set Steam allows in a custom
           URL. Anything containing spaces, dots, slashes or other punctuation
           can never be one, so reject it here rather than paying for a call. */
        if (preg_match('/^[a-zA-Z0-9_-]{1,32}$/', $id) === 1) {
            return 'vanity';
        }

        return null;
    }

    /**
     * Normalize various Steam URL / ID formats to a clean identifier.
     */
    private function normalizeInput(string $input): string
    {
        $input = trim($input);

        // Strip common schemes
        $input = preg_replace('#^https?://#i', '', $input);

        // Drop any query string or fragment before path matching
        $input = preg_replace('/[?#].*$/', '', $input);

        // If it looks like a steamcommunity.com URL, extract the final path segment
        if (preg_match('#steamcommunity\.com/(?:id|profiles)/([^/\s]+)#i', $input, $m)) {
            $input = $m[1];
        }

        // Strip trailing slash and whitespace
        $input = rtrim($input, "/ \t\n\r\0\x0B");

        return $input;
    }

    public function VanityURL($username)
    {
        $apikey = config('steam-api.steamApiKey');

        if (empty($apikey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withOptions(['connect_timeout' => 5])
                ->get('https://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/', [
                    'key' => $apikey,
                    'vanityurl' => $username,
                ]);

            $user = $response->json();
        } catch (\Throwable $e) {
            return null;
        }

        if (! is_array($user) || ! isset($user['response']) || ! is_array($user['response'])) {
            return null;
        }

        $success = $user['response']['success'] ?? null;

        if ((int) $success === 1) {
            return $user['response']['steamid'] ?? null;
        }

        return null;
    }

    public function show($id)
    {
        try {
            $s = new SteamID($id);
        } catch (\InvalidArgumentException $e) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        if (! $s->IsValid()) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        $apikey = config('steam-api.steamApiKey');

        if (empty($apikey)) {
            return redirect('/')->with('error', __('trans.error_no_api_key'));
        }

        try {
            $response = Http::timeout(6)->withOptions(['connect_timeout' => 3])
                ->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
                    'key' => $apikey,
                    'steamids' => $id,
                ]);

            $data = $response->json();
        } catch (\Throwable $e) {
            return redirect('/')->with('error', __('trans.error_api_unreachable'));
        }

        if (! is_array($data) || ! isset($data['response']) || ! is_array($data['response'])) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        $players = $data['response']['players'] ?? [];

        if (empty($players[0])) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        $id64temp = $players[0]['steamid'] ?? null;

        if ($id64temp === null) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        try {
            $s = new SteamID($id64temp);
        } catch (\InvalidArgumentException $e) {
            return redirect('/')->with('error', __('trans.error_check_id'));
        }

        $data['si64'] = $players[0]['steamid'];
        $data['cvs'] = $players[0]['communityvisibilitystate'] ?? '';
        $data['prs'] = $players[0]['profilestate'] ?? '';
        $data['pn'] = $players[0]['personaname'] ?? '';
        $data['purl'] = $players[0]['profileurl'] ?? '';
        $data['av'] = $players[0]['avatar'] ?? '';
        $data['avm'] = $players[0]['avatarmedium'] ?? '';
        $data['avf'] = $players[0]['avatarfull'] ?? '';
        $data['avhash'] = $players[0]['avatarhash'] ?? '';
        $data['ps'] = $players[0]['personastate'] ?? '';
        $data['rn'] = $players[0]['realname'] ?? '';
        $data['pcid'] = $players[0]['primaryclanid'] ?? '';
        $data['createdat'] = $players[0]['timecreated'] ?? '';
        $data['psf'] = $players[0]['personastateflags'] ?? '';

        $data['steam3'] = $s->RenderSteam3();
        $data['steam32'] = $s->RenderSteam2();
        $data['account_id'] = $s->GetAccountID();
        $data['invite_url'] = $s->RenderSteamInvite();
        $data['profile2'] = 'https://steamcommunity.com/profiles/'.$data['si64'].'/';

        /* Supplementary lookups (bans, CS:GO hours).
           These deliberately go through the HTTP facade rather than the
           bundled Steam client: that client builds `new GuzzleClient()` with
           no timeout, so any Steam-side slowness or rate-limiting turned into
           an unbounded page hang. Both are now bounded, and a failed lookup is
           cached too, so a slow upstream is paid at most once per TTL instead
           of on every single page view. */
        $bans = Cache::get("steam.bans.$id64temp");
        if ($bans === null) {
            $bans = ['cb' => '', 'vb' => '', 'novb' => '', 'dslb' => '', 'nogb' => '', 'eb' => ''];
            try {
                $res = Http::timeout(3)->withOptions(['connect_timeout' => 2])
                    ->get('https://api.steampowered.com/ISteamUser/GetPlayerBans/v0001/', [
                        'key' => $apikey,
                        'steamids' => $id64temp,
                    ]);

                $p = $res->json('response.players.0');

                if (is_array($p)) {
                    $bans = [
                        'cb' => $p['CommunityBanned'] ?? '',
                        'vb' => $p['VACBanned'] ?? '',
                        'novb' => $p['NumberOfVACBans'] ?? '',
                        'dslb' => $p['DaysSinceLastBan'] ?? '',
                        'nogb' => $p['NumberOfGameBans'] ?? '',
                        'eb' => $p['EconomyBan'] ?? '',
                    ];
                }
            } catch (\Throwable $e) {
                // keep the empty shape; cached below so we don't retry every hit
            }
            Cache::put("steam.bans.$id64temp", $bans, 600);
        }

        // CS:GO hours. appids_filter keeps this to a single title, but Steam
        // still has to walk the account's library, so it is the slowest call.
        $hours = Cache::get("steam.hours.$id64temp");
        if ($hours === null) {
            $hours = [];
            try {
                $res = Http::timeout(4)->withOptions(['connect_timeout' => 2])
                    ->get('https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/', [
                        'key' => $apikey,
                        'steamid' => $id64temp,
                        'appids_filter[0]' => 730,
                    ]);

                $games = $res->json('response.games');

                if (is_array($games)) {
                    foreach ($games as $g) {
                        if (isset($g['appid']) && (int) $g['appid'] === 730) {
                            $hours['csgo'] = ['playtime_forever' => $g['playtime_forever'] ?? 0];
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // cached below, so a timeout isn't paid again on the next view
            }
            Cache::put("steam.hours.$id64temp", $hours, 600);
        }

        /* "Profile Found!" is only truthful when the visitor arrived from the
           search box — a bookmarked or typed /{id} URL shouldn't announce it.
           Flash to the session rather than the view: the layout reads these
           with session('status') / session('error'), and a View's ->with()
           would only set a view variable and never reach the session. */
        if (session()->get('from_search')) {
            session()->flash('status', __('trans.profile_found'));
        }

        return view('steaminfo', compact('data', 'bans', 'hours'));
    }
}
