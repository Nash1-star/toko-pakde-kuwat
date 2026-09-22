<?php

$dir = __DIR__ . '/app/Models/';
$files = glob($dir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (!str_contains($content, '$guarded')) {
        $content = str_replace(
            "class " . basename($file, '.php') . " extends Model\n{",
            "class " . basename($file, '.php') . " extends Model\n{\n    protected \$guarded = [];\n",
            $content
        );
        
        // Handle User model which extends Authenticatable
        if (basename($file) === 'User.php') {
            $content = str_replace(
                "class User extends Authenticatable\n{",
                "class User extends Authenticatable\n{\n    protected \$guarded = [];\n",
                $content
            );
        }
        
        file_put_contents($file, $content);
    }
}
echo "Models updated successfully.";

