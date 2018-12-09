<?php

use Jcupitt\Vips\Image;

/**
 * @param Image $vips
 * @return Image
 */
function fixImageOrientation(Image $vips): Image
{
    // Image must actually have an orientation
    if ($vips->typeof('orientation')) {
        // Rotate the image according to the orientation
        switch ($vips->get('orientation')) {
            case 1:
            default:
                break;
            case 2:
                $vips = $vips->fliphor();
                break;
            case 4:
                $vips = $vips->fliphor();
            case 3:
                $vips = $vips->rot180();
                break;
            case 5:
                $vips = $vips->flipver();
            case 6:
                $vips = $vips->rot90();
                break;
            case 7:
                $vips = $vips->flipver();
            case 8:
                $vips = $vips->rot270();
                break;

        }

        // Orientation has been fixed, strip orientation data from the image
        $vips->remove('orientation');
    }

    return $vips;
}

/**
 * Scale to a given width or height, whichever comes first
 *
 * @param Image $vips
 * @param int $width
 * @param int $height
 * @return Image
 */
function fitInBox(Image $vips, int $width, int $height): Image
{
    $widthScale = $width / $vips->width;
    $heightScale = $height / $vips->height;
    $resizeScale = $widthScale > $heightScale ? $heightScale : $widthScale;

    return $vips->resize($resizeScale);
}

/**
 * Scale to a specific width and height, whichever comes first. Then center and fill remaining space with white (jpg) or transparent pixels.
 *
 * @param Image $vips
 * @param int $width
 * @param int $height
 * @param string $fileExtension
 * @return Image
 */
function fitInBoxFill(Image $vips, int $width, int $height, string $fileExtension): Image
{
    $vips = fillBox($vips, $width, $height);

    $background = $fileExtension === 'png'
        ? [0, 0, 0, 0] // Transparent
        : [255]; // White

    // @todo test bands, adding extra band should only be necessary if the image doesn't have 4 bands
    // @todo test 1, 2, 4, 5+ band images?
    $vips = $vips->bandjoin(255);

    return $vips->embed(
        ($width - $vips->width) / 2,
        ($height - $vips->height) / 2,
        $width,
        $height,
        [
            'extend' => 'background',
            'background' => $background
        ]
    );
}

/**
 * Scale image to given width
 *
 * @param Image $vips
 * @param int $width
 * @return Image
 */
function resizeToWidth(Image $vips, int $width): Image
{
    return $vips->resize($width / $vips->width);
}

/**
 * Scale image to given height
 *
 * @param Image $vips
 * @param int $height
 * @return Image
 */
function resizeToHeight(Image $vips, int $height): Image
{
    return $vips->resize($height / $vips->height);
}

/**
 * Scale and then crop the image in the middle
 *
 * @param string $fileLocation
 * @param int $width
 * @param int $height
 * @param bool $autoRotate
 * @return Image
 */
function fillBox(string $fileLocation, int $width, int $height, $autoRotate = true): Image
{
    return Image::thumbnail($fileLocation, $width,
        ['height' => $height, 'crop' => 'centre', 'auto_rotate' => $autoRotate]);;
}
