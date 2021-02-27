<?php
namespace BooklyInvoices\Lib;

use Bookly\Lib;
use BooklyInvoices\Backend\Components;
use BooklyInvoices\Backend\Modules as Backend;
use BooklyInvoices\Frontend\Modules as Frontend;

/**
 * Class Plugin
 * @package BooklyInvoices\Lib
 */
abstract class Plugin extends Lib\Base\Plugin
{
    protected static $prefix;
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    /**
     * @inheritdoc
     */
    protected static function init()
    {
        // Init ajax.
        Backend\Invoice\Ajax::init();
        Components\Invoice\Ajax::init();
        Frontend\Invoice\Ajax::init();

        // Init proxy.
        Backend\Notifications\ProxyProviders\Local::init();
        Backend\Notifications\ProxyProviders\Shared::init();
        Backend\Payments\ProxyProviders\Local::init();
        Backend\Settings\ProxyProviders\Shared::init();
        Notifications\Assets\Item\ProxyProviders\Shared::init();
        Notifications\Assets\Test\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();
    }

    /**
     * @inheritdoc
     */
    public static function activate( $network_wide )
    {
        // Set client address mandatory on the front-end
        update_option( 'bookly_cst_required_address', '1' );
        update_option( 'bookly_app_show_address', '1' );
        $address_show_fields = (array) get_option( 'bookly_cst_address_show_fields' );
        $exists_selected     = false;
        foreach ( $address_show_fields as $column => $attr ) {
            if ( $attr['show'] ) {
                $exists_selected = true;
                break;
            }
        }
        if ( ! $exists_selected ) {
            foreach ( $address_show_fields as &$field ) {
                $field['show'] = true;
            }
            update_option( 'bookly_cst_address_show_fields', $address_show_fields );
        }

        parent::activate( $network_wide );
    }
}