<?php
namespace Bookly\Backend\Components\Settings;

use Bookly\Lib;

/**
 * Class Inputs
 * @package Bookly\Backend\Components\Settings
 */
class Inputs
{
    /**
     * Render numeric input.
     *
     * @param string   $option_name
     * @param string   $label
     * @param string   $help
     * @param int|null $min
     * @param int|null $step
     * @param int|null $max
     */
    public static function renderNumber( $option_name, $label, $help, $min = null, $step = null, $max = null )
    {
        $control = strtr(
            '<input type="number" id="{name}" class="form-control" name="{name}" value="{value}"{min}{max}{step} />',
            array(
                '{name}'  => esc_attr( $option_name ),
                '{value}' => esc_attr( get_option( $option_name ) ),
                '{min}'   => $min !== null ? ' min="' . $min . '"' : '',
                '{max}'   => $max !== null ? ' max="' . $max . '"' : '',
                '{step}'  => $step !== null ? ' step="' . $step . '"' : '',
            )
        );

        echo self::buildControl( $option_name, $label, $help, $control );
    }

    /**
     * Render row with numeric inputs
     *
     * @param array      $option_names
     * @param string     $label
     * @param string     $help
     * @param null|array $min
     * @param null|array $step
     * @param null|array $max
     */
    public static function renderNumbers( array $option_names, $label, $help, $min = null, $step = null, $max = null )
    {
        $control = '<div class="form-row">';
        foreach ( $option_names as $index => $option_name ) {
            $control .= strtr(
                '<div class="col"><input type="number" id="{name}" class="form-control" name="{name}" value="{value}"{min}{max}{step} /></div>',
                array(
                    '{name}'  => esc_attr( $option_name ),
                    '{value}' => esc_attr( get_option( $option_name ) ),
                    '{min}'   => $min !== null ? ' min="' . $min[ $index ] . '"' : '',
                    '{max}'   => $max !== null ? ' max="' . $max[ $index ] . '"' : '',
                    '{step}'  => $step !== null ? ' step="' . $step[ $index ] . '"' : '',
                )
            );
        }
        $control .= '</div>';

        echo self::buildControl( $option_names[0], $label, $help, $control );
    }

    /**
     * Render text input.
     *
     * @param string      $option_name
     * @param string      $label
     * @param string|null $help
     */
    public static function renderText( $option_name, $label, $help = null )
    {
        $control = strtr(
            '<input type="text" id="{name}" class="form-control" name="{name}" value="{value}" />',
            array(
                '{name}'  => esc_attr( $option_name ),
                '{value}' => esc_attr( get_option( $option_name ) ),
            )
        );

        echo self::buildControl( $option_name, $label, $help, $control );
    }

    /**
     * Render text area input.
     *
     * @param string      $option_name
     * @param string      $label
     * @param string|null $help
     * @param int         $rows
     */
    public static function renderTextArea( $option_name, $label, $help = null, $rows = 9 )
    {
        $control = strtr(
            '<textarea id="{name}" name="{name}" class="form-control" rows="{rows}" placeholder="{placeholder}">{value}</textarea>',
            array(
                '{name}'  => esc_attr( $option_name ),
                '{value}' => esc_textarea( get_option( $option_name ) ),
                '{rows}'  => $rows,
                '{placeholder}' => esc_attr__( 'Enter a value', 'bookly' ),
            )
        );

        echo self::buildControl( $option_name, $label, $help, $control );
    }

    /**
     * Build setting control.
     *
     * @param string $option_name
     * @param string $label
     * @param string $help
     * @param string $control_html
     * @return string
     */
    public static function buildControl( $option_name, $label, $help, $control_html )
    {

        return strtr(
            '<div class="form-group">{label}{control}{help}</div>',
            array(
                '{label}'   => $label != '' ? sprintf( '<label for="%s">%s</label>', $option_name, $label ) : '',
                '{help}'    => $help  != '' ? sprintf( '<small class="form-text text-muted">%s</small>', $help ) : '',
                '{control}' => $control_html,
            )
        );
    }

    /**
     * Render text with copy.
     *
     * @param string $option_name
     * @param string $label
     * @param string|null $help
     */
    public static function renderTextCopy( $option_name, $label, $help = null )
    {
        $version   = Lib\Plugin::getVersion();
        $resources = plugins_url( 'backend\components\settings\resources', Lib\Plugin::getMainFile() );

        wp_enqueue_script( 'bookly-settings-controls.js', $resources . '/js/settings-controls.js', array( 'jquery' ), $version );

        $control = strtr(
            '<span id="{name}" style="cursor: text">{value}</span>
             <a href="#{name}" class="far fa-copy fa-fw text-secondary text-decoration-none ml-auto" title="{title}"></a>
             <small class="text-muted ml-auto" style="display:none">{copied}</small>',

            array(
                '{name}'  => esc_attr( $option_name ),
                '{value}' => esc_attr( get_option( $option_name ) ),
                '{title}' => esc_attr( __( 'Copy to clipboard', 'bookly' ) ),
                '{copied}' => esc_attr( __( 'copied', 'bookly' ) )
            )
        );

        echo strtr(
            '<div class="form-group bookly-js-copy-to-clipboard">{label}<div class="form-control d-flex align-items-center" style="opacity:1;cursor:default">{control}</div>{help}</div>',
            array(
                '{label}'   => $label != '' ? sprintf( '<label for="%s">%s</label>', $option_name, $label ) : '',
                '{help}'    => $help  != '' ? sprintf( '<small class="form-text text-muted">%s</small>', $help ) : '',
                '{control}' => $control,
            )
        );
    }
}