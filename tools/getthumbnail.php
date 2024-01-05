<?php

// Define a function to generate the path for a thumbnail based on the provided logo path
function getThumbnail($logo): string
{
    // Combine the directory, filename (without extension), "_thumbnail", and the extension to form the thumbnail path
    return pathinfo($logo, PATHINFO_DIRNAME) . "/" . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);
}

?>
