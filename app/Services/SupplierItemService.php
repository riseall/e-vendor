<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierItemService
{
    /**
     *
     * @param string $keyword
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function searchItems(string $keyword, int $page = 1, int $limit = 20): array
    {
        $apiBase = rtrim(config('services.syncmaster.api_url'), '/');
        $apiKey  = config('services.syncmaster.key');

        try {
            $response = Http::timeout(10)->withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept'    => 'application/json',
            ])->get("{$apiBase}/api/wsa/item-mstr", [
                'search' => $keyword,
                'page'   => $page,
                'limit'  => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];

                return [
                    'items'   => $data['items'] ?? [],
                    'hasMore' => (bool) ($data['hasMore'] ?? false),
                ];
            }

            Log::warning('SyncMaster WSA item-mstr failed response', [
                'status' => $response->status(),
                'body'   => $response->json() ?? $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('SyncMaster WSA item-mstr unreachable: ' . $e->getMessage());
        }

        return ['items' => [], 'hasMore' => false];
    }
}
