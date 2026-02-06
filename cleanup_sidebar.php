<?php

$directory = __DIR__ . '/resources/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$regex = '/@section\(\'sidebar\'\)(.*?)@endsection/s';

echo "Starting cleanup in: $directory\n";

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());

        // Check if file contains the sidebar section
        if (preg_match($regex, $content)) {
            echo "Cleaning file: " . $file->getFilename() . "\n";
            $newContent = preg_replace($regex, "", $content);
            $newContent = trim($newContent); // Clean up leading/trailing whitespace

            if (file_put_contents($file->getPathname(), $newContent) !== false) {
                echo " - Success\n";
            } else {
                echo " - Failed to write\n";
            }
        }
    }
}

echo "Cleanup complete.\n";
