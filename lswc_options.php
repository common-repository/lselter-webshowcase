<?php
//done: add api call calculator
//done: add other size option checkboxes

//done: add change schedule
class lswcSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'LSWC Settings',
            'manage_options',
            'lswc-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('lswc_options');
        ?>
        <div class="wrap">
            <h2>LSWC Settings</h2>

            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('lswc_page2images_group');
                //settings_fields('lswc_bxslider_option_group');
                do_settings_sections('lswc-setting-admin');
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'lswc_page2images_group', // Option group
            'lswc_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'lswc_p2i_section', // ID
            'LSWC Page2Images Options', // Title
            array($this, 'print_p2i_section_info'), // Callback
            'lswc-setting-admin' // Page
        );

//        add_settings_field(
//            'id_number', // ID
//            'ID Number', // Title
//            array($this, 'id_number_callback'), // Callback
//            'lswc-setting-admin', // Page
//            'lswc_p2i_section' // Section
//        );

        add_settings_field(
            'p2i_api_key',
            'Page2Images API Key',
            array($this, 'p2i_api_key_callback'),
            'lswc-setting-admin',
            'lswc_p2i_section'
        );
        add_settings_section(
            'lswc_ss_section', // ID
            'LSWC Screenshot Options', // Title
            array($this, 'print_ss_section_info'), // Callback
            'lswc-setting-admin' // Page
        );
        add_settings_field(
            'schedule',
            'Image update Frequency',
            array($this, 'freq_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'default_thumb_size',
            'Thumbnail size',
            array($this, 'thumb_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_d',
            'Desktop',
            array($this, 'size_d_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_i4',
            'Iphone 4',
            array($this, 'size_i4_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_i5',
            'Iphone 5',
            array($this, 'size_i5_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_a',
            'Android Phone',
            array($this, 'size_a_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_w',
            'Windows Phone',
            array($this, 'size_w_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );

        add_settings_field(
            'size_at',
            'Android Tablet',
            array($this, 'size_at_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_field(
            'size_ip',
            'Ipad',
            array($this, 'size_ip_callback'),
            'lswc-setting-admin',
            'lswc_ss_section'
        );
        add_settings_section(
            'lswc_m_section', // ID
            'LSWC Screenshot Options', // Title
            array($this, 'print_section_info'), // Callback
            'lswc-setting-admin' // Page
        );
        add_settings_field(
            'manual_rfrsh',
            'Force Screenshot Update Now (If doing this do not change any settings, save these first then update images. This may not work well with larger numbers of sites and thumbnails due to timeout limits)',
            array($this, 'manual_rfrsh_callback'),
            'lswc-setting-admin',
            'lswc_m_section'
        );

//        register_setting(
//            'lswc_bxslider_option_group', // Option group
//            'lswc_bxslider_name', // Option name
//            array( $this, 'sanitize' ) // Sanitize
//        );

//        add_settings_section(
//            'lswc_p2i_section2', // ID
//            'LSWC bxSlider Options', // Title
//            array($this, 'print_section_info'), // Callback
//            'lswc-setting-admin' // Page
//        );
//
//        add_settings_field(
//            'id_number2', // ID
//            'ID Number2', // Title
//            array($this, 'id_number2_callback'), // Callback
//            'lswc-setting-admin', // Page
//            'lswc_p2i_section2' // Section
//        );
//
//        add_settings_field(
//            'title2',
//            'Title2',
//            array($this, 'title2_callback'),
//            'lswc-setting-admin',
//            'lswc_p2i_section2'
//        );


    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */

    public function sanitize($input)
    {

        //var_dump($_POST);
        //check int
        $new_input = array();
        $new_input['sizes[]']= array();
        if (isset($input['id_number']))
            $new_input['id_number'] = absint($input['id_number']);
        //check title


        //only allow a member of array(schedules)
        if (isset($input['p2i_api_key']))

            $new_input['p2i_api_key'] = sanitize_text_field($input['p2i_api_key']);


        if (isset($input['schedule'])) {
            $new_input['schedule'] = sanitize_text_field($input['schedule']);
            update_schedule($new_input['schedule']);
        };
        if (isset($input['default_thumb_size']))
            $new_input['default_thumb_size'] = sanitize_text_field($input['default_thumb_size']);
        if (isset($input['id_number2']))
            $new_input['id_number2'] = absint($input['id_number2']);
        //check title

        if (isset($input['title2']))
            $new_input['title2'] = sanitize_text_field($input['title2']);

        if (isset($input['size_i4']))
            $new_input['size_i4'] = $input['size_i4'];
        else
            $new_input['size_i4']= 0;

        if (isset($input['size_i5']))
            $new_input['size_i5'] = 1;
        else
            $new_input['size_i5']= 0;

        if (isset($input['size_a']))
            $new_input['size_a'] = 1;
        else
            $new_input['size_a']= 0;

        if (isset($input['size_at']))
            $new_input['size_at'] =1;
        else
            $new_input['size_at']= 0;

        if (isset($input['size_w']))
            $new_input['size_w'] = 1;
        else
            $new_input['size_w']= 0;

        if (isset($input['size_d']))
            $new_input['size_d'] = 1;
        else
            $new_input['size_d']= 0;

        if (isset($input['size_ip']))
            $new_input['size_ip'] = $input['size_ip'];
        else
            $new_input['size_ip']= 0;

        if (isset($input['manual_rfrsh']))
            lselter_webshowcase_cache_thumbnails();
        return $new_input;

    }

    /**
     * Print the Section text
     */
    public function print_ss_section_info()
    {
        print '<strong>Enter your settings below:</strong><br>These settings controll when and which size of screenshots are taken.<br> Remember that the total number of API calls is approximately equal to: <br>The number of published sites x the frequency x the number of size options selected<br> Also not that sites where the tumbnail override options is selected are still screenshotted but the thumbnail is not overridden<br>';
        //api call calculator
        echo 'Currently this would work out at: ';
        $query = new WP_Query(array(
            'post_type' => 'sites',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));
        $published_sites=$query->found_posts;

        wp_reset_query();
        $sizes = 0;
        if(isset($this->options['size_i4'])){
            if($this->options['size_i4'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_i5'])){
            if($this->options['size_i5'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_a'])){
            if($this->options['size_a'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_w'])){
            if($this->options['size_w'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_ip'])){
            if($this->options['size_ip'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_at'])){
            if($this->options['size_at'] == 1){
                $sizes = $sizes +1;
            };
        };
        if(isset($this->options['size_d'])){
            if($this->options['size_d'] == 1){
                $sizes = $sizes +1;
            };
        };
        echo '<strong>'. $published_sites * $sizes.' '.$this->options['schedule'] .'</strong>';
    }/**
 * Print the Section text
 */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }
    /**
     * Print the Section text
     */
    public function print_p2i_section_info()
    {
        print '<strong>Enter your settings below:</strong> <br>';
        echo ' These settings relate to the site <a href="http://www.page2images.com">Page2Images</a>. <br> Please create an account on this site before using this page.<br>Make sure that you activate your chosen price plan after you create an account to complete the activation.';
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        //var_dump($this -> options);
        //var_dump(get_option('lswc_page2images_options'));
        printf(
            '<input type="text" id="id_number" name="lswc_options[id_number]" value="%s" />',
            isset($this->options['id_number']) ? esc_attr($this->options['id_number']) : ''
        );
        //echo('<img src="http://api.page2images.com/restfullink?p2i_url=http://lselter.co.uk&p2i_device=6&p2i_screen=1280x800&p2i_size=1280x800&p2i_imageformat=jpg&p2i_wait=0&p2i_key=217544a4d99552e3"/>');
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function p2i_api_key_callback()
    {
        printf(
            '<input type="text" id="p2i_api_key" name="lswc_options[p2i_api_key]" value="%s" />',
            isset($this->options['p2i_api_key']) ? esc_attr($this->options['p2i_api_key']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number2_callback()
    {
        printf(
            '<input type="text" id="id_number2" name="lswc_options[id_number2]" value="%s" />',
            isset($this->options['id_number2']) ? esc_attr($this->options['id_number2']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title2_callback()
    {
        printf(
            '<input type="text" id="title2" name="lswc_options[title2]" value="%s" />',
            isset($this->options['title2']) ? esc_attr($this->options['title2']) : ''
        );
        //include('lswc_cachethumbs.php');
        //lselter_webshowcase_cache_thumbnails();
    }

    /**
     * Get the settings option array and print one of its values
     * get schedules and create drop down with available schedules
     */
    public function freq_callback()
{
    //var_dump( get_option( 'lswc_page2images_options' ));
//        printf(
//            '<input type="text" id="schedule" name="lswc_bxslider_name[schedule]" value="%s" />',
//            isset( $this->options['schedule'] ) ? esc_attr( $this->options['schedule']) : ''
//        );
    //var_dump($this->options['schedule']);
    //var_dump(wp_get_schedules());
    print(
    '<select name="lswc_options[schedule]">');
    $arroptions = wp_get_schedules()+ array('never'=> array('display'=>'Never'));
    foreach ($arroptions as $y => $x) {

        echo('<option value = "' . $y . '"');
        if (isset($this->options['schedule'])) {
            if ($this->options['schedule'] == $y) {
                echo('selected');
            };
        };
        echo('>' . $x['display'] . '</option>');
    };
    //echo('<option value="never">Never</option>');
    echo('</select>');
    //require_once('APIs/p2i.php');
    //var_dump($this -> options);
}
    public function thumb_callback()
    {

        print(
        '<select name="lswc_options[default_thumb_size]">');
        $arroptions = array(0=>'Iphone 4',1=> 'Iphone 5', 2=> 'Android', 3=> 'Ipad',4=> 'Android Pad', 6=> 'Desktop');
        //0 - iPhone4, 1 - iPhone5, 2 - Android, 3 - WinPhone, 4 - iPad, 5 - Android Pad, 6 - Desktop
        foreach ($arroptions as $y => $x) {

            echo('<option value = "' . $y . '"');
            if (isset($this->options['default_thumb_size'])) {
                if ($this->options['default_thumb_size'] == $y) {
                    echo('selected');
                };
            };
            echo('>' . $x . '</option>');
        };
        //echo('<option value="never">Never</option>');
        echo('</select>');
        //require_once('APIs/p2i.php');
        //var_dump($this -> options);
    }

    public function size_d_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_d]" value="1"');
    if(isset($this->options['size_d'])) {
        checked(1 == $this->options['size_d']);
    }
        echo('/>');

    }
    public function size_i4_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_i4]" value="1"');
    if(isset($this->options['size_i4'])) {
        checked(1 == $this->options['size_i4']);
    }
        echo('/>');

    }
    public function size_i5_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_i5]" value="1"');
        if(isset($this->options['size_i5'])) {
            checked(1 == $this->options['size_i5']);
        }
        echo('/>');

    }
    public function size_a_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_a]" value="1"');
        if(isset($this->options['size_a'])) {
            checked(1 == $this->options['size_a']);
        }
        echo('/>');

    }
    public function size_at_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_at]" value="1"');
        if(isset($this->options['size_at'])) {
            checked(1 == $this->options['size_at']);
        }
        echo('/>');

    }
    public function size_w_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_w]" value="1"');
        if(isset($this->options['size_w'])) {
            checked(1 == $this->options['size_w']);
        }
        echo('/>');

    }
    public function size_ip_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[size_ip]" value="1"');
        if(isset($this->options['size_ip'])) {
            checked(1 == $this->options['size_ip']);
        }
        echo('/>');

    }
    public function manual_rfrsh_callback()
    {

        echo('
    <input type="checkbox" name="lswc_options[manual_rfrsh]" value="1"');

        echo('/>');

    }


};


if (is_admin())
    $lswc_settings_page = new lswcSettingsPage();

