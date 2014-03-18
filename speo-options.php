<?php
// if( isset( $_POST ) ) {
// 	echo '<code><pre>';
// 		var_dump( $_POST );
// 	echo '</pre></code>';
// }
class SpeoOptions
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
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Exiftool Options', 
            'manage_options', 
            'speo_exif_options', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'speo_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Exiftools Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'speo_options_group' );   
                do_settings_sections( 'speo_exif_options' );
                echo '</ol>';
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
            'speo_options_group', // Option group
            'speo_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'speo_options_all', // ID
            'Options', // Title
            array( $this, 'print_section_info' ), // Callback
            'speo_exif_options' // Page
        );

        //get the blog language
        $this_lang = get_locale();

        //get the values from the list.xml
        $xmldoc = new DOMDocument();
        $xmldoc->load( plugin_dir_path( __FILE__ ) . 'list.xml' );

        $xpathvar = new Domxpath($xmldoc);

        $queryResult = $xpathvar->query('//tag/@name');

        $possible_values = array();

        foreach( $queryResult as $result ){
        	if( substr( $result->textContent, 0, 9 ) === 'MakerNote' )
        		continue;

            $possible_values[ $result->textContent ] = 0;
            ksort( $possible_values );
        }

		foreach( $possible_values as $value => $bool ) {
			// $xpath = new Domxpath($xmldoc);
			// $descs = $xpath->query('//tag[@name="' . $value . '"]/desc[@lang="en"]');
			// $titles = $xpath->query('//tag[@name="' . $value . '"]/desc[@lang="' . substr( $this_lang, 0, 2 ) . '"]');

			// foreach( $descs as $desc ) {
				// $i=1;
				// $opt_title = '<li>' . $value;
				// foreach( $titles as $title ) {
				// 	if( $i > 1 )
				// 		continue;

				// 	$opt_title .= '<br />(' . $title->textContent . ')';
				// 	$i++;
				// }
				// $opt_title .= '</li>';

				//add the actual setting
				add_settings_field(
		            'speo_exif_' . $value, // ID
		            $value, // Title 
		            array( $this, 'speo_callback' ), // Callback
		            'speo_exif_options', // Page
		            'speo_options_all', // Section
		            $value //args
		        );
			// }
		}
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $inputs )
    {
        return $inputs;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Check the values you want to retreive from images:<ol>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function speo_callback( $value ) {
    	// echo '<code><pre>';
    	// 	var_dump($this->options);
    	// echo '</pre></code>';
        printf(
            '<input type="checkbox" id="speo_exif_' . $value . '" name="speo_options[' . $value . ']" %s />',
            checked( isset( $this->options[$value] ), true, false )
            // ( isset( $this->options[$value] ) ? $this->options[$value] : 'none' )

        );
    }
}

if( is_admin() )
    $my_settings_page = new SpeoOptions();
