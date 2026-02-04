<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::withCount('serviceDetails')->latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|string|max:255',
            'service_details' => 'nullable|array',
            'details' => 'nullable|array',
            'details.*.name' => 'required|string|max:255',
            'details.*.has_work_days' => 'required|boolean',
            'details.*.work_days' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'service_type' => $validated['service_type'],
                'service_details' => $validated['service_details'] ?? null,
            ]);

            if (isset($validated['details']) && is_array($validated['details'])) {
                foreach ($validated['details'] as $detail) {
                    $service->serviceDetails()->create([
                        'name' => $detail['name'],
                        'has_work_days' => $detail['has_work_days'],
                        'work_days' => $detail['has_work_days'] ? ($detail['work_days'] ?? null) : null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('services.index')->with('success', 'تم إضافة الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الخدمة');
        }
    }

    public function show(Service $service)
    {
        $service->load('serviceDetails');
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $service->load('serviceDetails');
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|string|max:255',
            'service_details' => 'nullable|array',
            'details' => 'nullable|array',
            'details.*.name' => 'required|string|max:255',
            'details.*.has_work_days' => 'required|boolean',
            'details.*.work_days' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $service->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'service_type' => $validated['service_type'],
                'service_details' => $validated['service_details'] ?? null,
            ]);

            $service->serviceDetails()->delete();

            if (isset($validated['details']) && is_array($validated['details'])) {
                foreach ($validated['details'] as $detail) {
                    $service->serviceDetails()->create([
                        'name' => $detail['name'],
                        'has_work_days' => $detail['has_work_days'],
                        'work_days' => $detail['has_work_days'] ? ($detail['work_days'] ?? null) : null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('services.index')->with('success', 'تم تحديث الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الخدمة');
        }
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'تم حذف الخدمة بنجاح');
    }
}
