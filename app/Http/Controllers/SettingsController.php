<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $wpsMaxPercentage       = Setting::get('wps_max_percentage', 70);
        $acceptedSalaryDelayDays = Setting::get('accepted_salary_delay_days', 0);

        return view('settings.index', compact('wpsMaxPercentage', 'acceptedSalaryDelayDays'));
    }

    public function updateWpsPercentage(Request $request)
    {
        $validated = $request->validate([
            'wps_max_percentage' => 'required|numeric|min:0|max:100'
        ]);

        try {
            Setting::set(
                'wps_max_percentage',
                $validated['wps_max_percentage'],
                'decimal',
                'Maximum allowed percentage for Wage Protection System (WPS)'
            );

            \Log::info('Settings: WPS percentage updated', [
                'new_value' => $validated['wps_max_percentage'],
                'user_id' => auth()->id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث نسبة WPS بنجاح',
                    'wps_max_percentage' => (float) $validated['wps_max_percentage']
                ]);
            }

            return redirect()->route('settings.index')
                ->with('success', 'تم تحديث نسبة WPS بنجاح');

        } catch (\Exception $e) {
            \Log::error('Settings: Failed to update WPS percentage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('settings.index')
                ->with('error', 'حدث خطأ أثناء التحديث');
        }
    }

    public function getWpsPercentage()
    {
        try {
            $wpsMaxPercentage = Setting::get('wps_max_percentage', 70);

            return response()->json([
                'success' => true,
                'wps_max_percentage' => (float) $wpsMaxPercentage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSalaryDelayDays(Request $request)
    {
        $validated = $request->validate([
            'accepted_salary_delay_days' => 'required|integer|min:0|max:365'
        ]);

        try {
            Setting::set(
                'accepted_salary_delay_days',
                $validated['accepted_salary_delay_days'],
                'integer',
                'عدد أيام التأخير المسموح بها بعد نهاية الشهر قبل احتساب التأخير في صرف الرواتب'
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث أيام التأخير المسموح بها بنجاح',
                    'accepted_salary_delay_days' => (int) $validated['accepted_salary_delay_days']
                ]);
            }

            return redirect()->route('settings.index')
                ->with('success', 'تم تحديث أيام التأخير المسموح بها بنجاح');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('settings.index')
                ->with('error', 'حدث خطأ أثناء التحديث');
        }
    }
}
