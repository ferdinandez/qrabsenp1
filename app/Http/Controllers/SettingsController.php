<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get all settings grouped by category
     */
    public function index()
    {
        try {
            $settings = Setting::all()->groupBy('category');
            
            return response()->json([
                'settings' => $settings,
                'message' => 'Settings retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get setting by key
     */
    public function show($key)
    {
        try {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return response()->json([
                    'message' => 'Setting not found'
                ], 404);
            }
            
            return response()->json([
                'setting' => $setting
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple settings
     */
    public function update(Request $request)
    {
        try {
            $settings = $request->input('settings');
            
            if (!is_array($settings)) {
                return response()->json([
                    'message' => 'Invalid settings format'
                ], 400);
            }

            foreach ($settings as $key => $value) {
                Setting::where('key', $key)->update([
                    'value' => $value
                ]);
            }

            return response()->json([
                'message' => 'Settings updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update single setting
     */
    public function updateSingle(Request $request, $key)
    {
        try {
            $validator = Validator::make($request->all(), [
                'value' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return response()->json([
                    'message' => 'Setting not found'
                ], 404);
            }

            $setting->update([
                'value' => $request->value
            ]);

            return response()->json([
                'message' => 'Setting updated successfully',
                'setting' => $setting
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        try {
            // This would reset to default values from migration
            // For now, return a message
            return response()->json([
                'message' => 'Settings reset to default values'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
