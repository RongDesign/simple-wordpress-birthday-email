<?php 
add_action('birthday_email', 'birthday_email_send_mail');
 
function birthday_email_send_mail() {
$users = get_users( array( 'fields' => array( 'ID' ) ) );
foreach($users as $user){
        $birthday = get_user_meta ( $user->ID, 'birth_date');
        $user_info = get_userdata($user->ID);
        $todays_date = date("Y/m/d");
        if ($birthday[0] == $todays_date) {
          $to = $user_info->user_email;
          $subject = get_field('birthday_email_subject',6262);
          $body = get_field('birthday_email_body',6262).'<p style="text-align:center;"><strong>Your Birthday Coupon Code: </strong>'.create_birthday_coupon($user->ID).'</p>';
          $headers = array('Content-Type: text/html; charset=UTF-8','From: '.get_field('birthday_email_sender',6262));

        wp_mail( $to, $subject, $body, $headers );
        }
    }
 
}
function create_birthday_coupon($user_id) {
  /**
 * Create a coupon programatically
 */
$coupon_code = 'happy_birthday_'.date(Y)."_".$user_id; // Code
$amount = '10'; // Amount
$discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
					
$coupon = array(
	'post_title' => $coupon_code,
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type'		=> 'shop_coupon'
);
					
$new_coupon_id = wp_insert_post( $coupon );
					
// Add meta
update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
update_post_meta( $new_coupon_id, 'individual_use', 'no' );
update_post_meta( $new_coupon_id, 'product_ids', '' );
update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
update_post_meta( $new_coupon_id, 'usage_limit', '' );
update_post_meta( $new_coupon_id, 'expiry_date', '' );
update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

  return $coupon_code;
}
if ( ! wp_next_scheduled( 'birthday_email' ) ) {
    wp_schedule_event( time(), 'daily', 'birthday_email' );
}

//Reset Birthday Coupon Code If Time Changes
//wp_clear_scheduled_hook( 'birthday_email' );
?>