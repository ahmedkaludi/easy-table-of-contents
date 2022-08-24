<div id='toc' class='wrap'>
    <a href="https://tocwp.com/" target="_blank">
        <img src="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?>" alt="tocwp"
             srcset="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 1x, <?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 2x">
    </a>
    <h1 style="display:none;">&nbsp;</h1>
    <div class="toc-tab-panel">
        <a id="eztoc-welcome" class="eztoc-tablinks" data-href="no" href="#welcome"
           onclick="ezTocTabToggle(event, 'welcome')">Welcome</a>
        <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings"
           onclick="ezTocTabToggle(event, 'general')">Settings</a>
        <?php
        $pro = '';

        if (function_exists('ez_toc_pro_activation_link')) {
            $pro = '<a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#eztoc-prosettings" onclick="ezTocTabToggle(event, \'prosettings\')">PRO Settings</a>';
        } ?>
        <?php echo $pro; ?>

        <?php
        if (!function_exists('ez_toc_pro_activation_link')) { ?>
            <a class="eztoc-tablinks" id="eztoc-freevspro" href="#freevspro-support"
               onclick="ezTocTabToggle(event, 'freevspro')" data-href="no">Free vs PRO</a>
        <?php }
        ?>
        <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support"
           onclick="ezTocTabToggle(event, 'technical')" data-href="no">Help & Support</a>
        <a class="eztoc-tablinks" id="eztoc-upgrade" href="https://tocwp.com/pricing/" target="_blank">UPGRADE
            to PRO</a>
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
               data-href="no">License</a>
            <?php
            if ($days < 30) {
                ?>
                <span class="dashicons dashicons-warning" style="color: #ffb229;position: relative;top:
                15px;left: -10px;"></span>
            <?php }
        } ?>
    </div><!-- /.Tab panel -->
    <div class="eztoc_support_div eztoc-tabcontent" id="welcome" style="display: block;">
        <p style="font-weight: bold;font-size: 30px;color: #000;">Thank YOU for using Easy Table of Content
            . </p>
        <p style="font-size: 18px;padding: 0 10%;line-height: 1.7;color: #000;">We strive to create the best
            TOC solution in WordPress. Our dedicated development team does continious development and
            innoviation to make sure we are able to meet your demand.</p>
        <p style="font-size: 16px;font-weight: 600;color: #000;">Please support us by Upgrading to Premium
            verison.</p>
        <a target="_blank" href="https://tocwp.com/pricing/">
            <button class="button-toc" style="display: inline-block;font-size: 20px;">
                <span>YES! I want to Support by UPGRADING.</span></button>
        </a>
        <a href="<?php echo add_query_arg('page', 'table-of-contents', admin_url('options-general.php')); ?>"
           style="text-decoration: none;">
            <button class="button-toc1"
                    style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">
                <span style="cursor: pointer;">No Thanks, I will stick with FREE version for now.</span>
            </button>
        </a>
    </div><!-- /.Welcome ended -->
    <div class="eztoc-tabcontent" id="general">
        <div id="eztoc-tabs" style="margin-top: 10px;">
            <a href="#eztoc-general">General</a> | <a href="#eztoc-appearance">Appearance</a> | <a
                    href="#eztoc-advanced">Advanced</a> | <a href="#eztoc-shortcode">Shortcode</a>
        </div>
        <form method="post" action="<?php echo esc_url(self_admin_url('options.php')); ?>">

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-general">
                    <h3><span><?php _e('General', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_general', 'ez_toc_settings_general'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-appearance">
                    <h3><span><?php _e('Appearance', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_appearance', 'ez_toc_settings_appearance'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-advanced">
                    <h3><span><?php _e('Advanced', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_advanced', 'ez_toc_settings_advanced'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-shortcode">
                    <h3><span><?php _e('Shortcode', 'easy-table-of-contents'); ?></span></h3>
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
                        <h3><span><?php _e('PRO Settings', 'easy-table-of-contents'); ?></span></h3>
                        <div class="inside">

                            <table class="form-table">
                                <?php do_settings_fields('ez_toc_settings_prosettings', 'ez_toc_settings_prosettings'); ?>

                            </table>

                        </div><!-- /.inside -->
                    </div><!-- /.postbox -->

                </div><!-- /.metabox-holder -->
            <?php } ?>
            <?php settings_fields('ez-toc-settings'); ?>
            <?php submit_button(__('Save Changes', 'easy-table-of-contents')); ?>
        </form>
    </div><!-- /.General Settings ended -->


    <div class="eztoc_support_div eztoc-tabcontent" id="technical">
        <div id="eztoc-tabs-technical">
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-support',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical active"><?php echo esc_html__('Technical Support', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-how-to-use',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html__('How to Use', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-shortcode',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html__('Shortcode', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-documentation',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html__('Documentation', 'easy-table-of-contents') ?></a>
            |
            <a href="javascript:void(0)" onclick="ezTocTabToggle(event, 'eztoc-technical-hooks-for-developers',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php echo esc_html__('Hooks (for Developers)', 'easy-table-of-contents') ?></a>
        </div>
        <div class="eztoc-form-page-ui">
            <div class="eztoc-left-side">
                <div class="ez-toc-pull-technical-img">
                    <a href="https://tocwp.com/" target="_blank">
                        <img src="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?>"
                             alt="tocwp"
                             srcset="<?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 1x, <?php echo plugins_url('assets/eztoc-logo.png', dirname(__FILE__)) ?> 2x">
                    </a>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-support">
                    <h1><?= esc_html__('Technical Support', 'easy-table-of-contents'); ?></h1>
                    <p><?php echo esc_html__('We are dedicated to provide Technical support & Help to our users. Use the below form for sending your questions.', 'easy-table-of-contents') ?> </p>
                    <p><?php echo esc_html__('You can also contact us from ', 'easy-table-of-contents') ?><a
                                href="https://tocwp.com/contact/">https://tocwp.com/contact/</a>.</p>

                    <div class="eztoc_support_div_form" id="technical-form">
                        <ul>
                            <li>
                                <label class="support-label">Email<span class="star-mark">*</span></label>
                                <div class="support-input">

                                    <input type="text" id="eztoc_query_email" name="eztoc_query_email"
                                           placeholder="Enter your Email" required/>
                                </div>
                            </li>

                            <li>
                                <label class="support-label">Query<span class="star-mark">*</span></label>

                                <div class="support-input">
                                    <label for="eztoc_query_message">
                                    <textarea rows="5" cols="50" id="eztoc_query_message"
                                              name="eztoc_query_message"
                                              placeholder="Write your query"></textarea></label>
                                </div>


                            </li>


                            <li>
                                <div class="eztoc-customer-type">
                                    <label class="support-label">Type</label>
                                    <div class="support-input">
                                        <select name="eztoc_customer_type" id="eztoc_customer_type">
                                            <option value="select">Select Customer Type</option>
                                            <option value="paid">Paid<span> (Response within 24 hrs)</span>
                                            </option>
                                            <option value="free">
                                                Free<span> ( Avg Response within 48-72 hrs)</span>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <button class="button button-primary eztoc-send-query"><?php echo esc_html__('Send Support Request', 'easy-table-of-contents'); ?></button>
                            </li>
                        </ul>
                        <div class="clear"></div>
                        <span class="eztoc-query-success eztoc-result eztoc_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly', 'easy-table-of-contents'); ?></span>
                        <span class="eztoc-query-error eztoc-result eztoc_hide"><?php echo esc_html__('Message not sent. please check your network connection', 'easy-table-of-contents'); ?></span>
                    </div>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-how-to-use"
                     style="display: none;">
                    <h1><?= esc_html__('How to Use', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?= esc_html__('Once you have activated the
                        plugin, follow the basic customization below.', 'easy-table-of-contents'); ?></p>
                    <h3><?= esc_html__('1. Under GENERAL &gt; ENABLE SUPPORT', 'easy-table-of-contents'); ?></h3>
                    <ol>
                        <li><?= esc_html__('Choose a whether you want to show Easy Table of Contents.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Choose various Post Types which defined in your wordpress setup.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?= esc_html__('2. Under GENERAL &gt; AUTO-INSERT TABLE OF CONTENTS', 'easy-table-of-contents'); ?></h3>
                    <p><?= esc_html__('If you want to use the auto-insert feature:', 'easy-table-of-contents'); ?></p>
                    <ol>
                        <li><?= esc_html__('Select which post types you would like to have the TOC display.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Choose the TOC Position.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Show the TOC when how much heading at least.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?= esc_html__('3. Under GENERAL &gt; STICKY TOGGLE OPTIONS', 'easy-table-of-contents'); ?></h3>
                    <ol>
                        <li><?= esc_html__('Choose On/Off for showing TOC as Sticky Toggle.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Choose width & height for customization Sticky Toggle Bar.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Write Open Button Text for showing on open bar of Sticky Toggle.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?= esc_html__('4. Under APPEARANCE &gt; THEMES', 'easy-table-of-contents'); ?></h3>
                    <ol>
                        <li><?= esc_html__('Choose a theme.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Select your preferred Title alignment.', 'easy-table-of-contents'); ?></li>
                        <li><?= esc_html__('Customize any of the Appearance settings to override theme defaults.', 'easy-table-of-contents'); ?></li>
                    </ol>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-shortcode" style="display: none;">
                    <h1><?= esc_html__('Shortcode', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?= esc_html__('Use the following shortcode within your content to have the table of contents display where you wish to:', 'easy-table-of-contents'); ?></p>
                    <table class="form-table">
                        <?php do_settings_fields('ez_toc_settings_shortcode', 'ez_toc_settings_shortcode'); ?>
                    </table>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-documentation"
                     style="display: none;">
                    <h1><?= esc_html__('Documentation', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php echo esc_html__('Easy Table Of Contents can be displayed in various manners from a simple, to a modern floating element.', 'easy-table-of-contents') ?> </p>
                    <p>It has a unique feature the allows it to show the current heading section while
                        scrolling</p>
                    <p>Here is a description of each behaviour:</p>

                    <h3>1. Invisible, floating on scroll<span class="joli-pro-only"> (Pro only)</span></h3>
                    <p>Table Of Contents is invisible after page is loaded. It only shows on scroll through a
                        floating widget</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <p>
                                <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/invisible-floating.gif"
                                     alt=""></p>
                        </section>
                    </details>

                    <h3>2. Unfolded, in-content</h3>
                    <p>Table Of Contents is unfolded after page is loaded. It remains in its position and no
                        widget shows on scroll.</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <p>
                                <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/unfolded-incontent.gif"
                                     alt=""></p>
                        </section>
                    </details>

                    <h3>3. Unfolded, folded &amp; floating on scroll<span
                                class="joli-pro-only"> (Pro only)</span></h3>
                    <p>Table Of Contents is unfolded after page is loaded. It also shows on scroll through a
                        floating widget.</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <p>
                                <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/unfolded-floating.gif"
                                     alt=""></p>
                        </section>
                    </details>

                    <h3>4. Unfolded, unfolded &amp; floating on scroll<span
                                class="joli-pro-only"> (Pro only)</span></h3>
                    <p>Table Of Contents is unfolded after page is loaded. It remains unfolded and floating on
                        scroll.</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <!-- <p><img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/invisible-floating.gif" alt=""></p> -->
                        </section>
                    </details>

                    <h3>5. Folded, in-content</h3>
                    <p>Table Of Contents is folded after page is loaded. It can be unfolded at will. It
                        remains in its position and no widget shows on scroll.</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <p>
                                <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/folded-incontent.gif"
                                     alt=""></p>
                        </section>
                    </details>

                    <h3>6. Folded, folded &amp; floating on scroll<span
                                class="joli-pro-only"> (Pro only)</span></h3>
                    <p>Table Of Contents is folded after page is loaded. It can be unfolded at will. It also
                        shows on scroll through a floating widget.</p>
                    <details class="primer" style="display: inline-block; width: 100%;">
                        <summary title="View demo">Click here to view demo</summary>
                        <section>
                            <p>
                                <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/folded-floating.gif"
                                     alt=""></p>
                        </section>
                    </details>

                    <h2>Globally disabling the Table of Contents</h2>
                    <p>For some reason, you may need to deactivate the table of contents without touching your
                        content. To do so:</p>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <p><code>add_filter('joli_toc_disable_autoinsert', function(){ return true; });</code></p>
                    <p>This will disable both shortcodes and auto-insert from running.</p>

                    <h2>Disable Javascript</h2>
                    <p>This filter will prevent the javascript from loading. It is not recommanded unless you
                        know exactly what you are doing.</p>
                    <p>By doing so, you may break some of the plugin's functionalities including smooth
                        scrolling and jump-to offset</p>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <p><code>add_filter('joli_toc_disable_js', function(){ return true; });</code></p>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-hooks-for-developers" style="display:
                none;">
                    <h1><?= esc_html__('Hooks (for Developers)', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php echo esc_html__('Because of the way Joli Table Of Contents has been designed, you must use the "joli-div" class to any of the content you may want to add or it will break the normal behaviour of the plugin.', 'easy-table-of-contents') ?> </p>
                    <h4>Do:</h4>
                    <code style="color: darkgreen;">&lt;div class="joli-div"&gt;My content&lt;/div&gt;</code>

                    <h4>Don't:</h4>
                    <code style="color: red;">&lt;div&gt;My content&lt;/div&gt;</code>

                    <h2>Hooks</h2>
                    <p>Joli Table Of Contents is developer friendly and allows you to hook onto it.</p>
                    <p>You can use the following hooks:</p>

                    <h3>Actions:</h3>
                    <ul>
                        <li><code>joli_toc_before_table_of_contents</code></li>
                        <li><code>joli_toc_before_title</code></li>
                        <li><code>joli_toc_after_title</code></li>
                        <li><code>joli_toc_after_headings</code></li>
                        <li><code>joli_toc_after_table_of_contents</code></li>
                    </ul>

                    <p>
                        <img src="http://localhost/wordpress/wp-content/plugins/joli-table-of-contents/assets/admin/img/wpjoli-joli-toc-hooks-action.png"
                             alt=""></p>

                    <h4>Example: adding a horizontal bar after the title</h4>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <p>
                    </p>
                    <pre>
                        add_action( 'joli_toc_after_title', 'echo_hr' );
                        function echo_hr(){
                            echo &lt;hr class="joli-div"&gt;;
                        }
                    </pre>
                    <p></p>
                    <p class="warning">Do not forget to add the "joli-div" class to your content</p>

                    <h3>Filters:</h3>
                    <p>Use the following filters to customize the Table of contents toggle button</p>

                    <h4>Customize the "expand" button toggle</h4>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <code>add_filter('joli_toc_expand_str', function(){ return '&gt;';});</code>
                    <p>If you are use Font awesome, you can use the icons like so:</p>
                    <code>add_filter('joli_toc_expand_str', function(){ return '&lt;i class="fa fa-caret-down"&gt;&lt;/i&gt;';});</code>

                    <h4>Customize the "collapse" button toggle</h4>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <code>add_filter('joli_toc_collapse_str', function(){ return '˅';});</code>
                    <p>If you are use Font awesome, you can use the icons like so:</p>
                    <code>add_filter('joli_toc_collapse_str', function(){ return '&lt;i class="fa
                        fa-caret-right"&gt;&lt;/i&gt;';});</code>

                    <hr>
                    <h4>Filter out the headings input</h4>
                    <p>Before processing the TOC, an array of headings is passed after the content has been
                        analyzed</p>
                    <p>This array looks like as follows and can be modified easily to meet your needs</p>
                    <pre>
                    Array
                        (
                            [0] =&gt; Array
                                (
                                    [id] =&gt; my-title-1
                                    [title] =&gt; My title 1
                                    [icon] =&gt;
                                    [depth] =&gt; 2
                                )

                            [1] =&gt; Array
                                (
                                    [id] =&gt; my-title-2
                                    [title] =&gt; My title 2
                                    [icon] =&gt;
                                    [depth] =&gt; 3
                                )
                                ...
                        )
                    </pre>
                    <p>Copy &amp; paste the following code into your theme's function.php file:</p>
                    <h5>Ex 1: Removing every first heading</h5>
                    <pre>

                        add_filter('joli_toc_headings', 'filter_headings', 10, 2);

                        function filter_headings( $headings ){
                            //Removes the first element
                            array_shift($headings);
                            return $headings;
                        }
                    </pre>

                    <h5>Ex 2: Adds " - AWESOME!" as a suffix for every h2 title</h5>
                    <pre>
                        add_filter('joli_toc_headings', 'filter_headings', 10, 2);
                        function filter_headings( $headings ){
                            //Adds ' - AWESOME!' as a prefix for every h2 title
                            array_shift($headings);
                            $headings = array_map(function($heading){
                                //Target H2s only
                                if ($heading['depth'] == 2){
                                    $heading['title'] .= ' - AWESOME!';
                                }
                                return $heading;
                            }, $headings);

                            return $headings;
                        }
                    </pre>
                </div>
            </div>
            <div class="eztoc-right-side">
                <div class="eztoc-bio-box" id="ez_Bio">
                    <h1>Vision & Mission</h1>
                    <p class="eztoc-p">We strive to provide the best TOC in the world.</p>
                    <section class="eztoc_dev-bio">
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/ahmed-kaludi.jpg', dirname(__FILE__))
                                 ?>" alt="ahmed-kaludi"/>
                            <p>Lead Dev</p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/Mohammed-kaludi.jpeg', dirname
                                 (__FILE__)) ?>" alt="Mohammed-kaludi"/>
                            <p>Developer</p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo plugins_url('assets/zabi.jpg', dirname(__FILE__)) ?>"
                                 alt="zabi.jpg"/>
                            <p>Developer</p>
                        </div>
                    </section>
                    <p class="eztoc_boxdesk"> Delivering a good user experience means a lot to us, so we try
                        our best to reply each and every question.</p>
                    <p class="company-link"> Support the innovation & development by upgrading to PRO <a
                                href="https://tocwp.com/pricing/">I Want To Upgrade!</a></p>
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
                    <h1><?php _e('UPGRADE to PRO Version'); ?></h1>
                    <p><?php _e('Take your Table of Contents to the NEXT Level!', 'easy-table-of-contents'); ?></p>
                    <a class="buy" href="#upgrade"><?php _e('Purchase Now', 'easy-table-of-contents'); ?></a>
                </div>
                <div class="pvf">
                    <div class="ext">
                        <div class="ex-1 e-1">
                            <h4><?php _e('Premium Features', 'easy-table-of-contents'); ?></h4>
                            <p><?php _e('Easy TOC Pro will enhances your website table of contents and takes it to a next level to help you reach more engagement and personalization with your users.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-2">
                            <h4><?php _e('Continuous Innovation', 'easy-table-of-contents'); ?></h4>
                            <p><?php _e('We are planning to continiously build premium features and release them. We have a roadmap and we listen to our customers to turn their feedback into reality.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-3">
                            <h4><?php _e('Tech Support', 'easy-table-of-contents'); ?></h4>
                            <p><?php _e('Get private ticketing help from our full-time technical staff & developers who helps you with the technical issues.', 'easy-table-of-contents'); ?></p>
                        </div>
                    </div><!-- /. ext -->
                    <div class="pvf-cnt">
                        <div class="pvf-tlt">
                            <h2><?php _e('Compare Pro vs. Free Version', 'easy-table-of-contents'); ?></h2>
                            <span><?php _e('See what you\'ll get with the professional version', 'easy-table-of-contents'); ?></span>
                        </div>
                        <div class="pvf-cmp">
                            <div class="fr">
                                <h1>FREE</h1>
                                <div class="fr-fe">
                                    <div class="fe-1">
                                        <h4><?php _e('Continious Development', 'easy-table-of-contents'); ?></h4>
                                        <p><?php _e('We take bug reports and feature requests seriously. We’re continiously developing &amp; improve this product for last 2 years with passion and love.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                    <div class="fe-1">
                                        <h4><?php _e('50+ Features', 'easy-table-of-contents'); ?></h4>
                                        <p><?php _e('We\'re constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                </div><!-- /. fr-fe -->
                            </div><!-- /. fr -->
                            <div class="pr">
                                <h1>PRO</h1>
                                <div class="pr-fe">
                                    <span><?php _e('Everything in Free, and:', 'easy-table-of-contents'); ?></span>
                                    <div class="fet">
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?php _e('Gutenberg Block', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php _e('Easily create TOC in Gutenberg block without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4><?php _e('Elementor Widget', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php _e('Easily create TOC in Elementor with the widget without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4>Fixed/Sticky TOC</h4>
                                            </div>
                                            <p>Users can faster find the content they want with sticky</p>
                                        </div>


                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4>Full AMP Support</h4>
                                            </div>
                                            <p>Generates a table of contents with your existing setup and
                                                makes them AMP automatically.</p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4>Continious Updates</h4>
                                            </div>
                                            <p>We're continiously updating our premium features and releasing
                                                them.</p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo plugins_url('assets/right-tick.png',
                                                    dirname(__FILE__)) ?>" alt="right-tick"/>
                                                <h4>Documentation</h4>
                                            </div>
                                            <p>We create tutorials for every possible feature and keep it
                                                updated for you.</p>
                                        </div>
                                    </div><!-- /. fet -->
                                    <div class="pr-btn">
                                        <a href="#upgrade">Upgrade to Pro</a>
                                    </div><!-- /. pr-btn -->
                                </div><!-- /. pr-fe -->
                            </div><!-- /.pr -->
                        </div><!-- /. pvf-cmp -->
                    </div><!-- /. pvf-cnt -->
                    <div id="upgrade" class="amp-upg">
                        <div class="upg-t">
                            <h2>Let's Upgrade Your Easy Table of Contents</h2>
                            <span>Choose your plan and upgrade in minutes!</span>
                        </div>
                        <div class="etoc-pri-lst">
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=1"
                                   target="_blank">
                                    <h5>PERSONAL</h5>
                                    <span class="d-amt"><sup>$</sup>49</span>
                                    <span class="amt"><sup>$</sup>49</span>
                                    <span class="s-amt">(Save $59)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">1 Site License</span>
                                    <span class="e">Tech Support</span>
                                    <span class="f">1 year Updates </span>
                                    <span class="etoc-sv">Pro Features </span>
                                    <span class="pri-by">Buy Now</span>
                                </a>
                            </div>
                            <div class="pri-tb rec">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=2"
                                   target="_blank">
                                    <h5>MULTIPLE</h5>
                                    <span class="d-amt"><sup>$</sup>69</span>
                                    <span class="amt"><sup>$</sup>69</span>
                                    <span class="s-amt">(Save $79)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">3 Site License</span>
                                    <span class="e">Tech Support</span>
                                    <span class="f">1 year Updates</span>
                                    <span class="etoc-sv">Save 78%</span>
                                    <span class="pri-by">Buy Now</span>
                                    <span class="etoc-rcm">RECOMMENDED</span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=3"
                                   target="_blank">
                                    <h5>WEBMASTER</h5>
                                    <span class="d-amt"><sup>$</sup>79</span>
                                    <span class="amt"><sup>$</sup>79</span>
                                    <span class="s-amt">(Save $99)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">10 Site License</span>
                                    <span class="e">Tech Support</span>
                                    <span class="f">1 year Updates</span>
                                    <span class="etoc-sv">Save 83%</span>
                                    <span class="pri-by">Buy Now</span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=4"
                                   target="_blank">
                                    <h5>FREELANCER</h5>
                                    <span class="d-amt"><sup>$</sup>99</span>
                                    <span class="amt"><sup>$</sup>99</span>
                                    <span class="s-amt">(Save $119)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">25 Site License</span>
                                    <span class="e">Tech Support</span>
                                    <span class="f">1 year Updates</span>
                                    <span class="etoc-sv">Save 90%</span>
                                    <span class="pri-by">Buy Now</span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=5"
                                   target="_blank">
                                    <h5>AGENCY</h5>
                                    <span class="d-amt"><sup>$</sup>199</span>
                                    <span class="amt"><sup>$</sup>199</span>
                                    <span class="s-amt">(Save $199)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">Unlimited Sites</span>
                                    <span class="e">E-mail support</span>
                                    <span class="f">1 year Updates</span>
                                    <span class="etoc-sv">UNLIMITED</span>
                                    <span class="pri-by">Buy Now</span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=6"
                                   target="_blank">
                                    <h5>LIFETIME</h5>
                                    <span class="d-amt"><sup>$</sup>499</span>
                                    <span class="amt"><sup>$</sup>499</span>
                                    <span class="s-amt">(Save $199)</span>
                                    <span class="bil">Billed Annually</span>
                                    <span class="s">Unlimited Sites</span>
                                    <span class="e">Unlimited E-mail support</span>
                                    <span class="f">Lifetime License</span>
                                    <span class="etoc-sv">UNLIMITED</span>
                                    <span class="pri-by">Buy Now</span>
                                </a>
                            </div>
                        </div><!-- /.pri-lst -->
                        <div class="tru-us">
                            <img src="<?php echo plugins_url('assets/toc-rating.png', dirname(__FILE__))
                            ?>" alt="toc-rating"/>
                            <h2>Used by more than 3,00,000+ Users!</h2>
                            <p>More than 300k Websites, Blogs &amp; E-Commerce shops are powered by our easy
                                table of contents plugin making it the #1 Independent TOC plugin in
                                WordPress.</p>
                            <a href="https://wordpress.org/support/plugin/easy-table-of-contents/reviews/?filter=5"
                               target="_blank">Read The Reviews</a>
                        </div>
                    </div><!--/ .amp-upg -->
                    <div class="ampfaq">
                        <h2>Frequently Asked Questions</h2>
                        <div class="faq-lst">
                            <div class="lt">
                                <ul>
                                    <li>
                                        <span>Is there a setup fee?</span>
                                        <p>No. There are no setup fees on any of our plans</p>
                                    </li>
                                    <li>
                                        <span>What's the time span for your contracts?</span>
                                        <p>All the plans are year-to-year which are subscribed annually except
                                            for lifetime plan.</p>
                                    </li>
                                    <li>
                                        <span>What payment methods are accepted?</span>
                                        <p>We accepts PayPal and Credit Card payments.</p>
                                    </li>
                                    <li>
                                        <span>Do you offer support if I need help?</span>
                                        <p>Yes! Top-notch customer support for our paid customers is key for a
                                            quality product, so we’ll do our very best to resolve any issues
                                            you encounter via our support page.</p>
                                    </li>
                                    <li>
                                        <span>Can I use the plugins after my subscription is expired?</span>
                                        <p>Yes, you can use the plugins, but you will not get future updates
                                            for those plugins.</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="rt">
                                <ul>
                                    <li>
                                        <span>Can I cancel my membership at any time?</span>
                                        <p>Yes. You can cancel your membership by contacting us.</p>
                                    </li>
                                    <li>
                                        <span>Can I change my plan later on?</span>
                                        <p>Yes. You can upgrade your plan by contacting us.</p>
                                    </li>
                                    <li>
                                        <span>Do you offer refunds?</span>
                                        <p>You are fully protected by our 100% Money-Back Guarantee
                                            Unconditional. If during the next 14 days you experience an issue
                                            that makes the plugin unusable, and we are unable to resolve it,
                                            we’ll happily offer a full refund.</p>
                                    </li>
                                    <li>
                                        <span>Do I get updates for the premium plugin?</span>
                                        <p>Yes, you will get updates for all the premium plugins until your
                                            subscription is active.</p>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.faq-lst -->
                        <div class="f-cnt">
                            <span>I have other pre-sale questions, can you help?</span>
                            <p>All the plans are year-to-year which are subscribed annually.</p>
                            <a href="https://tocwp.com/contact/'?utm_source=tocwp-plugin&utm_medium=addon-card'"
                               target="_blank">Contact a Human</a>
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
