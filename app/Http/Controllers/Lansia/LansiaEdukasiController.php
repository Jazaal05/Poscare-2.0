<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\EdukasiContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaEdukasiController extends Controller
{
    public function index()
    {
        return view('lansia.edukasi.index');
    }

    public function list(Request $request)
    {
        $platform = $request->get('platform');
        $category = $request->get('category');

        // Filter hanya konten lansia
        $query = EdukasiContent::query()->where('layanan', 'lansia');
        
        if ($platform) $query->where('platform', $platform);
        if ($category) $query->where('category', $category);

        $data = $query->orderBy('id', 'desc')->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // =============================================
    // STORE — dengan validasi platform ketat
    // =============================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'platform'  => 'required|in:youtube,tiktok,facebook,instagram,article',
            'url'       => 'required|url',
            'title'     => 'nullable|string|max:255',
            'category'  => 'required|in:kesehatan-lansia,pola-hidup-sehat,pencegahan-penyakit,gizi-lansia,tips-lansia',
            'thumbnail' => 'nullable|url',
            'duration'  => 'nullable|string|max:50',
        ]);

        // Validasi platform dan URL harus sesuai
        $this->validatePlatformUrl($data['platform'], $data['url']);

        // Validasi konten publik untuk Facebook dan Instagram
        if (in_array($data['platform'], ['facebook', 'instagram'])) {
            $this->validatePublicContent($data['platform'], $data['url']);
        }

        // Auto-fetch info jika title atau thumbnail kosong
        if (empty($data['title']) || empty($data['thumbnail'])) {
            $fetched = $this->fetchContentInfo($data['platform'], $data['url']);
            if (empty($data['title']))     $data['title']     = $fetched['title']     ?? $data['url'];
            if (empty($data['thumbnail'])) $data['thumbnail'] = $fetched['thumbnail'] ?? null;
        }

        // Title wajib ada (fallback ke URL jika masih kosong)
        if (empty($data['title'])) $data['title'] = $data['url'];

        $data['penulis_id'] = Auth::id();
        $data['layanan']    = 'lansia'; // Selalu lansia
        $edukasi = EdukasiContent::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Konten edukasi lansia berhasil ditambahkan!',
            'data'    => $edukasi,
        ], 201);
    }

    // =============================================
    // FETCH INFO — auto-fetch title & thumbnail
    // =============================================
    public function fetchInfo(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:youtube,tiktok,facebook,instagram,article',
            'url'      => 'required|url',
        ]);

        // Validasi platform dan URL
        $this->validatePlatformUrl($request->platform, $request->url);

        $info = $this->fetchContentInfo($request->platform, $request->url);

        return response()->json([
            'success' => true,
            'data'    => $info,
        ]);
    }

    public function destroy($id)
    {
        $item = EdukasiContent::where('layanan', 'lansia')->findOrFail($id);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Konten berhasil dihapus!']);
    }

    public function show($id)
    {
        $item = EdukasiContent::where('layanan', 'lansia')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = EdukasiContent::where('layanan', 'lansia')->findOrFail($id);
        
        $data = $request->validate([
            'platform'  => 'sometimes|in:youtube,tiktok,facebook,instagram,article',
            'url'       => 'sometimes|url',
            'title'     => 'sometimes|string|max:255',
            'category'  => 'sometimes|in:kesehatan-lansia,pola-hidup-sehat,pencegahan-penyakit,gizi-lansia,tips-lansia',
            'thumbnail' => 'nullable|url',
        ]);

        // Validasi platform dan URL harus sesuai jika keduanya ada
        if (isset($data['platform']) && isset($data['url'])) {
            $this->validatePlatformUrl($data['platform'], $data['url']);
        } elseif (isset($data['platform'])) {
            $this->validatePlatformUrl($data['platform'], $item->url);
        } elseif (isset($data['url'])) {
            $this->validatePlatformUrl($item->platform, $data['url']);
        }

        // Validasi konten publik untuk Facebook dan Instagram
        $platform = $data['platform'] ?? $item->platform;
        $url = $data['url'] ?? $item->url;
        if (in_array($platform, ['facebook', 'instagram'])) {
            $this->validatePublicContent($platform, $url);
        }

        $item->update($data);
        return response()->json(['success' => true, 'message' => 'Konten berhasil diperbarui!']);
    }

    // =============================================
    // PRIVATE: Validate platform and URL match
    // =============================================
    private function validatePlatformUrl(string $platform, string $url): void
    {
        $patterns = [
            'youtube'   => '/(?:youtube\.com|youtu\.be)/i',
            'tiktok'    => '/tiktok\.com/i',
            'facebook'  => '/facebook\.com|fb\.watch/i',
            'instagram' => '/instagram\.com/i',
        ];

        // Artikel tidak perlu validasi khusus
        if ($platform === 'article') {
            return;
        }

        // Cek apakah URL sesuai dengan platform
        if (isset($patterns[$platform]) && !preg_match($patterns[$platform], $url)) {
            $platformNames = [
                'youtube'   => 'YouTube',
                'tiktok'    => 'TikTok',
                'facebook'  => 'Facebook',
                'instagram' => 'Instagram',
            ];
            
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                response()->json([
                    'success' => false,
                    'message' => "Platform {$platformNames[$platform]} tidak sesuai dengan URL yang dimasukkan. Pastikan URL berasal dari {$platformNames[$platform]}.",
                ], 422)
            );
        }
    }

    // =============================================
    // PRIVATE: Validate public content (Facebook & Instagram)
    // =============================================
    private function validatePublicContent(string $platform, string $url): void
    {
        // Cek indikator konten privat di URL
        if ($platform === 'facebook') {
            // Facebook private groups atau private posts biasanya punya pattern tertentu
            if (preg_match('/\/groups\/.*\/permalink/i', $url) || 
                preg_match('/\/permalink\.php/i', $url)) {
                // Ini bisa jadi private, tapi kita biarkan user yang tahu
                // Tidak throw error, hanya warning di frontend
            }
        }

        if ($platform === 'instagram') {
            // Instagram private account tidak bisa diakses via URL publik
            // Kita tidak bisa validasi di backend, hanya warning di frontend
        }

        // Note: Validasi penuh konten publik/privat memerlukan API access token
        // Untuk sekarang, kita hanya memberikan warning di frontend
    }

    // =============================================
    // PRIVATE: Fetch content info (title + thumbnail)
    // =============================================
    private function fetchContentInfo(string $platform, string $url): array
    {
        return match ($platform) {
            'youtube' => $this->fetchYouTubeInfo($url),
            'article' => $this->fetchArticleInfo($url),
            default   => ['title' => '', 'thumbnail' => ''],
        };
    }

    private function fetchYouTubeInfo(string $url): array
    {
        try {
            // Ambil via oEmbed API YouTube
            $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
            $response  = $this->curlGet($oembedUrl);

            $title     = '';
            $thumbnail = '';

            if ($response) {
                $data      = json_decode($response, true);
                $title     = $data['title'] ?? '';
                $thumbnail = $data['thumbnail_url'] ?? '';
            }

            // Coba ambil thumbnail HD dari video ID
            $videoId = $this->extractYouTubeId($url);
            if ($videoId) {
                $thumbnail = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }

            return ['title' => $title, 'thumbnail' => $thumbnail];
        } catch (\Exception $e) {
            return ['title' => '', 'thumbnail' => ''];
        }
    }

    private function fetchArticleInfo(string $url): array
    {
        try {
            $html = $this->curlGet($url);
            if (!$html) return ['title' => '', 'thumbnail' => ''];

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath     = new \DOMXPath($dom);
            $thumbnail = '';
            $title     = '';

            // Ambil title dari <title> tag
            $titleNodes = $xpath->query('//title');
            if ($titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)->nodeValue);
            }

            // Ambil og:title jika ada (lebih akurat)
            $ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
            if ($ogTitle->length > 0) $title = trim($ogTitle->item(0)->nodeValue);

            // Ambil thumbnail: og:image → twitter:image → link[rel=image_src]
            $ogImage = $xpath->query('//meta[@property="og:image"]/@content');
            if ($ogImage->length > 0) {
                $thumbnail = trim($ogImage->item(0)->nodeValue);
            } else {
                $twitterImage = $xpath->query('//meta[@name="twitter:image"]/@content');
                if ($twitterImage->length > 0) {
                    $thumbnail = trim($twitterImage->item(0)->nodeValue);
                }
            }

            // Ubah URL relatif ke absolut
            if ($thumbnail && !preg_match('/^https?:\/\//', $thumbnail)) {
                $parsed    = parse_url($url);
                $thumbnail = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . $thumbnail;
            }

            return ['title' => $title, 'thumbnail' => $thumbnail];
        } catch (\Exception $e) {
            return ['title' => '', 'thumbnail' => ''];
        }
    }

    private function extractYouTubeId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=)([^&\?\/]+)/',
            '/(?:youtu\.be\/)([^&\?\/]+)/',
            '/(?:youtube\.com\/embed\/)([^&\?\/]+)/',
            '/(?:youtube\.com\/shorts\/)([^&\?\/]+)/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) return $matches[1];
        }
        return null;
    }

    private function curlGet(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'PosCareBot/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($response !== false && $httpCode === 200) ? $response : null;
    }
}
