<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib as BooklyLib;
?>
<div id="bookly-staff-rating" class="bookly-js-staff-rating-<?php echo $form_id ?> ">
    <div id="bookly-tbs">
    <?php if ( $ca ) : ?>
        <?php if ( $expired ) : ?>
            <p><?php esc_html_e( 'The feedback period has expired.', 'bookly' ) ?></p>
        <?php elseif( $not_started ) : ?>
            <p><?php esc_html_e( 'You cannot rate this service before appointment.', 'bookly' ) ?></p>
        <?php else : ?>
            <div class="bookly-js-rating-wrap">
                <div id="bookly-rating-quiz" class="my-2">
                    <label><?php printf( esc_html__( 'Rate the quality of the %s provided to you on %s at %s by %s', 'bookly' ), $ca['service_title'], $ca['date'], $ca['time'], esc_html( $ca['staff_name'] ) ) ?></label>
                    <div>
                        <?php for ( $i = 0; $i <= 4; ++ $i ): ?><i class="bookly-js-star bookly-cursor-pointer fa-star fa-2x <?php echo ( $ca['rating'] !== null && $ca['rating'] > $i ) ? 'fas text-warning' : 'far text-muted' ?>"></i><?php endfor ?>
                    </div>
                </div>
                <?php if ( ! isset( $attributes['hide'] ) || $attributes['hide'] != 'comment' ) : ?>
                    <div class="form-group">
                        <textarea class="form-control bookly-js-rating-comment mt-4" placeholder="<?php esc_attr_e( 'Leave your comment', 'bookly' ) ?>"><?php echo esc_textarea( $ca['rating_comment'] ) ?></textarea>
                    </div>
                <?php endif ?>
                <div class="form-group">
                    <?php Buttons::renderSubmit( 'bookly-save', null, null, array( 'disabled' => 'disabled' ) ) ?>
                </div>
            </div>
            <p class="bookly-js-rating-success" style="display: none;">
                <?php esc_html_e( 'Your rating has been saved. We appreciate your feedback.', 'bookly' ) ?>
            </p>
        <?php endif ?>
    <?php endif ?>
    </div>
</div>

<script type="text/javascript">
    (function (win, fn) {
        var done = false, top = true,
            doc = win.document,
            root = doc.documentElement,
            modern = doc.addEventListener,
            add = modern ? 'addEventListener' : 'attachEvent',
            rem = modern ? 'removeEventListener' : 'detachEvent',
            pre = modern ? '' : 'on',
            init = function(e) {
                if (e.type == 'readystatechange') if (doc.readyState != 'complete') return;
                (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                if (!done) { done = true; fn.call(win, e.type || e); }
            },
            poll = function() {
                try { root.doScroll('left'); } catch(e) { setTimeout(poll, 50); return; }
                init('poll');
            };
        if (doc.readyState == 'complete') fn.call(win, 'lazy');
        else {
            if (!modern) if (root.doScroll) {
                try { top = !win.frameElement; } catch(e) { }
                if (top) poll();
            }
            doc[add](pre + 'DOMContentLoaded', init, false);
            doc[add](pre + 'readystatechange', init, false);
            win[add](pre + 'load', init, false);
        }
    })(window, function() {
        let rating = false;
        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-rating-quiz').on('mouseenter', '.bookly-js-star', function () {
            let index = jQuery(this).index();
            jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-rating-quiz i.bookly-js-star').each(function () {
                if (jQuery(this).index() <= index) {
                    jQuery(this).removeClass('text-muted far').addClass('text-warning fas');
                } else {
                    jQuery(this).removeClass('text-warning fas').addClass('text-muted far');
                }
            });
        }).on('click', '.bookly-js-star', function () {
            rating = jQuery(this).index();
            jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-save').prop('disabled', false);
        }).on('mouseleave', '.bookly-js-star', function () {
            jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-rating-quiz i.bookly-js-star').each(function () {
                if (rating !== false && jQuery(this).index() <= rating) {
                    jQuery(this).removeClass('text-muted far').addClass('text-warning fas');
                } else {
                    jQuery(this).removeClass('text-warning fas').addClass('text-muted far');
                }
            });
        });
        
        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> button').on('click', function () {
            if (rating !== false) {
                var ladda = Ladda.create(this);
                ladda.start();
                jQuery.post({
                    url        : <?php echo json_encode( $ajax_url ) ?>,
                    data       : {
                        action    : 'bookly_ratings_set_rating',
                        csrf_token: <?php echo json_encode( BooklyLib\Utils\Common::getCsrfToken() ) ?>,
                        token     : <?php echo json_encode( $token ) ?>,
                        rating    : rating + 1,
                        comment   : jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-comment').val()
                    },
                    dataType   : 'json',
                    xhrFields  : {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success    : function (response) {
                        ladda.stop();
                        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-wrap').hide();
                        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-success').show();
                    }
                });
            }
        });
    });
</script>