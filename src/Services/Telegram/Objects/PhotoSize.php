<?php


namespace App\Services\Telegram\Objects;

/**
 * Class PhotoSize.
 *
 *
 * @method string   getFileId()     Unique identifier for this file.
 * @method int      getWidth()      Photo width.
 * @method int      getHeight()     Photo height.
 * @method int      getFileSize()   (Optional). File size.
 */
class PhotoSize extends BaseObject
{

    public function relations()
    {
        return [];
    }

    public function getObjectName()
    {
        return 'photo_size';
    }
}