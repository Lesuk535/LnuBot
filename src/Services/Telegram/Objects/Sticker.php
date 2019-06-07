<?php


namespace App\Services\Telegram\Objects;


/**
 * Class Sticker.
 *
 *
 * @method string       getFileId()     Unique identifier for this file.
 * @method int          getWidth()      Sticker width.
 * @method int          getHeight()     Sticker height.
 * @method PhotoSize    getThumb()      (Optional). Sticker thumbnail in .webp or .jpg format.
 * @method int          getFileSize()   (Optional). File size.
 */
class Sticker extends BaseObject
{

    public function relations()
    {
        return [
            'thumb' => PhotoSize::class,
        ];
    }

    public function getObjectName()
    {
        return 'sticker';
    }
}