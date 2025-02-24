<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected $supabaseUrl;
    protected $serviceKey;
    protected $bucket;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');
        $this->bucket = env('SUPABASE_STORAGE_BUCKET');
    }

    public function upload($file)
    {
        if (!$file) return null;

        $filename = Str::uuid() . '-' . $file->getClientOriginalName();
        
        // Read binary file contents
        $fileContents = file_get_contents($file->getRealPath());

        // Upload using binary-safe encoding
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Content-Type'  => $file->getMimeType(),
            'Cache-Control' => 'public, max-age=31536000',
        ])->withBody($fileContents, $file->getMimeType())
          ->put("$this->supabaseUrl/storage/v1/object/$this->bucket/uploads/$filename");

        if ($response->failed()) {
            throw new \Exception('Upload failed: ' . $response->body());
        }

        return $filename; // Save file path in database
    }

    public function getFileUrl($path)
    {
        return "$this->supabaseUrl/storage/v1/object/public/$this->bucket/uploads/$path";
    }
}
