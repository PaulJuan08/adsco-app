<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    /**
     * Stream PDF in browser with proper headers to prevent Chrome blocking
     */
    public function view($encryptedFilename)
    {
        // Decrypt the filename for security
        try {
            $filename = Crypt::decrypt($encryptedFilename);
            $filename = basename($filename); // Prevent directory traversal
            // Clean filename
            $filename = preg_replace('/[^a-zA-Z0-9_\-\s\.\(\)]/', '', $filename);
        } catch (\Exception $e) {
            Log::error('PDF decryption failed: ' . $e->getMessage());
            abort(404, 'Invalid PDF file.');
        }
        
        // Check multiple possible locations
        $possiblePaths = [
            public_path('pdf/' . $filename),
            public_path('pdfs/' . $filename),
            public_path('storage/pdf/' . $filename),
            public_path('storage/pdfs/' . $filename),
        ];
        
        $foundPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $foundPath = $path;
                Log::info('PDF found at: ' . $path);
                break;
            }
        }
        
        if (!$foundPath) {
            Log::error('PDF not found: ' . $filename . ' in locations: ' . implode(', ', $possiblePaths));
            abort(404, 'PDF file not found.');
        }
        
        // Get the file content
        $content = file_get_contents($foundPath);
        
        // Return with proper headers to prevent Chrome blocking
        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Content-Length', filesize($foundPath))
            ->header('Accept-Ranges', 'bytes')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cross-Origin-Resource-Policy', 'same-origin')
            ->header('Content-Security-Policy', "frame-ancestors 'self'");
    }
    
    /**
     * Download PDF file
     */
    public function download($encryptedFilename)
    {
        // Decrypt the filename for security
        try {
            $filename = Crypt::decrypt($encryptedFilename);
            $filename = basename($filename);
            $filename = preg_replace('/[^a-zA-Z0-9_\-\s\.\(\)]/', '', $filename);
        } catch (\Exception $e) {
            Log::error('PDF download decryption failed: ' . $e->getMessage());
            abort(404, 'Invalid PDF file.');
        }
        
        // Check multiple possible locations
        $possiblePaths = [
            public_path('pdf/' . $filename),
            public_path('pdfs/' . $filename),
            public_path('storage/pdf/' . $filename),
            public_path('storage/pdfs/' . $filename),
        ];
        
        $foundPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $foundPath = $path;
                break;
            }
        }
        
        if (!$foundPath) {
            abort(404, 'PDF file not found.');
        }
        
        return response()->download($foundPath, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}