<?php

function getThumbnail($logo): string
{
    return pathinfo($logo, PATHINFO_DIRNAME) . "/" . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);
}

?>
