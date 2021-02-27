<?php
namespace BooklyCustomDuration\Backend\Modules\Services\ProxyProviders;

use Bookly\Backend\Modules\Services\Proxy;
use Bookly\Lib as BooklyLib;

class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareUpdateService( array $data )
    {
        if ( $data['duration'] == 'custom' ) {
            $data['duration'] = $data['unit_duration'];
        } else {
            $data['units_min'] = 1;
            $data['units_max'] = 1;
        }

        return $data;
    }
}
