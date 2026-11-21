<?php

namespace App\Http\Controllers;

class PublicFileController extends Controller
{
    public function show(string $path)
    {
        $root = realpath(storage_path('app/public'));
        $file = realpath(storage_path('app/public/' . $path));
        abort_unless($root && $file && is_file($file) && str_starts_with($file, $root . DIRECTORY_SEPARATOR), 404);

        return response()->file($file);
    }
}
