<?php

// Define a function to generate the file upload path for a given original filename and subfolder
function file_upload_path($original_filename, $upload_subfolder_name = "logos"): string
{
    // Combine the subfolder name and the basename of the original filename to form the upload path
    $path_segments = [$upload_subfolder_name, basename($original_filename)];
    return join("/", $path_segments);
}

// Define a function to check if the file type is valid based on file extension and MIME type
function file_type_is_valid($temporary_path, $new_path): bool
{
    // Define arrays of allowed file extensions and MIME types
    $allowed_file_extensions = ["jpg", "jpeg", "png"];
    $allowed_mime_types = ["image/jpeg", "image/png"];

    // Get the actual file extension and MIME type of the file
    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = mime_content_type($temporary_path);

    // Check if the actual file extension and MIME type are in the allowed lists
    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    // Return true if both file extension and MIME type are valid
    return $file_extension_is_valid && $mime_type_is_valid;
}

// Define a function to resize an image to a specified width and height
function resize_image($path, $width, $height, $name = "")
{
    // Create a new ImageResize instance with the provided path
    $image = new \Gumlet\ImageResize($path);

    // Resize the image to the specified width and height, maintaining the aspect ratio
    $image->resize($width, $height, true);

    // Save the resized image with an optional name appended to the filename
    $image->save(pathinfo($path, PATHINFO_DIRNAME) . "/" . pathinfo($path, PATHINFO_FILENAME) . "{$name}." . pathinfo($path, PATHINFO_EXTENSION));
}

// Define a function to delete a logo file and its associated thumbnail
function deleteLogo($logo)
{
    // Generate the path for the associated thumbnail file
    $thumbnail = pathinfo($logo, PATHINFO_DIRNAME) . "/" . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);

    // Delete both the thumbnail and the original logo file
    unlink($thumbnail);
    unlink($logo);
}

?>
