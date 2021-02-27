<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;
use Bookly\Lib as BooklyLib;
?>
<table width="100%" style="border:0.1px solid #666;">
    <thead>
    <tr>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Service', 'bookly' ) ?></th>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Date', 'bookly' ) ?></th>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Provider', 'bookly' ) ?></th>
        <?php if ( $show['deposit'] ) : ?>
            <th style="border-right:0.1px solid #666; text-align: right; padding-right: 2px"><?php esc_html_e( 'Deposit', 'bookly' ) ?></th>
        <?php endif ?>
        <th style="border-right:0.1px solid #666; text-align: right; padding-right: 2px"><?php esc_html_e( 'Price', 'bookly' ) ?> </th>
        <?php if ( $show['taxes'] ) : ?>
            <th style="border-right:0.1px solid #666; text-align: right; padding-right: 2px"><?php esc_html_e( 'Tax', 'bookly' ) ?></th>
        <?php endif ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $payment['items'] as $item ) : ?>
        <tr>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px"><?php if ( $item['number_of_persons'] > 1 ) echo $item['number_of_persons'] . '&nbsp;&times;&nbsp;' ?><?php echo esc_html( $item['service_name'] ) ?><?php if ( isset( $item['units'], $item['duration'] ) && $item['units'] > 1 ) echo '&nbsp;(' . DateTime::secondsToInterval( $item['units'] * $item['duration'] ) . ')' ?>
                <?php if ( ! empty ( $item['extras'] ) ) : ?>
                    <ul>
                        <?php $extras_price = 0 ?>
                        <?php foreach ( $item['extras'] as $extra ) : ?>
                            <li><?php if ( $extra['quantity'] > 1 ) echo $extra['quantity'] . '&nbsp;&times;&nbsp;' ?><?php echo esc_html( $extra['title'] ) ?></li>
                            <?php $extras_price += $extra['price'] * $extra['quantity'] ?>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </td>
            <?php $appointment_date = $item['appointment_date'] ?>
            <?php if ( $appointment_date !== null ) : ?>
                <?php if ( $time_zone !== null ) : ?>
                    <?php $appointment_date = date_create( $appointment_date . ' ' . BooklyLib\Config::getWPTimeZone() ) ?>
                    <?php $appointment_date = date_format( date_timestamp_set( date_create( $time_zone ), $appointment_date->getTimestamp() ), 'Y-m-d H:i:s' ) ?>
                <?php elseif ( $time_zone_offset !== null ) : ?>
                    <?php $appointment_date = DateTime::applyTimeZoneOffset( $appointment_date, $time_zone_offset ) ?>
                <?php endif ?>
                <?php $appointment_date = DateTime::formatDateTime( $appointment_date ) ?>
            <?php else : ?>
                <?php $appointment_date = __( 'N/A', 'bookly' ) ?>
            <?php endif ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px"><?php echo $appointment_date ?></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px"><?php echo $item['staff_name'] ?></td>
            <?php if ( $show['deposit'] ) : ?>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo $item['deposit_format'] ?></td>
            <?php endif ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px">
                <?php if ( $payment['from_backend'] ) : ?>
                    <?php echo Price::format( $item['service_price'] ) ?>
                <?php else : ?>
                    <?php if ( $item['number_of_persons'] > 1 )
                        echo $item['number_of_persons'] . '&nbsp;&times;&nbsp;' ?><?php echo Price::format( $item['service_price'] ) ?><?php if ( ! empty ( $item['extras'] ) ) : ?>
                        <ul><?php foreach ( $item['extras'] as $extra ) : ?>
                            <li><?php printf( '%s%s%s',
                            ( $item['number_of_persons'] > 1 && $payment['extras_multiply_nop'] ) ? $item['number_of_persons'] . '&nbsp;&times;&nbsp;' : '',
                            ( $extra['quantity'] > 1 ) ? $extra['quantity'] . '&nbsp;&times;&nbsp;' : '',
                            Price::format( $extra['price'] )
                        ) ?></li><?php endforeach ?></ul>
                    <?php endif ?>
                <?php endif ?>
            </td>
            <?php if ( $show['taxes'] ) : ?>
                <?php if ( $item['service_tax'] !== null ) : ?>
                    <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php printf( $payment['tax_in_price'] == 'included' ? '(%s)' : '%s', Price::format( $item['service_tax'] ) ) ?>
                    <?php if ( ! empty ( $item['extras'] ) ) : ?>
                        <ul>
                            <?php foreach ( $item['extras'] as $extra ) : ?>
                                <?php if ( isset( $extra['tax'] ) ) : ?>
                                    <li><?php echo Price::format( $extra['tax'] ) ?></li>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                    </td>
                <?php else : ?>
                    <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px">-</td>
                <?php endif ?>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="2"><?php esc_html_e( 'Subtotal', 'bookly' ) ?></td>
        <?php if ( $show['deposit'] ) : ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666;  text-align: right; padding:2px"><?php echo Price::format( $payment['subtotal']['deposit'] ) ?></td>
        <?php endif ?>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $payment['subtotal']['price'] ) ?></td>
        <?php if ( $show['taxes'] ) : ?>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"></td>
        <?php endif ?>
    </tr>
    <?php if ( $show['coupons'] || $payment['coupon'] ) : ?>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Coupon discount', 'bookly' ) ?><?php if ( $payment['coupon'] ) : ?><br>(<?php echo $payment['coupon']['code'] ?>)<?php endif ?>
        </td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right;padding:2px">
            <?php if ( $payment['coupon'] ) : ?>
                <?php if ( $payment['coupon']['discount'] ) : ?><?php echo $payment['coupon']['discount'] ?>%<br><?php endif ?>
                <?php if ( $payment['coupon']['deduction'] ) : ?><?php echo Price::format( $payment['coupon']['deduction'] ) ?><?php endif ?>
            <?php else : ?>
                <?php echo Price::format( 0 ) ?>
            <?php endif ?>
        </td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px"></td>
        <?php endif ?>
    </tr>
    <?php endif ?>
    <?php if ( $show['customer_groups'] || $payment['group_discount'] ) : ?>
        <tr>
            <td style="border-top:0.1px solid #666; padding:2px"></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Group discount', 'bookly' ) ?></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo $payment['group_discount'] ?: Price::format( 0 ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px"> </td>
            <?php endif ?>
        </tr>
    <?php endif ?>

    <?php foreach ( $adjustments as $adjustment ) : ?>
    <tr>
        <th style="border-top:0.1px solid #666; padding:2px"></th>
        <th style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php echo esc_html( $adjustment['reason'] ) ?></th>
        <th style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $adjustment['amount'] ) ?></th>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $adjustment['tax'] ) ?></td>
        <?php endif ?>
    </tr>
    <?php endforeach ?>

    <?php if ( $show['gateway'] || (float) $payment['price_correction'] ) : ?>
        <tr>
            <td style="border-top:0.1px solid #666; padding:2px"></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php echo \Bookly\Lib\Entities\Payment::typeToString( $payment['type'] ) ?></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right;padding:2px"><?php echo Price::format( $payment['price_correction'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right;padding:2px">-</td>
            <?php endif ?>
        </tr>
    <?php endif ?>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Total', 'bookly' ) ?></td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $payment['total'] ) ?></td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px">(<?php echo Price::format( $payment['tax_total'] ) ?>)</td>
        <?php endif ?>
    </tr>
    <?php if ( $payment['total'] != $payment['paid'] ) : ?>
        <tr>
            <td style="border-top:0.1px solid #666; padding:2px"></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Paid', 'bookly' ) ?></td>
            <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $payment['paid'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px">(<?php echo Price::format( $payment['tax_paid'] ) ?>)</td>
            <?php endif ?>
        </tr>

        <?php if ( ( $payment['total'] - $payment['paid'] ) > 0
            || ( $show['taxes'] && ( $payment['tax_total'] - $payment['tax_paid'] ) > 0 ) ) : ?>
            <tr>
                <td style="border-top:0.1px solid #666; padding:2px"></td>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Due', 'bookly' ) ?></td>
                <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px"><?php echo Price::format( $payment['total'] - $payment['paid'] ) ?></td>
                <?php if ( $show['taxes'] ) : ?>
                    <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; text-align: right; padding:2px">(<?php echo Price::format( $payment['tax_total'] - $payment['tax_paid'] ) ?>)</td>
                <?php endif ?>
            </tr>
        <?php endif ?>
    <?php endif ?>
    </tbody>
</table>