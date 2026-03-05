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
        $uid = $userInfo['uid'] ?? null;

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
}
