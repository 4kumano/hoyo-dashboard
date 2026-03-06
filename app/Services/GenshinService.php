<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GenshinService
{
    protected HoyolabService $hoyolabService;

    public function __construct(HoyolabService $hoyolabService)
    {
        $this->hoyolabService = $hoyolabService;
    }

    /**
     * Get Genshin Game Record Card from Hoyolab API
     * Returns the array containing stats data and basic info.
     */
    public function getGenshinGameRecordCard(?string $cookie): array
    {
        if (empty($cookie)) {
            return ['retcode' => -1, 'message' => 'Memuat data...'];
        }

        $userInfo = $this->hoyolabService->getUserFullInfo($cookie);
        $uid = $userInfo['hoyolab_uid'] ?? null;

        if (!$uid) {
            return ['retcode' => -1, 'message' => 'Gagal mengambil UID Hoyolab (getUserFullInfo).'];
        }

        $headers = [
            'Cookie' => $cookie,
            'x-rpc-lang' => 'en-us',
            'x-rpc-language' => 'en-us',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->get("https://sg-public-api.hoyolab.com/event/game_record/card/wapi/getGameRecordCard?uid={$uid}");

            $data = $response->json();

            if (isset($data['message']) && $data['message'] === 'OK') {
                $list = $data['data']['list'] ?? [];
                // Cari data khusus untuk Genshin Impact (game_id = 2)
                foreach ($list as $game) {
                    if (isset($game['game_id']) && $game['game_id'] == 2) {
                        return [
                            'retcode' => 0,
                            'message' => 'OK',
                            'stats' => $game['data'] ?? [],
                            'nickname' => $game['nickname'] ?? '',
                            'level' => $game['level'] ?? '',
                            'region_name' => $game['region_name'] ?? '',
                            'game_role_id' => $game['game_role_id'] ?? '',
                            'bg' => $game['background_image'] ?? '',
                        ];
                    }
                }
                return ['retcode' => -1, 'message' => 'Akun Genshin Impact tidak ditemukan pada profil Hoyolab ini.'];
            }

            return [
                'retcode' => $data['retcode'] ?? -1,
                'message' => $data['message'] ?? 'Terjadi kesalahan dari API Hoyolab.',
            ];

        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Genshin Game Record Index from Hoyolab API
     * Includes role, avatars, stats, city_explorations, world_explorations.
     */
    public function getGameRecordIndex(?string $cookie, string $role_id): array
    {
        if (empty($cookie) || empty($role_id)) {
            return ['retcode' => -1, 'message' => 'Parameter tidak valid.'];
        }

        try {
            $server = $this->hoyolabService->recognizeServer($role_id);
        } catch (\Exception $e) {
            return ['retcode' => -1, 'message' => 'UID tidak valid.'];
        }

        $headers = [
            'Cookie' => $cookie,
            'x-rpc-lang' => 'en-us',
            'x-rpc-language' => 'en-us',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->get("https://sg-public-api.hoyolab.com/event/game_record/genshin/api/index", [
                    'avatar_list_type' => 0,
                    'server' => $server,
                    'role_id' => $role_id,
                ]);

            $data = $response->json();

            if (isset($data['message']) && $data['message'] === 'OK') {
                return [
                    'retcode' => 0,
                    'message' => 'OK',
                    'data' => $data['data'] ?? [],
                ];
            }

            return [
                'retcode' => $data['retcode'] ?? -1,
                'message' => $data['message'] ?? 'Terjadi kesalahan dari API Hoyolab.',
            ];

        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Event Calendar from Hoyolab API
     * Includes Event Wishes and Events Overview.
     */
    public function getEventCalendar(?string $cookie, string $role_id): array
    {
        if (empty($cookie) || empty($role_id)) {
            return ['retcode' => -1, 'message' => 'Parameter tidak valid.'];
        }

        try {
            $server = $this->hoyolabService->recognizeServer($role_id);
        } catch (\Exception $e) {
            return ['retcode' => -1, 'message' => 'UID tidak valid.'];
        }

        $headers = [
            'Cookie' => $cookie,
            'x-rpc-lang' => 'en-us',
            'x-rpc-language' => 'en-us',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->post("https://sg-public-api.hoyolab.com/event/game_record/genshin/api/act_calendar", [
                    'server' => $server,
                    'role_id' => $role_id,
                ]);

            $data = $response->json();

            if (isset($data['message']) && $data['message'] === 'OK') {
                return [
                    'retcode' => 0,
                    'message' => 'OK',
                    'data' => $data['data'] ?? [],
                ];
            }

            return [
                'retcode' => $data['retcode'] ?? -1,
                'message' => $data['message'] ?? 'Terjadi kesalahan dari API Hoyolab.',
            ];

        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Character Detail from Hoyolab API
     * Returns equipment and detailed stats for a specific character.
     */
    public function getCharacterDetail(?string $cookie, string $role_id, int $character_id): array
    {
        if (empty($cookie) || empty($role_id) || empty($character_id)) {
            return ['retcode' => -1, 'message' => 'Parameter tidak valid.'];
        }

        try {
            $server = $this->hoyolabService->recognizeServer($role_id);
        } catch (\Exception $e) {
            return ['retcode' => -1, 'message' => 'UID tidak valid.'];
        }

        $headers = [
            'Cookie' => $cookie,
            'x-rpc-lang' => 'en-us',
            'x-rpc-language' => 'en-us',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->post("https://sg-public-api.hoyolab.com/event/game_record/genshin/api/character/detail", [
                    'server' => $server,
                    'role_id' => $role_id,
                    'character_ids' => [$character_id],
                ]);

            $data = $response->json();

            if (isset($data['message']) && $data['message'] === 'OK') {
                $characterData = $data['data']['list'][0] ?? [];
                
                if (isset($data['data']['property_map'])) {
                    $characterData['icon_stats'] = $this->IconStats($data['data']['property_map']);
                }

                return [
                    'retcode' => 0,
                    'message' => 'OK',
                    'data' => $characterData,
                ];
            }

            return [
                'retcode' => $data['retcode'] ?? -1,
                'message' => $data['message'] ?? 'Terjadi kesalahan dari API Hoyolab.',
            ];

        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Map Property Icons
     */
    public function IconStats(array $propertyMap): array
    {
        $icons = [];
        foreach ($propertyMap as $id => $prop) {
            if (isset($prop['property_type'])) {
                $icons[$prop['property_type']] = [
                    'property_type' => $prop['property_type'],
                    'name' => $prop['name'] ?? 'Unknown',
                    'icon' => $prop['icon'] ?? '',
                ];
            }
        }
        return $icons;
    }
}
