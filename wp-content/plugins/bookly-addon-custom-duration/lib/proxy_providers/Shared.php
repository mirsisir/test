<?php
namespace BooklyCustomDuration\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * Provide shared methods to be used in Bookly.
 *
 * @package BooklyCustomDuration\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCaSeStQuery( BooklyLib\Query $query )
    {
        return $query->addSelect( 's.duration, s.units_min, s.units_max' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareCategoryService( array $result, array $row )
    {
        $result['services'][ $row['id'] ]['units'] = self::_getUnitOptions( $row );

        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function prepareCategoryServiceStaffLocation( array $location_data, array $row )
    {
        $location_data['units'] = self::_getUnitOptions( $row );

        return $location_data;
    }

    /**
     * Add duration for services
     *
     * @param array $data
     * @return array
     */
    private static function _getUnitOptions( array $data )
    {
        $units = array();
        $staff_name_with_price = get_option( 'bookly_app_service_duration_with_price' );

        $duration  = (int) $data['duration'];
        $unit_min  = (int) $data['units_min'] ?: 1;
        $units_max = (int) $data['units_max'] ?: 1;

        $price = (float) $data['price'];
        for ( $unit = $unit_min; $unit <= $units_max; $unit++ ) {
            $title = BooklyLib\Utils\DateTime::secondsToInterval( $duration * $unit );
            if ( $staff_name_with_price ) {
                $title .= ' (' . BooklyLib\Utils\Price::format( $price * $unit ) . ')';
            }

            $units[$unit] = array(
                'id' => $unit,
                'name' => $title,
            );
        }

        return $units;
    }
}