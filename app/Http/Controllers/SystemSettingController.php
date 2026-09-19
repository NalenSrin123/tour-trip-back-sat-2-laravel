<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SystemSettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SystemSetting::query()->orderBy('setting_id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $setting = SystemSetting::create($this->validatedData($request));

        return response()->json($setting, 201);
    }

    public function show(SystemSetting $setting): JsonResponse
    {
        return response()->json($setting);
    }

    public function update(Request $request, SystemSetting $setting): JsonResponse
    {
        $setting->update($this->validatedData($request, $setting));

        return response()->json($setting->fresh());
    }

    public function destroy(SystemSetting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json(status: 204);
    }

    private function validatedData(Request $request, ?SystemSetting $setting = null): array
    {
        return $request->validate([
            'setting_key' => [
                $setting ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('system_settings', 'setting_key')->ignore($setting?->setting_id, 'setting_id'),
            ],
            'setting_value' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}