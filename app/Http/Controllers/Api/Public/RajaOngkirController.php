<?php

namespace App\Http\Controllers\Api\Public;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class RajaOngkirController extends Controller
{
    // Base URL RajaOngkir
    private const API_URL = 'https://rajaongkir.komerce.id/api/v1/';

    // ID lokasi asal sesuai kode tutorial
    private const ORIGIN_ID = 46174;

    /**
     * Search Destination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchDestination(Request $request)
    {
        // Validasi input pencarian
        $validated = $request->validate([
            'search' => 'required|string|min:2|max:100',
        ]);

        // Ambil API Key dari konfigurasi
        $apiKey = config('rajaongkir.api_key');

        // Periksa API Key
        if (empty($apiKey)) {
            return response()->json([
                'message' => 'API Key RajaOngkir belum dikonfigurasi.'
            ], 500);
        }

        try {
            // Request ke API RajaOngkir
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->acceptJson()
              ->timeout(30)
              ->get(self::API_URL . 'destination/domestic-destination', [
                  'search' => $validated['search'],
                  'limit' => 100,
                  'offset' => 0
              ]);

            // Ambil response JSON
            $result = $response->json();

            // Periksa jika request gagal
            if (
                !$response->successful() ||
                ($result['meta']['status'] ?? null) === 'error'
            ) {
                return response()->json([
                    'message' => $result['meta']['message']
                        ?? 'Gagal mengambil data tujuan.',
                    'status' => $response->status(),
                    'response' => $result ?? $response->body()
                ], $response->successful() ? 422 : $response->status());
            }

            // Tampilkan data tujuan
            return response()->json($result['data'] ?? []);

        } catch (ConnectionException $e) {

            return response()->json([
                'message' => 'Tidak dapat terhubung ke server RajaOngkir.'
            ], 502);
        }
    }


    /**
     * Check Ongkir
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOngkir(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'destination' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string|max:100',
            'price' => 'nullable|in:lowest,highest',
        ]);

        // Ambil API Key
        $apiKey = config('rajaongkir.api_key');

        // Periksa API Key
        if (empty($apiKey)) {
            return response()->json([
                'message' => 'API Key RajaOngkir belum dikonfigurasi.'
            ], 500);
        }

        try {
            // Data yang dikirim ke RajaOngkir
            $payload = [
                'origin' => self::ORIGIN_ID,
                'destination' => $validated['destination'],
                'weight' => $validated['weight'],
                'courier' => $validated['courier'],
            ];

            // Tambahkan price jika dikirim
            if (!empty($validated['price'])) {
                $payload['price'] = $validated['price'];
            }

            // Request API RajaOngkir
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->asForm()
              ->acceptJson()
              ->timeout(30)
              ->post(self::API_URL . 'calculate/domestic-cost', $payload);

            // Ambil response JSON
            $result = $response->json();

            // Periksa jika request gagal
            if (
                !$response->successful() ||
                ($result['meta']['status'] ?? null) === 'error'
            ) {
                return response()->json([
                    'message' => $result['meta']['message']
                        ?? 'Gagal menghitung ongkos kirim.',
                    'status' => $response->status(),
                    'response' => $result ?? $response->body()
                ], $response->successful() ? 422 : $response->status());
            }

            // Tampilkan data ongkir
            return response()->json($result['data'] ?? []);

        } catch (ConnectionException $e) {

            return response()->json([
                'message' => 'Tidak dapat terhubung ke server RajaOngkir.'
            ], 502);
        }
    }
}
