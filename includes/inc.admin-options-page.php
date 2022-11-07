<div id='toc' class='wrap'>
    <a href="https://tocwp.com/" target="_blank">
        <img src="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?>" alt="tocwp"
             srcset="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 1x, <?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 2x">
    </a>
    <h1 style="display:none;">&nbsp;</h1>
    <div class="toc-tab-panel">
        <a id="eztoc-welcome" class="eztoc-tablinks" data-href="no" href="#welcome"
           onclick="ezTocTabToggle(event, 'welcome')"><?= esc_html_e( 'Welcome', 'easy-table-of-contents' ) ?></a>
        <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings"
           onclick="ezTocTabToggle(event, 'general')"><?= esc_html_e( 'Settings', 'easy-table-of-contents' ) ?></a>
        <?php
        $pro = '';

        if (function_exists('ez_toc_pro_activation_link')) {
            $pro = '<a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#eztoc-prosettings" onclick="ezTocTabToggle(event, \'general\')">PRO Settings</a>';
        } ?>
        <?php echo $pro; ?>

        <?php
        if (!function_exists('ez_toc_pro_activation_link')) { ?>
            <a class="eztoc-tablinks" id="eztoc-freevspro" href="#freevspro-support"
               onclick="ezTocTabToggle(event, 'freevspro')" data-href="no"><?= esc_html_e( 'Free vs PRO', 'easy-table-of-contents' ) ?></a>
        <?php }
        ?>
        <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support"
           onclick="ezTocTabToggle(event, 'technical')" data-href="no"><?= esc_html_e( 'Help & Support', 'easy-table-of-contents' ) ?></a>
           <?php if (!function_exists('ez_toc_pro_activation_link')) { ?>
            <a class="eztoc-tablinks" id="eztoc-upgrade" href="https://tocwp.com/pricing/" target="_blank"><?= esc_html( 'UPGRADE to PRO', 'easy-table-of-contents' ) ?></a>
            <?php } ?>
        <?php

        if (function_exists('ez_toc_pro_activation_link')) {
            $license_info = get_option("easytoc_pro_upgrade_license");
            $license_exp = date('Y-m-d', strtotime($license_info['pro']['license_key_expires']));
            $today = date('Y-m-d');
            $exp_date = $license_exp;
            $date1 = date_create($today);
            $date2 = date_create($exp_date);
            $diff = date_diff($date1, $date2);
            $days = $diff->format("%a");
            $days = intval($days); ?>
            <a class="eztoc-tablinks" id="eztoc-license" href="#license"
               onclick="ezTocTabToggle(event, 'license')"
               data-href="no"><?= esc_html_e('License', 'easy-table-of-contents') ?></a>
            <?php
            if ($days < 30) {
                ?>
                <span class="dashicons dashicons-warning" style="color: #ffb229;position: relative;top:
                15px;left: -10px;"></span>
            <?php }
        } ?>
    </div><!-- /.Tab panel -->
    <div class="eztoc_support_div eztoc-tabcontent" id="welcome" style="display: block;">
        <p style="font-weight: bold;font-size: 30px;color: #000;"><?= esc_html_e( 'Thank YOU for using Easy Table of Content.', 'easy-table-of-contents' ) ?></p>
        <p style="font-size: 18px;padding: 0 10%;line-height: 1.7;color: #000;"><?= esc_html_e( 'We strive to create the best TOC solution in WordPress. Our dedicated development team does continuous development and innovation to make sure we are able to meet your demand.', 'easy-table-of-contents' ) ?></p>
        <p style="font-size: 16px;font-weight: 600;color: #000;"><?= esc_html_e( 'Please support us by Upgrading to Premium version.', 'easy-table-of-contents' ) ?></p>
        <a target="_blank" href="https://tocwp.com/pricing/">
            <button class="button-toc" style="display: inline-block;font-size: 20px;">
                <span><?= esc_html_e( 'YES! I want to Support by UPGRADING.', 'easy-table-of-contents' ) ?></span></button>
        </a>
        <a href="<?php echo add_query_arg('page', 'table-of-contents', admin_url('options-general.php')); ?>"
           style="text-decoration: none;">
            <button class="button-toc1"
                    style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">
                <span style="cursor: pointer;"><?= esc_html_e( 'No Thanks, I will stick with FREE version for now.', 'easy-table-of-contents' ) ?></span>
            </button>
        </a>
    </div>
    <div class="eztoc-tabcontent" id="general">
        <div id="eztoc-tabs" style="margin-top: 10px;">
            <a href="#eztoc-general"><?= esc_html_e( 'General', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-appearance"><?= esc_html_e( 'Appearance', 'easy-table-of-contents' ) ?></a> | <a
                    href="#eztoc-advanced"><?= esc_html_e( 'Advanced', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-shortcode"><?= esc_html_e( 'Shortcode', 'easy-table-of-contents' ) ?></a>
        </div>
        <form method="post" action="<?php echo esc_url(self_admin_url('options.php')); ?>">

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-general">
                    <h3><span><?= esc_html_e('General', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_general', 'ez_toc_settings_general'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-appearance">
                    <h3><span><?= esc_html_e('Appearance', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_appearance', 'ez_toc_settings_appearance'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-advanced">
                    <h3><span><?= esc_html_e('Advanced', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_advanced', 'ez_toc_settings_advanced'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-shortcode">
                    <h3><span><?= esc_html_e('Shortcode', 'easy-table-of-contents'); ?></span></h3>
                    <div class="inside">

                        <table class="form-table">
                            <?php do_settings_fields('ez_toc_settings_shortcode', 'ez_toc_settings_shortcode'); ?>
                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->
            <?php if (function_exists('ez_toc_pro_activation_link')) { ?>
                <div class="metabox-holder">

                    <div class="postbox" id="eztoc-prosettings">
                        <h3><span><?= esc_html_e('PRO Settings', 'easy-table-of-contents'); ?></span></h3>
                        <div class="inside">

                            <table class="form-table">
                                <?php do_settings_fields('ez_toc_settings_prosettings', 'ez_toc_settings_prosettings'); ?>

                            </table>

                        </div><!-- /.inside -->
                    </div><!-- /.postbox -->

                </div><!-- /.metabox-holder -->
            <?php } ?>
            <?php settings_fields('ez-toc-settings'); ?>
            <?php submit_button(esc_html('Save Changes', 'easy-table-of-contents')); ?>
        </form>
    </div><!-- /.General Settings ended -->


    <div class="eztoc_support_div eztoc-tabcontent" id="technical">
        <div id="eztoc-tabs-technical">
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-support',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical active"><?php echo esc_html_e('Technical Support', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-how-to-use',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html_e('How to Use', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-shortcode',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html_e('Shortcode', 'easy-table-of-contents') ?></a>
            |
            <a href="https://tocwp.com/docs/" target="_blank" class="eztoc-tablinks-technical"><?php echo
                esc_html_e('Documentation', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-hooks-for-developers',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html_e('Hooks (for Developers)', 'easy-table-of-contents') ?></a>
        </div>
        <div class="eztoc-form-page-ui">
            <div class="eztoc-left-side">
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-support">
                    <h1><?= esc_html_e('Technical Support', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php echo esc_html_e('We are dedicated to provide Technical support & Help to our users. Use the below form for sending your questions.', 'easy-table-of-contents') ?> </p>
                    <p><?php echo esc_html_e('You can also contact us from ', 'easy-table-of-contents') ?><a
                                href="https://tocwp.com/contact/">https://tocwp.com/contact/</a>.</p>

                    <div class="eztoc_support_div_form" id="technical-form">
                        <ul>
                            <li>
                                <label class="support-label"><?= esc_html_e( 'Email', 'easy-table-of-contents' ) ?><span class="star-mark">*</span></label>
                                <div class="support-input">

                                    <input type="text" id="eztoc_query_email" name="eztoc_query_email"
                                           placeholder="<?= esc_html_e( 'Enter your Email', 'easy-table-of-contents' ) ?>" required/>
                                </div>
                            </li>

                            <li>
                                <label class="support-label"><?= esc_html_e( 'Query', 'easy-table-of-contents' ) ?><span class="star-mark">*</span></label>

                                <div class="support-input">
                                    <label for="eztoc_query_message">
                                    <textarea rows="5" cols="50" id="eztoc_query_message"
                                              name="eztoc_query_message"
                                              placeholder="Write your query"></textarea></label>
                                </div>


                            </li>


                            <li>
                                <div class="eztoc-customer-type">
                                    <label class="support-label"><?= esc_html_e( 'Type', 'easy-table-of-contents' ) ?></label>
                                    <div class="support-input">
                                        <select name="eztoc_customer_type" id="eztoc_customer_type">
                                            <option value="select"><?= esc_html_e( 'Select Customer Type', 'easy-table-of-contents' ) ?></option>
                                            <option value="paid"><?= esc_html_e( 'Paid', 'easy-table-of-contents' ) ?><span> <?= esc_html_e( '(Response within 24 hrs)', 'easy-table-of-contents' ) ?></span>
                                            </option>
                                            <option value="free">
                                                <?= esc_html_e( 'Free', 'easy-table-of-contents' ) ?><span> <?= esc_html_e( '( Avg Response within 48-72 hrs)', 'easy-table-of-contents' ) ?></span>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <button class="button button-primary eztoc-send-query"><?php echo esc_html_e('Send Support Request', 'easy-table-of-contents'); ?></button>
                            </li>
                        </ul>
                        <div class="clear"></div>
                        <span class="eztoc-query-success eztoc-result eztoc_hide"><?php echo esc_html_e('Message sent successfully, Please wait we will get back to you shortly', 'easy-table-of-contents'); ?></span>
                        <span class="eztoc-query-error eztoc-result eztoc_hide"><?php echo esc_html_e('Message not sent. please check your network connection', 'easy-table-of-contents'); ?></span>
                    </div>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-how-to-use" style="display:
                none;">
                    <h1><?= esc_html_e('How to Use', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?= esc_html_e('You can check how to use `Easy Table of Contents`, follow the basic details below.', 'easy-table-of-contents'); ?></p>
                    <h3><?= esc_html_e('1. AUTOMATICALLY', 'easy-table-of-contents'); ?></h3>
                    <ol>
                        <li><?= esc_html_e('Go to the tab Settings &gt; General section, check Auto Insert', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('Select the post types which will have the table of contents automatically inserted.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html_e('NOTE: The table of contents will only be automatically inserted on post types for which it has been enabled.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html_e('After Auto Insert, the Position option for choosing where you want to display the `Easy Table of Contents`.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?= esc_html_e('2. MANUALLY', 'easy-table-of-contents'); ?></h3>
                    <p><?= esc_html_e('There are two ways for manual adding & display `Easy Table of Contents`:', 'easy-table-of-contents');
                        ?></p>
                    <ol>
                        <li><?= esc_html_e('Using shortcode, you can copy shortcode and paste the shortcode on editor of any post type.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('Using Insert table of contents option on editor of any post type.',
                                'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You have to choose post types on tab General &gt; Enable Support section then `Easy Table of Contents` editor options would be shown to choose settings for particular post type.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?= esc_html_e('3. DESIGN CUSTOMIZATION', 'easy-table-of-contents');
                        ?></h3>
                    <ol>
                        <li><?= esc_html_e('Go to tab Settings &gt; Appearance for design customization.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You can change width of `Easy Table of Contents` from select Fixed or Relative sizes or you select custom width then it will be showing custom width option for enter manually width.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You can also choose Alignment of `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You can also set Font Option of `Easy Table of Contents` according to your needs.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You can also choose Theme color of `Easy Table of Contents` on Theme Options section according to your choice.', 'easy-table-of-contents');
                            ?></li>
                        <li><?= esc_html_e('You can also choose Custom Theme colors of `Easy Table of Contents`. according to your requirements', 'easy-table-of-contents');
                            ?></li>
                    </ol>
                    <h3><?= esc_html_e('4. MORE DOCUMENTATION:', 'easy-table-of-contents'); ?></h3>
                    <p><?= esc_html_e('You can visit this link ', 'easy-table-of-contents') . '<a href="https://tocwp.com/docs/" target="_blank">' . esc_html_e('More Documentation', 'easy-table-of-contents') . '</a>' . esc_html_e(' for more documentation of `Easy Table of Contents`', 'easy-table-of-contents'); ?></p>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-shortcode" style="display: none;">
                    <h1><?= esc_html_e('Shortcode', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?= esc_html_e('Use the following shortcode within your content to have the table of contents display where you wish to:', 'easy-table-of-contents'); ?></p>
                    <table class="form-table">
                        <?php do_settings_fields('ez_toc_settings_shortcode', 'ez_toc_settings_shortcode'); ?>
                    </table>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-hooks-for-developers" style="display:
                none;">
                    <h1><?= esc_html_e('Hooks (for Developers)', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php echo esc_html_e('This plugin has been designed for easiest way & best features for the users & also as well as for the developers, any developer follow the below advanced instructions:', 'easy-table-of-contents') ?> </p>

                    <h2><?php echo esc_html_e('Hooks', 'easy-table-of-contents') ?></h2>
                    <p><?php echo esc_html_e('Developer can use these below hooks for customization of this plugin:', 'easy-table-of-contents')
                        ?></p>
                    <h4><?php echo esc_html_e('Actions:', 'easy-table-of-contents') ?></h4>
                    <ul>
                        <li><code><?php echo esc_html_e('ez_toc_before', 'easy-table-of-contents') ?></code>
                        </li>
                        <li><code><?php echo esc_html_e('ez_toc_after', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php echo esc_html_e('ez_toc_sticky_toggle_before', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php echo esc_html_e('ez_toc_sticky_toggle_after', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php echo esc_html_e('ez_toc_before_widget_container', 'easy-table-of-contents')
                                ?></code></li>
                        <li><code><?php echo esc_html_e('ez_toc_before_widget', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php echo esc_html_e('ez_toc_after_widget_container', 'easy-table-of-contents') ?></code>
                        </li>
                        <li><code><?php echo esc_html_e('ez_toc_after_widget', 'easy-table-of-contents')
                                ?></code></li>
                    </ul>


                    <h4><?php echo esc_html_e('Example: adding a span tag before the `Easy Table of Contents`',
                            'easy-table-of-contents') ?></h4>
                    <p><?php echo esc_html_e("Get this following code and paste into your theme\'s function.php file:", 'easy-table-of-contents') ?></p>
                    <pre>
                       <?php
                       $addCustomSpanText = esc_html_e("Some Text or Element here ", 'easy-table-of-contents');
                       echo "
add_action( 'ez_toc_before', 'addCustomSpan' );
function addCustomSpan()
{
    echo '&lt;span&gt;$addCustomSpanText&lt;/span&gt;';
}
                        "; ?>
                    </pre>

                </div>
            </div>
            <div class="eztoc-right-side">
                <div class="eztoc-bio-box" id="ez_Bio">
                    <h1><?php echo esc_html_e("Vision & Mission", 'easy-table-of-contents') ?></h1>
                    <p class="eztoc-p"><?php echo esc_html_e("We strive to provide the best TOC in the world.", 'easy-table-of-contents') ?></p>
                    <section class="eztoc_dev-bio">
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/ahmed-kaludi.jpg', dirname(__FILE__))
                                 ?>" alt="ahmed-kaludi"/>
                            <p><?= esc_html_e('Lead Dev', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/Mohammed-kaludi.jpeg', dirname
                                 (__FILE__)) ?>" alt="Mohammed-kaludi"/>
                            <p><?= esc_html_e('Developer', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/zabi.jpg', dirname(__FILE__)) ?>"
                                 alt="zabi.jpg"/>
                            <p><?= esc_html_e('Developer', 'easy-table-of-contents'); ?></p>
                        </div>
                    </section>
                    <p class="eztoc_boxdesk"><?= esc_html_e('Delivering a good user experience means a lot to us, so we try our best to reply each and every question.', 'easy-table-of-contents'); ?></p>
                    <p class="company-link"><?= esc_html_e('Support the innovation & development by upgrading to PRO ', 'easy-table-of-contents'); ?> <a href="https://tocwp.com/pricing/">I Want To Upgrade!</a></p>
                </div>
            </div>
        </div>
    </div>        <!-- /.Technical support div ended -->

    <div class="eztoc_support_div eztoc-tabcontent" id="freevspro">
        <div class="eztoc-wrapper">
            <div class="eztoc-wr">
                <div class="etoc-eztoc-img">
                    <span class="sp_ov"></span>
                </div>
                <div class="etoc-eztoc-cnt">
                    <h1><?= esc_html_e('UPGRADE to PRO Version'); ?></h1>
                    <p><?= esc_html_e('Take your Table of Contents to the NEXT Level!', 'easy-table-of-contents'); ?></p>
                    <a class="buy" href="#upgrade"><?= esc_html_e('Purchase Now', 'easy-table-of-contents'); ?></a>
                </div>
                <div class="pvf">
                    <div class="ext">
                        <div class="ex-1 e-1">
                            <h4><?= esc_html_e('Premium Features', 'easy-table-of-contents'); ?></h4>
                            <p><?= esc_html_e('Easy TOC Pro will enhances your website table of contents and takes it to a next level to help you reach more engagement and personalization with your users.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-2">
                            <h4><?= esc_html_e('Continuous Innovation', 'easy-table-of-contents'); ?></h4>
                            <p><?= esc_html_e('We are planning to continiously build premium features and release them. We have a roadmap and we listen to our customers to turn their feedback into reality.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-3">
                            <h4><?= esc_html_e('Tech Support', 'easy-table-of-contents'); ?></h4>
                            <p><?= esc_html_e('Get private ticketing help from our full-time technical staff & developers who helps you with the technical issues.', 'easy-table-of-contents'); ?></p>
                        </div>
                    </div><!-- /. ext -->
                    <div class="pvf-cnt">
                        <div class="pvf-tlt">
                            <h2><?= esc_html_e('Compare Pro vs. Free Version', 'easy-table-of-contents'); ?></h2>
                            <span><?= esc_html_e('See what you\'ll get with the professional version', 'easy-table-of-contents'); ?></span>
                        </div>
                        <div class="pvf-cmp">
                            <div class="fr">
                                <h1><?= esc_html_e('FREE', 'easy-table-of-contents'); ?></h1>
                                <div class="fr-fe">
                                    <div class="fe-1">
                                        <h4><?= esc_html_e('Continious Development', 'easy-table-of-contents'); ?></h4>
                                        <p><?= esc_html_e('We take bug reports and feature requests seriously. We’re continiously developing &amp; improve this product for last 2 years with passion and love.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                    <div class="fe-1">
                                        <h4><?= esc_html_e('50+ Features', 'easy-table-of-contents'); ?></h4>
                                        <p><?= esc_html_e('We\'re constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                </div><!-- /. fr-fe -->
                            </div><!-- /. fr -->
                            <div class="pr">
                                <h1><?= esc_html_e('PRO', 'easy-table-of-contents'); ?></h1>
                                <div class="pr-fe">
                                    <span><?= esc_html_e('Everything in Free, and:', 'easy-table-of-contents'); ?></span>
                                    <div class="fet">
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e('Gutenberg Block', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e('Easily create TOC in Gutenberg block without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e('Elementor Widget', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e('Easily create TOC in Elementor with the widget without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e('Fixed/Sticky TOC', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e('Users can faster find the content they want with sticky', 'easy-table-of-contents'); ?></p>
                                        </div>


                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e('Full AMP Support', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e('Generates a table of contents with your existing setup and makes them AMP automatically.', 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e('Continuous Updates', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e("We're continuously updating our premium features and releasing them.", 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?= esc_html_e("Documentation", 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?= esc_html_e("We create tutorials for every possible feature and keep it updated for you.", 'easy-table-of-contents'); ?></p>
                                        </div>
                                    </div><!-- /. fet -->
                                    <div class="pr-btn">
                                        <a href="#upgrade"><?= esc_html_e("Upgrade to Pro", 'easy-table-of-contents'); ?></a>
                                    </div><!-- /. pr-btn -->
                                </div><!-- /. pr-fe -->
                            </div><!-- /.pr -->
                        </div><!-- /. pvf-cmp -->
                    </div><!-- /. pvf-cnt -->
                    <div id="upgrade" class="amp-upg">
                        <div class="upg-t">
                            <h2><?= esc_html_e("Let's Upgrade Your Easy Table of Contents", 'easy-table-of-contents'); ?></h2>
                            <span><?= esc_html_e("Choose your plan and upgrade in minutes!", 'easy-table-of-contents'); ?></span>
                        </div>
                        <div class="etoc-pri-lst">
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=1"
                                   target="_blank">
                                    <h5><?= esc_html_e("PERSONAL", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>49</span>
                                    <span class="amt"><sup>$</sup>49</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $59)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("1 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("1 year Updates", 'easy-table-of-contents'); ?> </span>
                                    <span class="etoc-sv"><?= esc_html_e("Pro Features", 'easy-table-of-contents'); ?> </span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb rec">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=2"
                                   target="_blank">
                                    <h5><?= esc_html_e("MULTIPLE", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>69</span>
                                    <span class="amt"><sup>$</sup>69</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $79)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("3 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?= esc_html_e("Save 78%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-rcm"><?= esc_html_e("RECOMMENDED", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=3"
                                   target="_blank">
                                    <h5><?= esc_html_e("WEBMASTER", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>79</span>
                                    <span class="amt"><sup>$</sup>79</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $99)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("10 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?= esc_html_e("Save 83%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=4"
                                   target="_blank">
                                    <h5><?= esc_html_e("FREELANCER", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>99</span>
                                    <span class="amt"><sup>$</sup>99</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $119)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("25 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?= esc_html_e("Save 90%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=5"
                                   target="_blank">
                                    <h5><?= esc_html_e("AGENCY", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>199</span>
                                    <span class="amt"><sup>$</sup>199</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $199)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("Unlimited Sites", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("E-mail support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?= esc_html_e("UNLIMITED", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=6"
                                   target="_blank">
                                    <h5><?= esc_html_e("LIFETIME", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>499</span>
                                    <span class="amt"><sup>$</sup>499</span>
                                    <span class="s-amt"><?= esc_html_e("(Save $199)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?= esc_html_e("One-Time Fee", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?= esc_html_e("Unlimited Sites", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?= esc_html_e("Unlimited E-mail support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?= esc_html_e("Lifetime License", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?= esc_html_e("UNLIMITED", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?= esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                        </div><!-- /.pri-lst -->
                        <div class="tru-us">
                            <img src="<?php echo plugins_url('assets/toc-rating.png', dirname(__FILE__))
                            ?>" alt="toc-rating"/>
                            <h2><?= esc_html_e("Used by more than 3,00,000+ Users!", 'easy-table-of-contents'); ?></h2>
                            <p><?= esc_html_e("More than 300k Websites, Blogs &amp; E-Commerce shops are powered by our easy table of contents plugin making it the #1 Independent TOC plugin in WordPress.", 'easy-table-of-contents'); ?></p>
                            <a href="https://wordpress.org/support/plugin/easy-table-of-contents/reviews/?filter=5"
                               target="_blank"><?= esc_html_e("Read The Reviews", 'easy-table-of-contents'); ?></a>
                        </div>
                    </div><!--/ .amp-upg -->
                    <div class="ampfaq">
                        <h2><?= esc_html_e("Frequently Asked Questions", 'easy-table-of-contents'); ?></h2>
                        <div class="faq-lst">
                            <div class="lt">
                                <ul>
                                    <li>
                                        <span><?= esc_html_e("Is there a setup fee?", 'easy-table-of-contents'); ?></span>
                                        <p><?= esc_html_e("No. There are no setup fees on any of our plans", 'easy-table-of-contents'); ?></p>
                                    </li>
                                    <li>
                                        <span><?= esc_html_e("What's the time span for your contracts?", 'easy-table-of-contents'); ?></span>
                                        <p><?= esc_html_e("All the plans are year-to-year which are subscribed annually except for lifetime plan.", 'easy-table-of-contents'); ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("What payment methods are accepted?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("We accepts PayPal and Credit Card payments.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("Do you offer support if I need help?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("Yes! Top-notch customer support for our paid customers is key for a quality product, so we’ll do our very best to resolve any issues you encounter via our support page.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("Can I use the plugins after my subscription is expired?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("Yes, you can use the plugins, but you will not get future updates for those plugins.", 'easy-table-of-contents') ?></p>
                                    </li>
                                </ul>
                            </div>
                            <div class="rt">
                                <ul>
                                    <li>
                                        <span><?php echo esc_html_e("Can I cancel my membership at any time?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("Yes. You can cancel your membership by contacting us.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("Can I change my plan later on?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("Yes. You can upgrade your plan by contacting us.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("Do you offer refunds?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("You are fully protected by our 100% Money-Back Guarantee Unconditional. If during the next 14 days you experience an issue that makes the plugin unusable, and we are unable to resolve it, we’ll happily offer a full refund.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php echo esc_html_e("Do I get updates for the premium plugin?", 'easy-table-of-contents') ?></span>
                                        <p><?php echo esc_html_e("Yes, you will get updates for all the premium plugins until your subscription is active.", 'easy-table-of-contents') ?></p>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.faq-lst -->
                        <div class="f-cnt">
                            <span><?php echo esc_html_e("I have other pre-sale questions, can you help?", 'easy-table-of-contents') ?></span>
                            <p><?php echo esc_html_e("All the plans are year-to-year which are subscribed annually.", 'easy-table-of-contents') ?></p>
                            <a href="https://tocwp.com/contact/'?utm_source=tocwp-plugin&utm_medium=addon-card'"
                               target="_blank"><?php echo esc_html_e("Contact a Human", 'easy-table-of-contents') ?></a>
                        </div><!-- /.f-cnt -->
                    </div><!-- /.faq -->
                </div><!-- /. pvf -->
            </div>
        </div>
    </div><!-- /.freevspro div ended -->

    <div id="license" class="eztoc_support_div eztoc-tabcontent">
        <?php
        do_action("admin_upgrade_license_page");
        ?>
    </div>
</div>
