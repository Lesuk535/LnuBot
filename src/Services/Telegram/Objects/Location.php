<?php
/**
 * Created by PhpStorm.
 * User: 4erma
 * Date: 21.05.2019
 * Time: 14:58
 */

namespace App\Services\Telegram\Objects;

/**
 * Class Location
 *
 * @method float    getLongitude()  Longitude as defined by sender.
 * @method float    getLatitude()   Latitude as defined by sender.
 */
class Location extends BaseObject
{

    public function relations()
    {
        return [];
    }

    public function getObjectName()
    {
        return 'location';
    }
}