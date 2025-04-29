<?php
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if ( isset( $_POST['sac_submit'] ) ) {
    $service_title   = sanitize_text_field( $_POST['service_title'] );
    $is_available    = sanitize_text_field( $_POST['availability'] );
    $availability_date = '';

    if ( 'yes' === $is_available ) {
        $availability_date = sanitize_text_field( $_POST['availability_date'] );
    }

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'service_availability',
            'post_title'  => $service_title,
            'post_status' => 'publish',
        )
    );

    if ( $post_id ) {
        update_post_meta( $post_id, '_sac_is_available', $is_available );
        if ( 'yes' === $is_available && ! empty( $availability_date ) ) {
            update_post_meta( $post_id, '_sac_availability_date', $availability_date );
        } else {
            delete_post_meta( $post_id, '_sac_availability_date' );
        }

        do_action( 'sac_service_availability_saved', $post_id, $is_available, $availability_date );

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Service availability saved successfully!', 'service-availability-calendar' ) . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'There was an error saving the service availability.', 'service-availability-calendar' ) . '</p></div>';
    }
}
?>

<div class="wrap">
    <h2><?php esc_html_e( 'Add New Property/Unit Availability', 'service-availability-calendar' ); ?></h2>
    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Property/Unit Title', 'service-availability-calendar' ); ?></th>
                <td><input type="text" name="service_title" class="regular-text" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Availability', 'service-availability-calendar' ); ?></th>
                <td>
                    <select name="availability" id="availability">
                        <option value="no"><?php esc_html_e( 'Available Now', 'service-availability-calendar' ); ?></option>
                        <option value="yes"><?php esc_html_e( 'Available From', 'service-availability-calendar' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr valign="top" id="availability_details" style="display: none;">
                <th scope="row"><?php esc_html_e( 'Available From', 'service-availability-calendar' ); ?></th>
                <td>
                    <input type="date" id="availability_date" name="availability_date">
                    <span id="display_date"></span>  </td>
            </tr>
        </table>
        <?php submit_button( __( 'Save Availability', 'service-availability-calendar' ), 'primary', 'sac_submit' ); ?>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        let availabilitySelect = $('#availability');
        let availabilityDetails = $('#availability_details');
        let availabilityDateInput = $('#availability_date');
        let displayDateSpan = $('#display_date');

        availabilitySelect.change(function() {
            availabilityDetails.toggle($(this).val() === 'yes');
        }).change();

        // Format and display the date
        function formatAndDisplayDate() {
            let selectedDate = availabilityDateInput.val(); // yyyy-mm-dd
            if (selectedDate) {
                let dateObj = new Date(selectedDate);
                let options = { month: 'long', day: 'numeric', year: 'numeric' };
                let formattedDate = dateObj.toLocaleDateString('en-US', options); // Month Name Day, Year
                displayDateSpan.text('  ' + formattedDate);
            } else {
                displayDateSpan.text('');
            }
        }

        availabilityDateInput.on('change', formatAndDisplayDate);

        // Initial format (if there's a date already)
        formatAndDisplayDate();
    });
</script>