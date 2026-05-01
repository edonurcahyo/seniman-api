<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    // Get all services
    public function index()
    {
        $services = Layanan::orderBy('id_layanan', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    // Get single service
    public function show($id)
    {
        $service = Layanan::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    // Create new service
    public function store(Request $request)
    {
        $request->validate([
            'kode_layanan' => 'required|string|max:10|unique:layanan',
            'nama_layanan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('layanan', 'public');
        }

        $service = Layanan::create([
            'kode_layanan' => $request->kode_layanan,
            'nama_layanan' => $request->nama_layanan,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status ?? 'aktif',
            'gambar' => $gambarPath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil ditambahkan',
            'data' => $service
        ]);
    }

    // Update service
    public function update(Request $request, $id)
    {
        $service = Layanan::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $request->validate([
            'kode_layanan' => 'required|string|max:10|unique:layanan,kode_layanan,' . $id . ',id_layanan',
            'nama_layanan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $updateData = [
            'kode_layanan' => $request->kode_layanan,
            'nama_layanan' => $request->nama_layanan,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status ?? $service->status
        ];

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($service->gambar && Storage::disk('public')->exists($service->gambar)) {
                Storage::disk('public')->delete($service->gambar);
            }
            $updateData['gambar'] = $request->file('gambar')->store('layanan', 'public');
        }

        $service->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil diupdate',
            'data' => $service
        ]);
    }

    // Delete service
    public function destroy($id)
    {
        $service = Layanan::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        // Hapus gambar jika ada
        if ($service->gambar && Storage::disk('public')->exists($service->gambar)) {
            Storage::disk('public')->delete($service->gambar);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}