<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $data) {
            SystemSetting::set($key, $data['value']);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from your Congregation Management System.', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('SMTP Connection Test');
            });

            return back()->with('success', 'Test email sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: '.$e->getMessage());
        }
    }

    public function footerEdit()
    {
        // Authorization: SETTINGS_MANAGE permission or SUPER_ADMIN
        $this->authorize('manage', SystemSetting::class);

        // Load all footer settings with defaults
        $footerSettings = [
            'footer_description' => SystemSetting::where('key', 'footer_description')->first() 
                ?? (object)['key' => 'footer_description', 'value' => 'Supporting our community with grace and efficiency. Managing member records, events, and reports for the congregation.'],
            'footer_address' => SystemSetting::where('key', 'footer_address')->first() 
                ?? (object)['key' => 'footer_address', 'value' => '123 Congregation Ave, City, Country'],
            'footer_email' => SystemSetting::where('key', 'footer_email')->first() 
                ?? (object)['key' => 'footer_email', 'value' => 'contact@congregation.org'],
            'footer_copyright' => SystemSetting::where('key', 'footer_copyright')->first() 
                ?? (object)['key' => 'footer_copyright', 'value' => '&copy; ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.'],
        ];

        return view('settings.footer', compact('footerSettings'));
    }

    public function footerUpdate(Request $request)
    {
        // Authorization: SETTINGS_MANAGE permission or SUPER_ADMIN
        $this->authorize('manage', SystemSetting::class);

        $validated = $request->validate([
            'footer_description' => 'required|string|max:500',
            'footer_address' => 'required|string|max:255',
            'footer_email' => 'required|email|max:255',
            'footer_copyright' => 'required|string|max:255',
            'footer_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg,ico|max:2048', // 2MB max
            'remove_logo' => 'nullable|in:0,1',
        ]);

        // Handle logo upload
        if ($request->hasFile('footer_logo')) {
            $oldLogo = SystemSetting::get('footer_logo_path');
            
            // Delete old logo if exists and not default
            if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                // Don't delete if it's in the default images folder
                if (!str_starts_with($oldLogo, 'images/')) {
                    \Storage::disk('public')->delete($oldLogo);
                }
            }

            $file = $request->file('footer_logo');
            $extension = $file->getClientOriginalExtension();
            
            // Process image: resize and convert to WebP for optimization
            if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg'])) {
                try {
                    // Create image from uploaded file
                    $image = match(strtolower($extension)) {
                        'png' => imagecreatefrompng($file->getRealPath()),
                        'jpg', 'jpeg' => imagecreatefromjpeg($file->getRealPath()),
                        default => null
                    };

                    if ($image) {
                        // Get original dimensions
                        $origWidth = imagesx($image);
                        $origHeight = imagesy($image);
                        
                        // Max size for logo (512px for quality, smaller file)
                        $maxSize = 512;
                        $ratio = min($maxSize / $origWidth, $maxSize / $origHeight);
                        
                        // Calculate new dimensions
                        $newWidth = (int)($origWidth * $ratio);
                        $newHeight = (int)($origHeight * $ratio);
                        
                        // Create new image with transparency
                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        
                        // Preserve transparency for PNG
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                        imagefill($resized, 0, 0, $transparent);
                        
                        // Resize
                        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                        
                        // Save as WebP
                        $filename = 'footer/' . uniqid() . '.webp';
                        $fullPath = storage_path('app/public/' . $filename);
                        
                        // Ensure directory exists
                        if (!file_exists(dirname($fullPath))) {
                            mkdir(dirname($fullPath), 0755, true);
                        }
                        
                        imagewebp($resized, $fullPath, 90); // 90% quality
                        
                        // Clean up
                        imagedestroy($image);
                        imagedestroy($resized);
                        
                        // Store path
                        SystemSetting::set('footer_logo_path', $filename);
                    } else {
                        // Fallback: store original file
                        $path = $file->store('footer', 'public');
                        SystemSetting::set('footer_logo_path', $path);
                    }
                } catch (\Exception $e) {
                    // Fallback: store original file if conversion fails
                    $path = $file->store('footer', 'public');
                    SystemSetting::set('footer_logo_path', $path);
                }
            } else {
                // For SVG, ICO, WebP: store as-is
                $path = $file->store('footer', 'public');
                SystemSetting::set('footer_logo_path', $path);
            }
            
        } elseif ($request->input('remove_logo') === '1') {
            // Remove logo
            $oldLogo = SystemSetting::get('footer_logo_path');
            if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                // Don't delete if it's in the default images folder
                if (!str_starts_with($oldLogo, 'images/')) {
                    \Storage::disk('public')->delete($oldLogo);
                }
            }
            SystemSetting::set('footer_logo_path', null);
        }

        // Update other footer settings
        foreach (['footer_description', 'footer_address', 'footer_email', 'footer_copyright'] as $key) {
            if (isset($validated[$key])) {
                SystemSetting::set($key, $validated[$key]);
            }
        }

        return back()->with('success', __('Footer settings updated successfully.'));
    }
}
