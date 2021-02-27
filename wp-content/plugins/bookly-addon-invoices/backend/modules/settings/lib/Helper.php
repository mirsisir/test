<?php
namespace BooklyInvoices\Backend\Modules\Settings\Lib;

use Bookly\Lib\Utils\Common;
use Bookly\Backend\Components\Settings;

class Helper
{
    /** @var string */
    public static $mode = 'preview';

    /**
     * Helper constructor.
     *
     * @param string $mode
     */
    public function __construct( $mode = 'preview' )
    {
        self::$mode = $mode;
    }

    /**
     * Render editable text (multi-line).
     *
     * @param string $option_name
     * @param string $codes
     * @param string $placement
     * @param string $title
     */
    public static function renderString( $option_name, $codes = '', $placement = 'bottom', $title = '' )
    {
        echo self::$mode == 'preview'
            ? nl2br( Common::getTranslatedOption( $option_name ) )
            : self::renderText( $option_name, $codes, $placement, $title );
    }

    /**
     * @param $option_name
     * @param $class
     */
    public static function renderImage( $option_name, $class )
    {
        self::$mode == 'preview'
            ? self::renderAttachmentImage( $option_name )
            : Settings\Image::render( $option_name, $class );
    }

    /**
     * @param string $option_name
     * @param string $codes
     * @param string $placement
     * @param string $title
     * @return string
     */
    private static function renderText( $option_name, $codes = '', $placement = 'bottom', $title = '' )
    {
        $data_attributes = array(
            'codes'     => esc_attr( $codes ),
            'option'    => $option_name,
            'placement' => $placement,
            'title'     => esc_attr( $title ),
            'values'    => esc_attr( json_encode( array( $option_name => get_option( $option_name ) ) ) ),
        );

        return strtr( '<span class="bookly-editable bookly-js-editable bookly-js-option {class} text-pre-wrap" {data-*} data-type="bookly" data-fieldType="textarea">{content}</span>', array(
            '{class}'   => $option_name,
            '{data-*}'  => implode( ' ', array_map(
                function ( $value, $key ) {
                    return sprintf( 'data-%s="%s"', $key, $value );
                },
                $data_attributes,
                array_keys( $data_attributes )
            )),
            '{content}' => esc_attr( get_option( $option_name ) ),
        ) );
    }

    /**
     * @param $option_name
     */
    private static function renderAttachmentImage( $option_name )
    {
        $img = wp_get_attachment_image_src( get_option( $option_name ), 'full' );

        if ( $img ) {
            printf( '<img src="%s" />', $img[0] );
        }
    }

}