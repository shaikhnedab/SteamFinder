<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use SteamID;
use Steam as Steam2;

class SteamController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'steamid' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please enter a valid search input.');
        }

        $id = $request->steamid;

        $id = $this->normalizeInput($id);

        $szID = $this->VanityURL($id);
        if ($szID !== null) {
            return redirect('/'.$szID)->with('status', 'Profile Found!');
        }

        try {
            $s = new SteamID($id);

            if ($s->IsValid()) {
                $tempid = $s->ConvertToUInt64();
                return redirect('/'.$tempid)->with('status', 'Profile Found!');
            }
        } catch (\InvalidArgumentException $e) {
            // fall through to error redirect
        }

        return redirect()->back()->with('error', 'Failed to get data, please check the ID!');
    }

    /**
     * Normalize various Steam URL / ID formats to a clean identifier.
     */
    private function normalizeInput(string $input): string
    {
        $input = trim($input);

        // Strip common schemes
        $input = preg_replace('#^https?://#i', '', $input);

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

        if (!is_array($user) || !isset($user['response']) || !is_array($user['response'])) {
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
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        if (!$s->IsValid()) {
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        $apikey = config('steam-api.steamApiKey');

        if (empty($apikey)) {
            return redirect('/')->with('error', 'Steam API key is not configured.');
        }

        try {
            $response = Http::timeout(10)
                ->withOptions(['connect_timeout' => 5])
                ->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
                    'key' => $apikey,
                    'steamids' => $id,
                ]);

            $data = $response->json();
        } catch (\Throwable $e) {
            return redirect('/')->with('error', 'Failed to connect to Steam API. Please try again later.');
        }

        if (!is_array($data) || !isset($data['response']) || !is_array($data['response'])) {
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        $players = $data['response']['players'] ?? [];

        if (empty($players[0])) {
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        $id64temp = $players[0]['steamid'] ?? null;

        if ($id64temp === null) {
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        try {
            $s = new SteamID($id64temp);
        } catch (\InvalidArgumentException $e) {
            return redirect('/')->with('error', 'Failed to get data, please check the ID!');
        }

        $data['si64'] = $players[0]['steamid'];
        $data['cvs'] = $players[0]['communityvisibilitystate'] ?? "";
        $data['prs'] = $players[0]['profilestate'] ?? "";
        $data['pn'] = $players[0]['personaname'] ?? "";
        $data['purl'] = $players[0]['profileurl'] ?? "";
        $data['av'] = $players[0]['avatar'] ?? "";
        $data['avm'] = $players[0]['avatarmedium'] ?? "";
        $data['avf'] = $players[0]['avatarfull'] ?? "";
        $data['avhash'] = $players[0]['avatarhash'] ?? "";
        $data['ps'] = $players[0]['personastate'] ?? "";
        $data['rn'] = $players[0]['realname'] ?? "";
        $data['pcid'] = $players[0]['primaryclanid'] ?? "";
        $data['createdat'] = $players[0]['timecreated'] ?? "";
        $data['psf'] = $players[0]['personastateflags'] ?? "";

        $data['steam3'] = $s->RenderSteam3();
        $data['steam32'] = $s->RenderSteam2();
        $data['account_id'] = $s->GetAccountID();
        $data['invite_url'] = $s->RenderSteamInvite();
        $data['profile2'] = 'https://steamcommunity.com/profiles/'.$data['si64'].'/';

        // Fetch player bans with caching (only successful responses are cached)
        $bans = Cache::get("steam.bans.$id64temp");
        if ($bans === null) {
            try {
                $pbans = Steam2::user($id64temp)->GetPlayerBans()[0];
                $bans = [
                    'cb' => $pbans->CommunityBanned,
                    'vb' => $pbans->VACBanned,
                    'novb' => $pbans->NumberOfVACBans,
                    'dslb' => $pbans->DaysSinceLastBan,
                    'nogb' => $pbans->NumberOfGameBans,
                    'eb' => $pbans->EconomyBan,
                ];
                Cache::put("steam.bans.$id64temp", $bans, 600);
            } catch (\Throwable $e) {
                $bans = [
                    'cb' => '',
                    'vb' => '',
                    'novb' => '',
                    'dslb' => '',
                    'nogb' => '',
                    'eb' => '',
                ];
            }
        }

        // Fetch CS:GO hours with caching (only successful responses are cached)
        $hours = Cache::get("steam.hours.$id64temp");
        if ($hours === null) {
            $hours = [];
            try {
                $games = Steam2::player($id64temp)->GetOwnedGames(false, false, [730]);
                if ($games && count($games) > 0) {
                    foreach ($games as $h) {
                        if ($h->appId == 730) {
                            $hours['csgo'] = $h;
                            break;
                        }
                    }
                }
                Cache::put("steam.hours.$id64temp", $hours, 600);
            } catch (\Throwable $e) {
                // Return empty result on failure (not cached)
            }
        }

        return view('steaminfo', compact('data', 'bans', 'hours'));
    }
}
