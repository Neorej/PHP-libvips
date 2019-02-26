# PHP-libvips

$fileLocation = '';  
$fileDestination = '';  
$fileExtension = pathinfo($fileLocation, PATHINFO_EXTENSION);  

$vips = Image::newFromFile($fileLocation);  
$vips = fixImageOrientation($vips);  
$vips = fitInBox($vips, 200, 200);  
// OR  
$vips = fillBox($fileLocation, 200, 200);  

// Save to disk  
$vips->writeToFile($fileDestination);

// Output directly  
header('Content-type: image/' . $fileExtension);  
echo $vips->writeToBuffer('.' . $fileExtension);  
// OR  
//return ['image/' . $fileExtension, $vips->writeToBuffer('.' . $fileExtension)];  
