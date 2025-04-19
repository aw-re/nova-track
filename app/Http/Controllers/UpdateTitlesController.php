<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class UpdateTitlesController extends Controller
{
    /**
     * Update all blade template titles from CPMS to NovaTrack
     */
    public function updateTitles()
    {
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);
        $count = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                
                // Replace title sections
                $updatedContent = preg_replace("/@section\('title', '(.*?) - CPMS'\)/", "@section('title', '$1 - ".__('app.app_name')."')", $content);
                
                if ($content !== $updatedContent) {
                    File::put($file->getPathname(), $updatedContent);
                    $count++;
                }
            }
        }

        return "Updated $count files with new title format.";
    }
}
