<?php

namespace ball\file;


use ball\helper\File;
use ball\util\StringUtil;

class Uploader extends UploadHandler
{
    public static $imageVersions = [
        // The empty image version key defines options for the original image:
        File::IMG_VERSION_DEFAULT => [
            // Automatically rotate images based on EXIF meta data:
            'auto_orient' => true
        ],
        File::IMG_VERSION_DESKTOP => [
            // Automatically rotate images based on EXIF meta data:
            'max_width' => 1600,
            'max_height' => 900,
            'square' => true,
        ],
        File::IMG_VERSION_MOBILE => [
//            'crop' => true,
            'max_width' => 640,
            'max_height' => 480,
            'square' => true,
        ],
        File::IMG_VERSION_THUMBNAIL => [
            // Uncomment the following to use a defined directory for the thumbnails
            // instead of a subdirectory based on the version identifier.
            // Make sure that this directory doesn't allow execution of files if you
            // don't pose any restrictions on the type of uploaded files, e.g. by
            // copying the .htaccess file from the files directory for Apache:
            //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
            //'upload_url' => $this->get_full_url().'/thumb/',
            // Uncomment the following to force the max
            // dimensions and e.g. create square thumbnails:
            'crop' => true,
            'max_width' => 200,
            'max_height' => 200
        ]
    ];

    public static $singleImageVersions = [
        // The empty image version key defines options for the original image:
        File::IMG_VERSION_DEFAULT => [
            // Automatically rotate images based on EXIF meta data:
            'auto_orient' => true
        ],
    ];

    protected function get_file_name($file_path, $name, $size, $type, $error,
                                     $index, $content_range)
    {
        if (!empty($this->options['auto']) && $this->options['auto'] == true) {
            $name = time() . StringUtil::generateRandomString(5) . '.' . pathinfo($name, PATHINFO_EXTENSION);
        }
        return parent::get_file_name($file_path, $name, $size, $type, $error,
            $index, $content_range);
    }


    protected function imagick_create_scaled_image($file_name, $version, $options)
    {
        list($file_path, $new_file_path) =
            $this->get_scaled_image_file_paths($file_name, $version);
        $im = new \Imagick();
        $im->readImage($file_path);
        $im->setImageFormat(pathinfo($file_path, PATHINFO_EXTENSION));
        $im->stripImage();
//        $file_path = StringUtil::replaceExtension($file_path, "png");
//        $new_file_path = StringUtil::replaceExtension($new_file_path, "png");
        $im->writeImage($file_path);
        $im->clear();
        $im->destroy();
        $image = $this->imagick_get_image_object(
            $file_path,
            !empty($options['crop']) || !empty($options['no_cache'])
        );
        if ($image->getImageFormat() === 'GIF') {
            // Handle animated GIFs:
            $images = $image->coalesceImages();
            foreach ($images as $frame) {
                $image = $frame;
                $this->imagick_set_image_object($file_name, $image);
                break;
            }
        }
        $image_oriented = false;
        if (!empty($options['auto_orient'])) {
            $image_oriented = $this->imagick_orient_image($image);
        }
        $new_width = $max_width = $img_width = $image->getImageWidth();
        $new_height = $max_height = $img_height = $image->getImageHeight();
        if (!empty($options['max_width'])) {
            $new_width = $max_width = $options['max_width'];
        }
        if (!empty($options['max_height'])) {
            $new_height = $max_height = $options['max_height'];
        }
        if (!($image_oriented || $max_width < $img_width || $max_height < $img_height || !empty($options['strip']))) {
            if ($file_path !== $new_file_path) {
                if (isset($options['square']) && $options['square'] == true) { // 補正方形白邊
                    return $this->squareImage($image, $new_file_path, $options);
                } else {
                    return copy($file_path, $new_file_path);
                }
            }
            return true;
        }
        $crop = !empty($options['crop']);
        if ($crop) {
            $x = 0;
            $y = 0;
            if (($img_width / $img_height) >= ($max_width / $max_height)) {
                $new_width = 0; // Enables proportional scaling based on max_height
                $x = ($img_width / ($img_height / $max_height) - $max_width) / 2;
            } else {
                $new_height = 0; // Enables proportional scaling based on max_width
                $y = ($img_height / ($img_width / $max_width) - $max_height) / 2;
            }
        }
        $success = $image->resizeImage(
            $new_width,
            $new_height,
            isset($options['filter']) ? $options['filter'] : \imagick::FILTER_LANCZOS,
            isset($options['blur']) ? $options['blur'] : 1,
            $new_width && $new_height // fit image into constraints if not to be cropped
        );
        if ($success && $crop) {
            $success = $image->cropImage(
                $max_width,
                $max_height,
                $x,
                $y
            );
            if ($success) {
                $success = $image->setImagePage($max_width, $max_height, 0, 0);
            }
        }
        $type = strtolower(substr(strrchr($file_name, '.'), 1));
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                if (!empty($options['jpeg_quality'])) {
                    $image->setImageCompression(\imagick::COMPRESSION_JPEG);
                    $image->setImageCompressionQuality($options['jpeg_quality']);
                }
                break;
        }
        if (!empty($options['strip'])) {
            $image->stripImage();
        }

        if (isset($options['square']) && $options['square'] == true) {
            return $success && $this->squareImage($image, $new_file_path, $options);
        } else {
            return $success && $image->writeImage($new_file_path);
        }
    }

    /**
     * 補正方形白邊
     * @param \Imagick $image
     * @param string $new_file_path
     * @param array $options
     * @return bool
     * @throws \ImagickException
     */
    private function squareImage(\Imagick $image, string $new_file_path, array $options): bool
    {
        $max_width = $img_width = $image->getImageWidth();
        $max_height = $img_height = $image->getImageHeight();
        if (!empty($options['max_width'])) {
            $max_width = $options['max_width'];
        }
        if (!empty($options['max_height'])) {
            $max_height = $options['max_height'];
        }
        if ($img_width > $img_height) {
            $col = $max_width;
            $row = $img_height * $max_width / $img_width;
        } else {
            $row = $max_height;
            $col = $img_width * $max_height / $img_height;
        }
        $image->scaleImage($col, $row);
        $img = new \Imagick();
        $img->newImage($options['max_width'], $options['max_height'], new \ImagickPixel('transparent'));

        $img->setImageFormat(pathinfo($new_file_path, PATHINFO_EXTENSION));
        $img->compositeImage($image, \Imagick::COMPOSITE_DEFAULT,
            (intval($options['max_width']) / 2) - intval($col) / 2, (intval($options['max_height']) / 2) - intval($row) / 2);
        return $img->writeImage($new_file_path);
    }
}