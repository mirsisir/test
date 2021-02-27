<?php
namespace BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Staff;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function editStaff( array $data, Staff $staff )
    {
        if ( $gc_errors = BooklyLib\Session::get( 'staff_google_auth_error' ) ) {
            foreach ( (array) json_decode( $gc_errors, true ) as $error ) {
                $data['alert']['error'][] = $error;
            }
            BooklyLib\Session::destroy( 'staff_google_auth_error' );
        }

        $auth_url           = null;
        $google_calendars   = array();
        $google_calendar_id = null;
        if ( $staff->getGoogleData() == '' ) {
            if ( Lib\Config::getGoogleCalendarSyncMode() !== null ) {
                $google  = new Lib\Google\Client();
                $auth_url = $google->createAuthUrl( $staff->getId() );
            } else {
                $auth_url = false;
            }
        } else {
            $google = new Lib\Google\Client();
            if ( $google->auth( $staff, true ) && ( $list = $google->getCalendarList() ) !== false ) {
                $google_calendars   = $list;
                $google_calendar_id = $google->data()->calendar->id;
            } else {
                foreach ( $google->getErrors() as $error ) {
                    $data['alert']['error'][] = $error;
                }
            }
        }

        $data['tpl']['gc'] = compact( 'staff', 'auth_url', 'google_calendars', 'google_calendar_id' );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function renderStaffDetails( $staff )
    {
        $categories = Lib\Entities\StaffCategory::query()->sortBy( 'position' )->fetchArray();

        self::renderTemplate( 'staff_details', compact( 'categories', 'staff' ) );
    }

    /**
     * @inheritDoc
     */
    public static function preUpdateStaff( Staff $staff, array $params )
    {
        if ( array_key_exists( 'google_disconnect', $params ) && $params['google_disconnect'] == '1' ) {
            $google = new Lib\Google\Client();
            if ( $google->auth( $staff ) ) {
                if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                    $google->calendar()->stopWatching( false );
                }
                $google->revokeToken();
            }
            $staff->setGoogleData( null );
        } elseif ( isset ( $params['google_calendar_id'] ) ) {
            $calendar_id = $params['google_calendar_id'];
            $google      = new Lib\Google\Client();
            $update_google_data = false;
            if ( $google->auth( $staff, true ) ) {
                if ( ( $staff->getVisibility() === 'archive' )
                    && ( $params['visibility'] !== 'archive' )
                ) {
                    // Change visibility from archive
                    if ( BooklyLib\Proxy\Pro::getGoogleCalendarSyncMode() === '2-way' ) {
                        $google->calendar()->clearSyncToken()->sync();
                        $google->calendar()->watch();
                        $update_google_data = true;
                    }
                } elseif ( ( $staff->getVisibility() !== 'archive' )
                    && ( $params['visibility'] === 'archive' )
                ) {
                    // Change visibility to archive
                    if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                        $google->calendar()->clearSyncToken();
                        $update_google_data = true;
                    }
                } elseif ( $calendar_id !== $google->data()->calendar->id ) {
                    // Calendar changed
                    if ( $staff->getVisibility() === 'archive' ) {
                        wp_send_json_error( array( 'error' => __( 'Can\'t change calendar for archived staff', 'bookly' ) ) );
                    } elseif ( $calendar_id != '' ) {
                        if ( ! $google->validateCalendarId( $calendar_id ) ) {
                            wp_send_json_error( array( 'error' => implode( '<br>', $google->getErrors() ) ) );
                        }
                    } else {
                        $calendar_id = null;
                    }
                    if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                        $google->calendar()->clearSyncToken()->stopWatching( false );
                    }
                    $google->data()->calendar->id = $calendar_id;
                    $update_google_data = true;
                }
                if ( $update_google_data ) {
                    $google_data = $google->data();
                    $staff->setGoogleData( $google_data->toJson() );
                }
            }
        }
        if ( ! $params['working_time_limit'] ) {
            $staff->setWorkingTimeLimit( null );
        }
    }

}