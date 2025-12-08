<?php

$componentsDir = 'resources/views/components';
$viewsDir = 'resources/views';

$components = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($componentsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        // Convert path to component tag
        // e.g., resources/views/components/ui/button.blade.php -> x-ui.button
        $relativePath = str_replace($componentsDir . '/', '', $path);
        $relativePath = str_replace('.blade.php', '', $relativePath);
        $tag = 'x-' . str_replace('/', '.', $relativePath);
        $components[$tag] = $path;
    }
}

echo "Checking " . count($components) . " components...\n";

$unused = [];

foreach ($components as $tag => $path) {
    // Grep for the tag in the views directory
    // We look for "<x-tag" or ":x-tag" (dynamic) or "@component('components.tag')"
    // Simplified check: just the tag name
    $command = "grep -r --include='*.blade.php' '$tag' $viewsDir | wc -l";
    $count = (int) shell_exec($command);
    
    if ($count === 0) {
        // Double check for class-based usage if applicable (not doing that deep check here for simplicity)
        $unused[] = $tag;
    }
}

if (empty($unused)) {
    echo "All components appear to be used.\n";
} else {
    echo "Potentially unused components:\n";
    foreach ($unused as $tag) {
        echo "- $tag (" . $components[$tag] . ")\n";
    }
}
