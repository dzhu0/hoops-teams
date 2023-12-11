<?php

function file_upload_path($original_filename, $upload_subfolder_name = "logos"): string
{
    $path_segments = [$upload_subfolder_name, basename($original_filename)];
    return join("/", $path_segments);
}

function file_type_is_valid($temporary_path, $new_path): bool
{
    $allowed_file_extensions = ["jpg", "jpeg", "png"];
    $allowed_mime_types = ["image/jpeg", "image/png"];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = mime_content_type($temporary_path);

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

function resize_image($path, $width, $height, $name = "")
{
    $image = new \Gumlet\ImageResize($path);
    $image->resize($width, $height, true);
    $image->save(pathinfo($path, PATHINFO_DIRNAME) . "/" . pathinfo($path, PATHINFO_FILENAME) . "{$name}." . pathinfo($path, PATHINFO_EXTENSION));
}

function deleteLogo($logo)
{
    $thumbnail = pathinfo($logo, PATHINFO_DIRNAME) . "/" . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);
    unlink($thumbnail);
    unlink($logo);
}

?>
