<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationSetting;
use Illuminate\Http\Request;

class EvaluasiSettingController extends Controller
{
    /**
     * Halaman Settings Bobot & Threshold
     */
    public function settings()
    {
        $settings = EvaluationSetting::getSettings();
        return view('admin.evaluasi.settings', compact('settings'));
    }

    /**
     * Update Settings Bobot & Threshold
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'weight_delivery' => 'required|numeric|min:0|max:100',
            'weight_quality' => 'required|numeric|min:0|max:100',
            'weight_quantity' => 'required|numeric|min:0|max:100',
            'weight_complain' => 'required|numeric|min:0|max:100',
            'weight_incoming_material' => 'required|numeric|min:0|max:100',
            'weight_safety_environment' => 'required|numeric|min:0|max:100',
            'threshold_baik' => 'required|numeric|min:0|max:100',
            'threshold_cukup' => 'required|numeric|min:0|max:100',
        ]);

        $totalWeight = $request->weight_delivery + $request->weight_quality + $request->weight_quantity
                     + $request->weight_complain + $request->weight_incoming_material + $request->weight_safety_environment;

        if (abs($totalWeight - 100.0) > 0.01) {
            return redirect()->back()->with('error', "Total bobot 6 aspek harus tepat 100% (saat ini: {$totalWeight}%).");
        }

        $settings = EvaluationSetting::getSettings();
        $settings->update($request->all());

        return redirect()->route('admin.evaluasi.settings')->with('success', "Pengaturan bobot & threshold evaluasi berhasil diperbarui.");
    }
}
