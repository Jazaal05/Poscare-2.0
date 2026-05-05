<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\EdukasiLansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaEdukasiController extends Controller
{
    // Mapping platform value dari form → format model
    private const PLATFORM_MAP = [
        'youtube'   => 'Youtube',
        'tiktok'    => 'Tiktok',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'article'   => 'Artikel',
        // Sudah PascalCase (dari form baru)
        'Youtube'   => 'Youtube',
        'Tiktok'    => 'Tiktok',
        'Facebook'  => 'Facebook',
        'Instagram' => 'Instagram',
        'Artikel'   => 'Artikel',
    ];

    public function index()
    {
        return view('lansia.edukasi.index');
    }

    // ── API: List konten ───────────────────────────────────────
    public function list(Request $request)
    {
        $query = EdukasiLansia::aktif();

        if ($request->platform) {
            $platform = self::PLATFORM_MAP[$request->platform] ?? $request->platform;
            $query->where('platform', $platform);
        }
        if ($request->category) {
            $query->where('kategori', $request->category);
        }

        $data = $query->orderBy('id', 'desc')->get()->map(fn($e) => $this->formatItem($e));

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── API: Show single ───────────────────────────────────────
    public function show($id)
    {
        $item = EdukasiLansia::findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->formatItem($item)]);
    }

    // ── API: Store ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'platform'  => 'required|string',
            'url'       => 'required|url',
            'title'     => 'nullable|string|max:255',
            'category'  => 'nullable|string|max:100',
            'thumbnail' => 'nullable|url',
        ]);

        // Normalisasi platform ke PascalCase
        $platform = self::PLATFORM_MAP[$data['platform']] ?? ucfirst(strtolower($data['platform']));

        // Validasi URL sesuai platform
        if (!EdukasiLansia::validateUrlForPlatform($data['url'], $platform)) {
            return response()->json([
                'success' => false,
                'message' => EdukasiLansia::getValidationMessage($platform),
            ], 422);
        }

        // Auto-fetch title & thumbnail jika kosong
        if (empty($data['title']) || empty($data['thumbnail'])) {
            $fetched = $this->fetchContentInfo($platform, $data['url']);
            if (empty($data['title']))     $data['title']     = $fetched['title']     ?? '';
            if (empty($data['thumbnail'])) $data['thumbnail'] = $fetched['thumbnail'] ?? null;
        }

        // Fallback title ke URL
        if (empty($data['title'])) $data['title'] = $data['url'];

        // Normalisasi kategori
        $kategori = $this->normalizeKategori($data['category'] ?? '');

        $edukasi = EdukasiLansia::create([
            'judul'      => $data['title'],
            'platform'   => $platform,
            'tautan'     => $data['url'],
            'thumbnail'  => $data['thumbnail'] ?? null,
            'kategori'   => $kategori,
            'dibuat_oleh'=> Auth::id(),
            'is_active'  => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konten edukasi berhasil ditambahkan!',
            'data'    => $this->formatItem($edukasi),
        ], 201);
    }

    // ── API: Update ────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $item = EdukasiLansia::findOrFail($id);

        $data = $request->validate([
            'platform'  => 'sometimes|string',
            'url'       => 'sometimes|url',
            'title'     => 'sometimes|string|max:255',
            'category'  => 'nullable|string|max:100',
            'thumbnail' => 'nullable|url',
        ]);

        // Tentukan platform dan URL final (gabungan data baru + data lama)
        $platform = isset($data['platform'])
            ? (self::PLATFORM_MAP[$data['platform']] ?? ucfirst(strtolower($data['platform'])))
            : $item->platform;

        $url = $data['url'] ?? $item->tautan;

        // Validasi URL harus sesuai platform — sama ketatnya dengan store()
        if (!EdukasiLansia::validateUrlForPlatform($url, $platform)) {
            return response()->json([
                'success' => false,
                'message' => EdukasiLansia::getValidationMessage($platform),
            ], 422);
        }

        $update = [];
        if (isset($data['platform'])) $update['platform']  = $platform;
        if (isset($data['url']))      $update['tautan']     = $url;
        if (isset($data['title']))    $update['judul']      = $data['title'];
        if (isset($data['category'])) $update['kategori']   = $this->normalizeKategori($data['category']);
        if (array_key_exists('thumbnail', $data)) $update['thumbnail'] = $data['thumbnail'];

        $item->update($update);

        return response()->json(['success' => true, 'message' => 'Konten berhasil diperbarui!', 'data' => $this->formatItem($item->fresh())]);
    }

    // ── API: Delete ────────────────────────────────────────────
    public function destroy($id)
    {
        $item = EdukasiLansia::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Konten berhasil dihapus!']);
    }

    // ── API: Fetch info (auto-fill title & thumbnail) ──────────
    public function fetchInfo(Request $request)
    {
        $request->validate([
            'platform' => 'required|string',
            'url'      => 'required|url',
        ]);

        $platform = self::PLATFORM_MAP[$request->platform] ?? ucfirst(strtolower($request->platform));

        if (!EdukasiLansia::validateUrlForPlatform($request->url, $platform)) {
            return response()->json([
                'success' => false,
                'message' => EdukasiLansia::getValidationMessage($platform),
            ], 422);
        }

        $info = $this->fetchContentInfo($platform, $request->url);

        return response()->json(['success' => true, 'data' => $info]);
    }

    // ── Helper: format item untuk response ────────────────────
    private function formatItem(EdukasiLansia $e): array
    {
        return [
            'id'        => $e->id,
            'title'     => $e->judul,
            'judul'     => $e->judul,
            'url'       => $e->tautan,
            'tautan'    => $e->tautan,
            'platform'  => strtolower($e->platform === 'Artikel' ? 'article' : $e->platform),
            'platform_label' => $e->platform,
            'category'  => $e->kategori,
            'kategori'  => $e->kategori,
            'thumbnail' => $e->thumbnail,
            'is_active' => $e->is_active,
            'created_at'=> $e->created_at?->format('d/m/Y'),
        ];
    }

    // ── Helper: normalisasi kategori ──────────────────────────
    private function normalizeKategori(string $raw): string
    {
        $map = [
            'kesehatan-lansia'    => 'Kesehatan Lansia',
            'pola-hidup-sehat'    => 'Pola Hidup Sehat',
            'pencegahan-penyakit' => 'Pencegahan Penyakit',
            'gizi-lansia'         => 'Gizi Lansia',
            'olahraga-lansia'     => 'Olahraga Lansia',
            'tips-lansia'         => 'Tips Lansia',
            'lainnya'             => 'Lainnya',
        ];
        return $map[strtolower($raw)] ?? ($raw ?: 'Kesehatan Lansia');
    }

    // ── Helper: fetch title & thumbnail dari URL ──────────────
    private function fetchContentInfo(string $platform, string $url): array
    {
        return match ($platform) {
            'Youtube' => $this->fetchYouTubeInfo($url),
            'Artikel' => $this->fetchArticleInfo($url),
            default   => ['title' => '', 'thumbnail' => ''],
        };
    }

    private function fetchYouTubeInfo(string $url): array
    {
        try {
            $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
            $response  = $this->curlGet($oembedUrl);
            $title     = '';
            $thumbnail = '';

            if ($response) {
                $data      = json_decode($response, true);
                $title     = $data['title'] ?? '';
                $thumbnail = $data['thumbnail_url'] ?? '';
            }

            // Thumbnail HD dari video ID
            if (preg_match('/(?:v=|youtu\.be\/)([^&?\/]+)/', $url, $m)) {
                $thumbnail = "https://img.youtube.com/vi/{$m[1]}/maxresdefault.jpg";
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

            $xpath = new \DOMXPath($dom);
            $title = $thumbnail = '';

            $t = $xpath->query('//meta[@property="og:title"]/@content');
            if ($t->length > 0) $title = trim($t->item(0)->nodeValue);
            if (!$title) {
                $t = $xpath->query('//title');
                if ($t->length > 0) $title = trim($t->item(0)->nodeValue);
            }

            $img = $xpath->query('//meta[@property="og:image"]/@content');
            if ($img->length > 0) $thumbnail = trim($img->item(0)->nodeValue);

            return ['title' => $title, 'thumbnail' => $thumbnail];
        } catch (\Exception $e) {
            return ['title' => '', 'thumbnail' => ''];
        }
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
