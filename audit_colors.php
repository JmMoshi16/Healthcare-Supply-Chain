<?php
$dirs = ['app/Views', 'public/assets'];
$hits = [];
foreach($dirs as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach($it as $file) {
        if($file->isFile() && in_array($file->getExtension(), ['php','css'])) {
            $content = file_get_contents($file);
            if(strpos($content, '#f0481c') !== false || strpos($content, '#d63d15') !== false || strpos($content, '#1a1a1a') !== false) {
                $hits[] = $file->getPathname();
            }
        }
    }
}
if(count($hits) > 0) {
    echo "Files still containing old retail colors:\n";
    foreach($hits as $h) echo "  - $h\n";
} else {
    echo "Clean! No old retail color refs found.\n";
}
