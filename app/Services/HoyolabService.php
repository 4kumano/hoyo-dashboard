<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HoyolabService
{
    protected string $baseUrl = 'https://api-os-takumi.hoyoverse.com';
    
    public function getUserFullInfo(?string $cookie): array
    {
        if (empty($cookie)) {
            return [];
        }
        
        try {
            $response = Http::withHeaders([
                'Cookie' => $cookie,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'origin' => 'https://act.hoyolab.com',
                'Referer' => 'https://act.hoyolab.com/',
                'x-rpc-lang' => 'en-us',
                'x-rpc-language' => 'en-us',
            ])->get('https://bbs-api-os.hoyolab.com/community/user/wapi/getUserFullInfo?gid=2');

            $data = $response->json();
            // dd($data);

            if (isset($data['message']) && $data['message'] === 'OK') {
                $userInfo = [
                    'hoyolab_uid' => $data['data']['user_info']['uid'] ?? null,
                    'nickname' => $data['data']['user_info']['nickname'] ?? null,
                    'avatar'=>$data['data']['user_info']['avatar_url'] ?? 'https://ui-avatars.com/api/?name=Commander&background=2563eb&color=fff',
                ];
                return $userInfo;
            }
        } catch (\Exception $e) {
            // handle error
        }

        return [];
    }
    // Game Specific API Routes (Base URL for Announcements, Events, etc)
    const HK4E_URL = 'https://sg-hk4e-api.hoyoverse.com/common/hk4e_global/'; // Genshin Impact
    const NAP_URL  = 'https://sg-announcement-static.hoyoverse.com/common/nap_global/'; // Zenless Zone Zero (ZZZ)
    const HKRPG_URL = 'https://sg-hkrpg-api.hoyoverse.com/common/hkrpg_global/'; // Honkai: Star Rail

    /**
     * Validate the cookie and get the user's game roles.
     * Required cookies: ltoken_v2, ltuid_v2 (or ltoken, ltuid)
     */
    public function loginWithCookie(string $cookie): array
    {
        try {
            $response = Http::withHeaders([
                'Host' => 'api-account-os.hoyolab.com',
                'Accept' => 'application/json, text/plain, */*',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Cookie' => $cookie,
            ])->get('https://api-account-os.hoyolab.com/binding/api/getUserGameRolesByCookie');

            $data = $response->json();

            if (!is_array($data)) {
                return [
                    'retcode' => -1,
                    // If it's returning HTML, just show generic error.
                    'message' => 'Layanan HoYoLAB sedang tidak dapat diakses atau Cookie Anda ditolak oleh server (Status: ' . $response->status() . ').',
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Gets the official game icon URL based on game code.
     * 
     * @param string $gameBiz
     * @return string
     */
    public function getIcon(string $gameBiz): string
    {
        if (str_contains($gameBiz, 'hk4e')) {
            return 'https://img-os-static.hoyolab.com/communityWeb/upload/1d7dd8f33c5ccdfdeac86e1e86ddd652.png'; // Genshin
        } elseif (str_contains($gameBiz, 'hkrpg')) {
            return 'https://img-os-static.hoyolab.com/communityWeb/upload/473afd1250b71ba470744aa240f6d638.png'; // Honkai: Starrail
        } elseif (str_contains($gameBiz, 'bh3')) {
            return 'https://img-os-static.hoyolab.com/communityWeb/upload/bbb364aaa7d51d168c96aaa6a1939cba.png'; // Honkai Impact 3rd
        } elseif (str_contains($gameBiz, 'nap')) {
            return 'https://img-os-static.hoyolab.com/communityWeb/upload/30ef8803324044b40ccbc69973da9848.png'; // ZZZ
        }
        
        // Default placeholder icon jika tidak ada kecocokan
        return 'https://ui-avatars.com/api/?name=Game&background=1e293b&color=fff';
    }

    /**
     * Gets the latest news/announcements for a specific game code.
     * 
     * @param string $gameBiz
     * @param string|int $uid
     * @param string $region
     * @param int $level
     * @param string $lang
     * @return array
     */
    public function getNews(string $gameBiz, $uid = 0, string $region = '', int $level = 1, string $lang = 'id-id'): array
    {
        $url = '';
        $params = [
            'game_biz' => $gameBiz,
            'bundle_id' => $gameBiz,
            'platform' => 'pc',
            'region' => $region,
            'uid' => $uid,
            'level' => $level,
            'lang' => $lang,
        ];

        if (str_contains($gameBiz, 'hk4e')) {
            $params['game'] = 'hk4e';
            $url = self::HK4E_URL . 'announcement/api/getAnnList';
        } elseif (str_contains($gameBiz, 'nap')) {
            $params['game'] = 'nap';
            $url = self::NAP_URL . 'announcement/api/getAnnList';
        } elseif (str_contains($gameBiz, 'hkrpg')) {
            $params['game'] = 'hkrpg';
            $params['channel_id'] = 1;
            $url = self::HKRPG_URL . 'announcement/api/getAnnList';
        } else {
            return []; // Unrecognized game config
        }

        try {
            $response = Http::get($url, $params);
            $data = $response->json();
            // dd($data);
            
            $newsList = [];
            
            // Format struktur respon getAnnList dari Hoyoverse
            $listData = $data['data']['list'] ?? [];
            if (!empty($listData) && is_array($listData)) {
                foreach ($listData as $item) {
                    // Cek apakah data dibungkus kategori (mengandung array 'list' di dalamnya)
                    if (isset($item['list']) && is_array($item['list'])) {
                        $tag = $item['type_label'] ?? 'Berita';
                        // Tentukan warna tag berdasar nama kategori
                        $tagColor = str_contains(strtolower($tag), 'event') ? 'text-purple-400' : 'text-blue-400';
                        $bg = str_contains(strtolower($tag), 'event') ? 'from-purple-900/40' : 'from-blue-900/40';

                        foreach ($item['list'] as $ann) {
                            $newsList[] = [
                                'game' => $gameBiz,
                                'tag'  => $tag,
                                'tag_color' => $tagColor,
                                'time' => isset($ann['start_time']) ? \Carbon\Carbon::parse($ann['start_time'])->diffForHumans() : 'Baru saja',
                                'title' => $ann['title'] ?? 'Tanpa Judul',
                                'desc' => $ann['subtitle'] ?? 'Baca selengkapnya di dalam game.',
                                'banner' => $ann['banner'] ?? '',
                                'bg' => $bg,
                            ];
                        }
                    } else {
                        // Data langsung berupa array of announcements (tanpa kategori)
                        $contentStripped = strip_tags($item['content'] ?? '');
                        // Gunakan subtitle, jika kosong ambil potongan text dari content
                        $desc = !empty($item['subtitle']) ? $item['subtitle'] : ($contentStripped ? mb_substr($contentStripped, 0, 100) . '...' : 'Baca selengkapnya di dalam game.');

                        $newsList[] = [
                            'game' => $gameBiz,
                            'tag'  => 'Berita',
                            'tag_color' => 'text-emerald-400',
                            'time' => 'Baru saja', // Struktur ini kebetulan tak punya start_time di sample
                            'title' => $item['title'] ?? 'Tanpa Judul',
                            'desc' => $desc,
                            'banner' => $item['banner'] ?? '',
                            'bg' => 'from-emerald-900/40',
                        ];
                    }
                }
            }
            // dd($newsList);
            return $newsList;
            
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Converts epoch timestamp to date string.
     * 
     * @param string|int $epoch
     * @param string $format
     * @return string
     */
    public function epochconverter($epoch, string $format = 'd M Y, H:i'): string
    {
        if (empty($epoch)) {
            return '-';
        }
        return \Carbon\Carbon::createFromTimestamp($epoch)->format($format);
    }
}
