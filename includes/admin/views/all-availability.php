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
    <h2><?php esc_html_e( 'All Service Availabilities', 'service-availability-calendar' ); ?></h2>
    <?php if ( ! empty( $availabilities ) ) : ?>
        <div class="table-responsive">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Service Title', 'service-availability-calendar' ); ?></th>
                        <th><?php esc_html_e( 'Availability', 'service-availability-calendar' ); ?></th>
                        <th><?php esc_html_e( 'Available From', 'service-availability-calendar' ); ?></th>
                        <th><?php esc_html_e( 'Shortcode', 'service-availability-calendar' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'service-availability-calendar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $availabilities as $availability ) :
                        $is_available    = get_post_meta( $availability->ID, '_sac_is_available', true );
                        $availability_date = get_post_meta( $availability->ID, '_sac_availability_date', true );
                        ?>
                        <tr>
                            <td><?php echo esc_html( $availability->post_title ); ?></td>
                            <td>
                                <?php
                                if ( 'yes' === $is_available ) {
                                    echo esc_html__( 'Available From', 'service-availability-calendar' );
                                } elseif ( 'no' === $is_available ) {
                                    echo esc_html__( 'Yes', 'service-availability-calendar' );
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html( $availability_date ? date( 'F j, Y', strtotime( $availability_date ) ) : 'Available Now' ); ?></td>
                            <td><code>[service_availability id="<?php echo esc_attr( $availability->ID ); ?>"]</code></td>
                            <td class="action-button">
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