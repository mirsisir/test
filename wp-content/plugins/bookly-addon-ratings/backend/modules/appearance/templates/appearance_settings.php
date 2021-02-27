<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <?php Inputs::renderCheckBox( __( 'Show staff member rating before employee name', 'bookly' ), null, get_option( 'bookly_ratings_app_show_on_frontend' ), array( 'id' => 'bookly-show-ratings' ) ) ?>
</div>