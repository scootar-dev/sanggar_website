<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SanggarProfile extends Model {
    protected $table = 'sanggar_profile';
    protected $fillable = [
        'nama_sanggar','tagline','sejarah','visi','misi','tahun_berdiri',
        'alamat','no_hp','email','instagram','facebook','youtube',
        'foto_profil','foto_sejarah',
        'jumlah_anggota','jumlah_penghargaan','jumlah_event',
    ];
    protected $casts = ['misi' => 'array'];

    public function socialUrl(string $platform): ?string
    {
        $value = trim((string) $this->getAttribute($platform));
        if ($value === '') return null;
        $domain = ['instagram' => 'instagram.com', 'facebook' => 'facebook.com', 'youtube' => 'youtube.com'][$platform] ?? null;
        if (!$domain) return null;
        if (preg_match('~^(?:www\.)?'.preg_quote($domain, '~').'[/]~i', $value)) {
            $value = 'https://' . $value;
        } elseif (!preg_match('~^https?://~i', $value)) {
            if (!preg_match('~^[@a-zA-Z0-9_./-]+$~', $value)) return null;
            $path = ltrim($value, '@/');
            if ($platform === 'youtube' && !str_contains($path, '/')) {
                $path = str_starts_with($path, 'UC') ? 'channel/'.$path : '@'.$path;
            }
            $value = 'https://'.$domain.'/'.$path;
        }
        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        if ($host !== $domain && !str_ends_with($host, '.'.$domain)) return null;
        return filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
    }

    /** Ambil satu-satunya row profil, buat jika belum ada */
    public static function getInstance(): static {
        return static::firstOrCreate([], [
            'nama_sanggar'       => 'Sanggar Mulya Bhakti',
            'tagline'            => 'Melestarikan Budaya Melalui Seni',
            'sejarah'            => 'Diisi sejarah sanggar...',
            'visi'               => 'Menjadi sanggar seni tari tradisional terdepan di Indonesia.',
            'misi'               => ['Melestarikan seni tari tradisional Indramayu.'],
            'tahun_berdiri'      => '2005',
            'jumlah_anggota'     => 200,
            'jumlah_penghargaan' => 49,
            'jumlah_event'       => 100,
        ]);
    }
}
