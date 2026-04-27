<?php

namespace App\Http\Controllers;

use App\Models\EdukasiContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EdukasiController extends Controller
{
    public function index()
    {
        return view('edukasi.index');
    }

    public function list(Request $request)
    {
        $platform = $request->get('platform');
        $category = $request->get('category');
        $layanan  = $request->get('layanan', 'balita');

        $query = EdukasiContent::query()->where('layanan', $layanan);
        if ($platform) $query->where('platform', $platform);
        if ($category) $query->where('category', $category);

        $data = $query->orderBy('id', 'desc')->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // =============================================
    // STORE — dengan auto-fetch YouTube/artikel info
    // Menggantikan: fetchYouTubeInfo() + fetchArticleThumbnail() di native
    // =============================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'platform'  => 'required|in:youtube,tiktok,facebook,instagram,article',
            'url'       => 'required|url',
            'title'     => 'nullable|string|max:255',
            'category'  => 'required|in:gizi,tumbuh-kembang,kesehatan,imunisasi,tips,kesehatan-lansia,pola-hidup-sehat,pencegahan-penyakit,gizi-lansia,tips-lansia',
            'thumbnail' => 'nullable|url',
            'duration'  => 'nullable|string|max:50',
            'layanan'   => 'nullable|in:balita,lansia',
        ]);

        // Auto-fetch info jika title atau thumbnail kosong
        if (empty($data['title']) || empty($data['thumbnail'])) {
            $fetched = $this->fetchContentInfo($data['platform'], $data['url']);
            if (empty($data['title']))     $data['title']     = $fetched['title']     ?? $data['url'];
            if (empty($data['thumbnail'])) $data['thumbnail'] = $fetched['thumbnail'] ?? null;
        }

        // Title wajib ada (fallback ke URL jika masih kosong)
        if (empty($data['title'])) $data['title'] = $data['url'];

        $data['penulis_id'] = Auth::id();
        $data['layanan']    = $data['layanan'] ?? 'balita';
        $edukasi = EdukasiContent::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Konten edukasi berhasil ditambahkan!',
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

        $info = $this->fetchContentInfo($request->platform, $request->url);

        return response()->json([
            'success' => true,
            'data'    => $info,
        ]);
    }

    public function destroy($id)
    {
        EdukasiContent::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Konten berhasil dihapus!']);
    }

    public function show($id)
    {
        $item = EdukasiContent::findOrFail($id);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = EdukasiContent::findOrFail($id);
        $data = $request->validate([
            'platform'  => 'sometimes|in:youtube,tiktok,facebook,instagram,article',
            'url'       => 'sometimes|url',
            'title'     => 'sometimes|string|max:255',
            'category'  => 'sometimes|in:gizi,tumbuh-kembang,kesehatan,imunisasi,tips,kesehatan-lansia,pola-hidup-sehat,pencegahan-penyakit,gizi-lansia,tips-lansia',
            'thumbnail' => 'nullable|url',
        ]);
        $item->update($data);
        return response()->json(['success' => true, 'message' => 'Konten berhasil diperbarui!']);
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
