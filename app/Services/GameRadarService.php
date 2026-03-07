<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameRadarService
{
    /**
     * Game ID to GamesRadar URL mapping.
     */
    protected array $urls = [
        2 => 'https://www.gamesradar.com/genshin-impact-codes-redeem/',
        6 => 'https://www.gamesradar.com/honkai-star-rail-codes-redeem/',
        8 => 'https://www.gamesradar.com/games/action-rpg/zenless-zone-zero-codes/',
    ];

    /**
     * Fetch and parse active redeem codes from GamesRadar.
     *
     * @param int $gameId  Game ID (2 = Genshin Impact, 6 = Honkai: Star Rail, 8 = Zenless Zone Zero)
     * @return array{retcode: int, message: string, codes?: array<array{code: string, rewards: string}>}
     */
    public function parseByGameRadar(int $gameId = 2): array
    {
        $url = $this->urls[$gameId] ?? null;

        if (!$url) {
            return [
                'retcode' => -1,
                'message' => "Game ID {$gameId} tidak didukung.",
            ];
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])->get($url);

            if (!$response->successful()) {
                return [
                    'retcode' => -1,
                    'message' => 'Gagal mengambil halaman: HTTP ' . $response->status(),
                ];
            }

            return $this->parseHtml($response->body());

        } catch (\Exception $e) {
            return [
                'retcode' => -1,
                'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse the HTML content to extract redeem codes.
     *
     * Finds div#article-body, collects all <ul> elements,
     * then iterates every <li> looking for all-uppercase <strong> tags as codes.
     */
    protected function parseHtml(string $html): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        libxml_clear_errors();

        // Find div with id "article-body"
        $divNodes = $xpath->query("//div[@id='article-body']");
        if ($divNodes->length === 0) {
            return [
                'retcode' => -1,
                'message' => 'Tidak dapat menemukan konten halaman (div#article-body).',
            ];
        }

        $div = $divNodes->item(0);

        // Find ALL <ul> inside that div
        $ulNodes = $xpath->query('.//ul', $div);
        if ($ulNodes->length === 0) {
            return [
                'retcode' => -1,
                'message' => 'Tidak dapat menemukan daftar kode (ul).',
            ];
        }

        $codes = [];

        // Collect all <li> from all <ul>
        foreach ($ulNodes as $ul) {
            $liNodes = $xpath->query('.//li', $ul);
            foreach ($liNodes as $li) {
                // Find <strong> inside this <li>
                $strongNodes = $xpath->query('.//strong', $li);
                if ($strongNodes->length === 0) {
                    continue;
                }

                $strongText = trim($strongNodes->item(0)->textContent);

                // Skip if not all uppercase (not a valid code)
                if ($strongText === '' || $strongText !== strtoupper($strongText) || !preg_match('/^[A-Z0-9]+$/', $strongText)) {
                    continue;
                }

                // Split on "/" and take the first part (clean up trailing paths)
                $code = trim(explode('/', $strongText, 2)[0]);

                // Extract rewards: full text split by "–" (en-dash) or "-"
                $fullText = trim($li->textContent);
                $rewards = '';
                $parts = preg_split('/\s*[–—]\s*/u', $fullText, 2);
                if (count($parts) >= 2) {
                    $rewards = trim($parts[1]);
                }

                if (!empty($code)) {
                    $codes[] = [
                        'code' => $code,
                        'rewards' => $rewards,
                    ];
                }
            }
        }

        return [
            'retcode' => 0,
            'message' => 'OK',
            'codes' => $codes,
        ];
    }
}
