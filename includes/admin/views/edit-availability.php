<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
    echo '<div class="notice notice-error"><p>Invalid Availability ID.</p></div>';
    return;
}

$availability_id = intval( $_GET['id'] );

// Get existing data
$title              = get_the_title( $availability_id );
$availability       = get_post_meta( $availability_id, '_sac_is_available', true );
$availability_date  = get_post_meta( $availability_id, '_sac_availability_date', true );

// Handle form submission
if ( isset( $_POST['sac_edit_availability_nonce'] ) && wp_verify_nonce( $_POST['sac_edit_availability_nonce'], 'sac_edit_availability' ) ) {
    // Sanitize input data
    $new_title              = sanitize_text_field( $_POST['title'] );
    $new_availability       = sanitize_text_field( $_POST['availability'] );
    $new_availability_date  = sanitize_text_field( $_POST['availability_date'] );

    // Update post title
    wp_update_post( array(
        'ID'         => $availability_id,
        'post_title' => $new_title,
    ) );

    // Update metadata
    update_post_meta( $availability_id, '_sac_is_available', $new_availability );
    if ( 'yes' === $new_availability && ! empty( $new_availability_date ) ) {
        update_post_meta( $availability_id, '_sac_availability_date', $new_availability_date );
    } else {
        delete_post_meta( $availability_id, '_sac_availability_date' ); // Delete date if 'Available Now'
    }

    echo '<div class="notice notice-success"><p>Service Availability updated successfully.</p></div>';

    // Refresh the form data after saving
    $title              = $new_title;
    $availability       = $new_availability;
    $availability_date  = $new_availability_date;
}

?>

<div class="wrap">
    <h1>Edit Property/Unit Availability</h1>

    <form method="post">
        <?php wp_nonce_field( 'sac_edit_availability', 'sac_edit_availability_nonce' ); ?>

        <table class="form-table">
            <tr>
                <th><label for="title">Property/Unit Title</label></th>
                <td><input name="title" type="text" id="title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required></td>
            </tr>

            <tr>
                <th><label for="availability">Availability</label></th>
                <td>
                    <select name="availability" id="availability">
                        <option value="no" <?php selected( $availability, 'no' ); ?>>Available Now</option>
                        <option value="yes" <?php selected( $availability, 'yes' ); ?>>Available From</option>
                    </select>
                </td>
            </tr>

            <tr id="availability_details" style="<?php echo ( $availability === 'yes' ) ? '' : 'display:none;'; ?>">
                <th><label for="availability_date">Available From</label></th>
                <td>
                    <input type="date" id="availability_date" name="availability_date" value="<?php echo esc_attr( $availability_date ); ?>">
                    <span id="display_date"></span>
                </td>
            </tr>
        </table>

        <?php submit_button( 'Update Availability' ); ?>
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