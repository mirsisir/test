<?php
namespace BooklyRatings\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyRatings\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareTableColumns( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::APPOINTMENTS ) {
            $columns['rating'] = __( 'Rating', 'bookly' );
        }

        return $columns;
    }
}