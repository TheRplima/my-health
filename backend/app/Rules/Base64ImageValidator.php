<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64ImageValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $explode = $this->explodeString($value);
        $allow = $this->allowedFormat();
        $format = $this->dataFormat($explode);
        $size = $this->dataSize($value);

        // check file format
        if (!in_array($format, $allow)) {
            $fail("The $attribute must be a file of type: " . implode(', ', $allow));
        }

        // check base64 format
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            $fail("The $attribute must be a valid base64 image");
        }

        // check file size
        if (!$size) {
            $fail("The $attribute must be less than 4MB");
        }
    }

    /**
     * Check the data size
     */
    public function dataSize($value)
    {
        // Decode the image
        $decodedImage = base64_decode($value);

        // Get image size in kilobytes
        $imageSize = strlen($decodedImage) / 1024;

        // Check if image is below max size
        return $imageSize <= 4096;
    }

    /**
     * Check the data format
     */
    public function dataFormat($explode)
    {

        $format = str_replace(
            [
                'data:image/',
                'image:data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '', '',
            ],
            $explode[0]
        );

        return $format;
    }

    /**
     * The allowed format in base 64 image
     */
    public function allowedFormat()
    {
        return ['gif', 'jpg', 'jpeg', 'png'];
    }

    /**
     * Explode base 64 image
     */
    public function explodeString($value)
    {
        return explode(',', $value);
    }
}
