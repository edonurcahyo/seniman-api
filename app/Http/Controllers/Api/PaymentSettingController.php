<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    // Get payment setting by cabang
    public function index(Request $request)
    {
        $cabangId = $request->input('cabang_id', 1);
        
        $setting = PaymentSetting::where('cabang_id', $cabangId)->first();
        
        if (!$setting) {
            // Return default empty structure
            return response()->json([
                'success' => true,
                'data' => [
                    'cabang_id' => $cabangId,
                    'bank_bca' => null,
                    'bank_mandiri' => null,
                    'bank_bni' => null,
                    'bank_bri' => null,
                    'qr_code' => null
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }
    
    // Update or create payment setting
    public function update(Request $request)
    {
        $request->validate([
            'cabang_id' => 'required|exists:cabang,id_cabang',
            'bank_bca' => 'nullable|string',
            'bank_mandiri' => 'nullable|string',
            'bank_bni' => 'nullable|string',
            'bank_bri' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        
        $setting = PaymentSetting::where('cabang_id', $request->cabang_id)->first();
        
        $data = [
            'bank_bca' => $request->bank_bca,
            'bank_mandiri' => $request->bank_mandiri,
            'bank_bni' => $request->bank_bni,
            'bank_bri' => $request->bank_bri
        ];
        
        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR if exists
            if ($setting && $setting->qr_code) {
                Storage::disk('public')->delete($setting->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('payment_qr', 'public');
        }
        
        if ($setting) {
            $setting->update($data);
        } else {
            $data['cabang_id'] = $request->cabang_id;
            $setting = PaymentSetting::create($data);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan pembayaran berhasil disimpan',
            'data' => $setting
        ]);
    }
}