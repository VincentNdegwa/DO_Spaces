<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // Max 10MB
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file was uploaded'
                ], 400);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            
            // Sanitize filename
            $fileName = str_replace(' ', '-', $fileName);
            $fileName = preg_replace('/[^A-Za-z0-9\-_.]/', '', $fileName);
            
            // Create unique filename
            $uniqueName = $fileName . '_' . time() . '.' . $extension;
            
            $path = Storage::disk('do')->putFileAs(
                'uploads',
                $file,
                $uniqueName,
                ['visibility' => 'public']
            );
            
            if (!$path) {
                throw new \Exception('Failed to upload file to storage');
            }

            $url = Storage::disk('do')->url($path);
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url,
                'filename' => $uniqueName
            ], 200);

        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
