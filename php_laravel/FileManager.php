<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Image;

class FileManager
{

  public const MAX_IMAGE_SIZE_KB  = 5000;
  public const MAX_IMAGE_WIDTH  = 1000;
  public const ALLOWED_MIME_TYPE = 'jpeg,bmp,png,webp,wbmp';
  public const CANNOT_BE_DELETED = 'default_1_1200_800.jpg,default_2_1200_800.jpg,default_3_1200_400.jpg';

  /**
   * Upload File
   *
   * @param \Illuminate\Http\UploadedFile $file
   * @param string $disk
   *
   * @return string
   */
  public function upload($file, $disk='s3')
  {
      if(!$file->isValid()) {
          return null;
      }

      $allowed_mime_types = ['image/jpeg', 'image/png', 'image/bmp', 'image/webp', 'image/pjpeg', 'image/vnd.wap.wbmp'];
      $size = $file->getClientSize()*0.001;
      $mime = $file->getClientMimeType();

      if (!in_array($mime, $allowed_mime_types) || $size > self::MAX_IMAGE_SIZE_KB) {
          return null;
      }

      $img = Image::make($file);

      // Read Exif meta data from current image
      if($img->exif()) {
          //To use you need to install Imagick driver
          //$this->remove_exif($img);
      }

      if($img->width() > self::MAX_IMAGE_WIDTH) {
          $img->resize(self::MAX_IMAGE_WIDTH, null, function ($constraint) {
                $constraint->aspectRatio();
          });
      }

      // Generate filepath
      $dir = $this->generateFolderName() . '/' . substr(md5(microtime()), mt_rand(0, 30), 2);
      $filename = md5($file->getClientOriginalName()) . time();
      $filepath = $dir . '/' . $filename . '.' . $file->getClientOriginalExtension();

      // Put file in Storage
      $isPut = Storage::disk($disk)->put($filepath, $img->encode());
      return ($isPut) ? $filepath : $isPut;
  }

  private function generateFolderName()
  {
      $forbidden_folder_name = ['ad'];
      $folder = substr(md5(microtime()), mt_rand(0, 30), 2);
      if(in_array($folder, $forbidden_folder_name)) {
          return substr(md5(microtime()), mt_rand(0, 30), 2);
      }
      return $folder;
  }

  public function delete($url, $disk='s3')
  {
      $cannot_be_deleted = explode(',', self::CANNOT_BE_DELETED);
      $allow = true;
      foreach($cannot_be_deleted as $item) {
        if(stripos($url, $item)) {
          $allow = false;
        }
      }
      if($allow) {
        $domen = env('AWS_URL');
        Storage::disk($disk)->delete(str_replace($domen, '', $url));
      }
  }

  /**
   * Get full url image
   *
   * @param string $path path to image
   * @param string $disc Storage disk
   * @return string
   */
  public function url($path, $disk='s3')
  {
      return Storage::disk($disk)->url($path);
  }

  /**
   * Get path to crop Image
   *
   * Resizes the image to fill the width
   * and height boundaries and crops any excess image data
   * https://glide.thephpleague.com/1.0/api/crop/
   *
   * @param string $path path to image
   * @param string $size necessary width and height (example: '200,300')
   * @param string $pos set where the image is cropped by adding a crop position.
   *               Accepts crop-top-left, crop-top, crop-top-right, crop-left, crop-center, crop-right, crop-bottom-left, crop-bottom or crop-bottom-right
   * @param string $disc Storage disk
   * @return string
   */
  public function crop($path, $size='', $pos='crop', $disk='s3')
  {
      $size_arr = explode(',', str_replace(' ', '', $size));
      if(count($size_arr) !== 2) {
          return Storage::disk($disk)->url($path);
      }
      $params = [
          'path' => $path,
          'w'    => $size_arr[0],
          'h'    => $size_arr[1],
          'fit'  => $pos
      ];
      return route('image', $params);
  }

  /**
   * Remove EXIF data
   *
   * Returns the current image in core format of the particular driver
   * and remove exif data.
   * Need a library to use ImageMagick
   * https://imagemagick.org/index.php
   * Need to install Imagick driver for Intervention\Image
   * http://image.intervention.io/getting_started/configuration
   *
   * @param Image $img
   * @return Image
   */
  private function remove_exif($img)
  {
      $imagick = $img->getCore();
      $profiles = $img->getImageProfiles("icc", true);
      $img->stripImage();
      if(!empty($profiles)) {
          $img->profileImage("icc", $profiles['icc']);
      }
      return $img;
  }

}
