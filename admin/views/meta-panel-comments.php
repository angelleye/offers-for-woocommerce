<?php
/**
 * Admin view
 *
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */
?>

<?php 
if( isset($offer_comments) ){
    ?>
    <div id="angelleye-woocommerce-offer-meta-comments">
    <?php
    foreach($offer_comments as $comment){
        echo '<li class="offer-comment-entry">';
            echo '<div class="offer-comment-entry-inner">';
                echo '<div class="offer-comment-date">'.date('Y-m-d @ h:i:s A', strtotime($comment->comment_date)).'</div>';
                echo '<div class="offer-comment-content">'.$comment->comment_content.'</div>';
            echo '</div>';
        echo '</li>';
    }
    ?>
    </div>
<?php
} else {
    echo __('No Comments Found.', 'offers-for-woocommerce');
}
?>