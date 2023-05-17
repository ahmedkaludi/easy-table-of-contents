<?php
/**
 * Deactivate Feedback Template
 * @since 2.0.27
 */

$current_user = wp_get_current_user();
$email = '';
if( $current_user instanceof WP_User ) {
	$email = trim( $current_user->user_email );	
}

$reasons = array(
    		1 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="temporary"/>' . __('It is only temporary', 'easy-table-of-contents') . '</label></li>',
		2 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="stopped showing toc"/>' . __('I stopped showing TOC on my site', 'easy-table-of-contents') . '</label></li>',
		3 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="missing feature"/>' . __('I miss a feature', 'easy-table-of-contents') . '</label></li>
		<li><input type="text" name="eztoc_disable_text[]" value="" placeholder="Please describe the feature"/></li>',
		4 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="technical issue"/>' . __('Technical Issue', 'easy-table-of-contents') . '</label></li>
		<li><textarea name="eztoc_disable_text[]" placeholder="' . __('Can we help? Please describe your problem', 'easy-table-of-contents') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="other plugin"/>' . __('I switched to another plugin', 'easy-table-of-contents') .  '</label></li>
		<li><input type="text" name="eztoc_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="other"/>' . __('Other reason', 'easy-table-of-contents') . '</label></li>
		<li><textarea name="eztoc_disable_text[]" placeholder="' . __('Please specify, if possible', 'easy-table-of-contents') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="eztoc-reloaded-feedback-overlay" style="display: none;">
    <div id="eztoc-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, please let us know why you are deactivating:', 'easy-table-of-contents'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    echo $reason;
                }
                ?>
	    </ul>
	    <?php if( null !== $email && !empty( $email ) ) : ?>
    	    <input type="hidden" name="eztoc_disable_from" value="<?php echo $email; ?>" />
	    <?php endif; ?>
	    <input id="eztoc-reloaded-feedback-submit" class="button button-primary" type="submit" name="eztoc_disable_submit" value="<?php _e('Submit & Deactivate', 'easy-table-of-contents'); ?>"/>
	    <a class="button eztoc-feedback-only-deactivate"><?php _e('Only Deactivate', 'easy-table-of-contents'); ?></a>
	    <a class="eztoc-feedback-not-deactivate" href="#"><?php _e('Don\'t deactivate', 'easy-table-of-contents'); ?></a>
	</form>
    </div>
</div>