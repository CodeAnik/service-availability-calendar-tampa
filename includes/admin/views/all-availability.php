<?php
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

$availabilities = get_posts(
    array(
        'post_type'   => 'service_availability',
        'numberposts' => -1,
    )
);

// Handle Deletion
if ( isset( $_POST['delete_availability'] ) && isset( $_POST['sac_delete_nonce'] ) && isset( $_POST['delete_availability_id'] ) ) {
    $delete_id = absint( $_POST['delete_availability_id'] );

    if ( ! wp_verify_nonce( $_POST['sac_delete_nonce'], 'delete_availability_' . $delete_id ) ) {
        die( 'Invalid request' );
    }

    if ( current_user_can( 'delete_posts' ) ) {
        wp_delete_post( $delete_id, true );
        wp_redirect( admin_url( 'admin.php?page=service-availability' ) );
        exit;
    } else {
        echo 'You do not have permission to delete this post.';
    }
}

?>

<div class="wrap">
    <h2><?php esc_html_e( 'All Rental Availabilities', 'service-availability-calendar' ); ?></h2>
    <?php if ( ! empty( $availabilities ) ) : ?>
        <div class="table-responsive">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 2%;"><?php esc_html_e( 'SL', 'service-availability-calendar' ); ?></th>  
                        <th style="width: 50%;"><?php esc_html_e( 'Property/Unit', 'service-availability-calendar' ); ?></th>
                        <th style="display: none;"><?php esc_html_e( 'Availability', 'service-availability-calendar' ); ?></th>
                        <th style="width: 13%;"><?php esc_html_e( 'Availability', 'service-availability-calendar' ); ?></th>
                        <th style="width: 10%;"><?php esc_html_e( 'Price/mo', 'service-availability-calendar' ); ?></th>
                        <th style="width: 20%;" class="shortcode-column"><?php esc_html_e( 'Availability Shortcode', 'service-availability-calendar' ); ?></th>
                        <th style="width: 15%;"><?php esc_html_e( 'Actions', 'service-availability-calendar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl_counter = 1;  // Initialize counter
                    foreach ( $availabilities as $availability ) :
                        $availability_date = get_post_meta( $availability->ID, '_sac_availability_date', true );
                        $is_available      = get_post_meta( $availability->ID, '_sac_is_available', true );
                        $property_price   = get_post_meta( $availability->ID, '_sac_property_price', true );
                        ?>
                        <tr class="row-left-border">
                            <td data-label="<?php esc_attr_e( 'SL', 'service-availability-calendar' ); ?>"><?php echo esc_html( $sl_counter++ ); ?></td>
                            <td data-label="<?php esc_attr_e( 'Service Title', 'service-availability-calendar' ); ?>"><?php echo esc_html( $availability->post_title ); ?></td>
                            <td data-label="<?php esc_attr_e( 'Availability', 'service-availability-calendar' ); ?>" style="display: none;"><?php echo 'yes' === $is_available ? esc_html__( 'Yes', 'service-availability-calendar' ) : esc_html__( 'No', 'service-availability-calendar' ); ?></td>
                            <td data-label="<?php esc_attr_e( 'Available From', 'service-availability-calendar' ); ?>">
                                <?php
                                if ( 'yes' === $is_available && ! empty( $availability_date ) ) {
                                    echo date_i18n( 'F j, Y', strtotime( $availability_date ) );
                                } else {
                                    echo esc_html__( 'Available Now', 'service-availability-calendar' );
                                }
                                ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Price', 'service-availability-calendar' ); ?>">
                                <?php echo $property_price ? esc_html( number_format( $property_price ) ) : esc_html__( 'Not set', 'service-availability-calendar' ); ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Availability Shortcode', 'service-availability-calendar' ); ?>" class="shortcode-column">
                                <p><code>[service_availability id="<?php echo esc_attr( $availability->ID ); ?>"]</code></p>
                                <p><code>[service_price id="<?php echo esc_attr( $availability->ID ); ?>"]</code></p>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Actions', 'service-availability-calendar' ); ?>" class="action-button">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=edit-service-availability&id=' . $availability->ID ) ); ?>" class="button button-primary"><?php esc_html_e( 'Edit', 'service-availability-calendar' ); ?></a>

                                <form method="post" action="">
                                    <?php wp_nonce_field( 'delete_availability_' . $availability->ID, 'sac_delete_nonce' ); ?>
                                    <input type="hidden" name="delete_availability_id" value="<?php echo esc_attr( $availability->ID ); ?>">
                                    <input type="submit" name="delete_availability" value="<?php esc_html_e( 'Delete', 'service-availability-calendar' ); ?>" class="button button-danger" onclick="return confirm('<?php esc_html_e( 'Are you sure you want to delete this availability?', 'service-availability-calendar' ); ?>')" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p><?php esc_html_e( 'No service availabilities found.', 'service-availability-calendar' ); ?></p>
    <?php endif; ?>
</div>