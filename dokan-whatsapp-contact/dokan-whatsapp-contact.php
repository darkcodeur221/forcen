<?php
/**
 * Plugin Name: Dokan WhatsApp Contact
 * Description: Ajoute un champ WhatsApp au profil vendeur Dokan et affiche un bouton pour contacter le vendeur sur sa boutique.
 * Version: 1.0.0
 * Author: ChatGPT
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Ajout du champ Numéro WhatsApp dans le formulaire de profil vendeur
 */
function dwc_add_whatsapp_field( $profile_info ) {
    $whatsapp_number = isset( $profile_info['whatsapp_number'] ) ? esc_attr( $profile_info['whatsapp_number'] ) : '';
    ?>
    <div class="dokan-form-group">
        <label for="whatsapp_number"><?php _e( 'Numéro WhatsApp', 'dokan-lite' ); ?></label>
        <input type="text" class="dokan-form-control" name="whatsapp_number" id="whatsapp_number" value="<?php echo $whatsapp_number; ?>" placeholder="Ex: +221771234567">
    </div>
    <?php
}
add_action( 'dokan_seller_meta_fields', 'dwc_add_whatsapp_field', 10 );

/**
 * Sauvegarde du numéro WhatsApp dans les métadonnées utilisateur
 */
function dwc_save_whatsapp_field( $store_id ) {
    if ( ! empty( $_POST['whatsapp_number'] ) ) {
        update_user_meta( $store_id, 'dokan_whatsapp_number', sanitize_text_field( $_POST['whatsapp_number'] ) );
    }
}
add_action( 'dokan_store_profile_saved', 'dwc_save_whatsapp_field' );

/**
 * Affichage du bouton WhatsApp sur la page boutique du vendeur
 */
function dwc_display_whatsapp_button() {
    $store_user = dokan()->vendor->get( get_query_var( 'author' ) );
    if ( ! $store_user ) {
        return;
    }

    $whatsapp_number = get_user_meta( $store_user->get_id(), 'dokan_whatsapp_number', true );

    if ( $whatsapp_number ) {
        $message = urlencode( __( "Bonjour, j\xE2\x80\x99ai vu votre boutique et j\xE2\x80\x99aimerais en savoir plus.", 'dokan-lite' ) );
        $whatsapp_link = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $whatsapp_number ) . '?text=' . $message;

        echo '<a href="' . esc_url( $whatsapp_link ) . '" target="_blank" class="dokan-btn dokan-btn-success" style="margin-top:15px;display:inline-block;">\xF0\x9F\x93\xB1 ' . __( 'Contacter sur WhatsApp', 'dokan-lite' ) . '</a>';
    }
}
add_action( 'dokan_store_profile_frame_after', 'dwc_display_whatsapp_button' );

/**
 * Style basique pour le bouton
 */
function dwc_enqueue_style() {
    echo '<style>
    .dokan-btn-success { background-color:#25D366 !important; color:#fff !important; font-weight:bold; border-radius:5px; }
    </style>';
}
add_action( 'wp_head', 'dwc_enqueue_style' );

