<?php
/*
	To generate A to Z listing  
*/
class AlphabetPlugin {
	//to hold search result from database
	var $result;
	//to hold generated html code
	var $html;
	//to hold link parameter text like /?cat=
	var $link_text;
	//to hold header 
	var $header_text;
	//background color
	var $bg_color; 
	//text color
	var $text_color; 
	//alphabet background color
	var $alpha_bg_color;
	//Constructor
	function AlphabetPlugin() 
	{ 
		$this->html = "";
	}
	
	//register settings
	public function aplhabet_listing_register_settings() {
		register_setting( 'alphabet-listing-settings-group', 'alphabet-listing-settings', array( &$this, 'validate_settings') );
	}
	
	//validate user input
	public function validate_settings( $settings ) {
		$settings['type'] = (preg_match('/^(post|page|category)$/i', $settings['type']) ? strtolower($settings['type']) : "post");
		$settings['bg_color'] = (preg_match('/^#[a-f0-9]{6}$/i', $settings['bg_color']) ? $settings['bg_color'] : "#f0f0f0");
		$settings['text_color'] = (preg_match('/^#[a-f0-9]{6}$/i', $settings['text_color']) ? $settings['text_color'] : "#424242");
		$settings['alpha_bg_color'] = (preg_match('/^#[a-f0-9]{6}$/i', $settings['alpha_bg_color']) ? $settings['alpha_bg_color'] : "#000000");
		return $settings;
	}	
	
	// activating the default values
	public function aplhabet_listing_activate() {
		$new_options = array(
			'title' => 'A to Z listing',
			'type' => 'post', 
			'bg_color' => '#f0f0f0',
			'text_color' => '#424242',
			'alpha_bg_color' => '#000000'
		);
		add_option('alphabet-listing-settings',$new_options);
	}

	public function alphabet_listing_create_menu() {

		add_menu_page( 
			__('Alphabet Listing'),
			__('Alphabet Listing Settings'),
			0,
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			'',
			plugins_url('/images/icon.png', __FILE__)
		);
		
		
		add_submenu_page( 
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			__("Alphabet Listing Settings"),
			__("Settings"),
			0,
			AL_DIRECTORY.'/alphabet_listing_settings.php'
		);
		
		add_submenu_page( 
			AL_DIRECTORY.'/alphabet_listing_settings.php',
			__("Alphabet Listing Help"),
			__("Help"),
			9,
			AL_DIRECTORY.'/help.php'
		);
	}	


	// deactivating
	public function aplhabet_listing_deactivate() {
		// needed for proper deletion of every option
		delete_option('alphabet-listing-settings');

	}
	
	//To inject the css that is need for rendering alphabets
	public function inject_css()
	{
		wp_register_style( 'prefix-style', AL_URL . "css/alphabet_listing.css" );
    	wp_enqueue_style( 'prefix-style' );
	}

	//To read post table and return all posts
	public function get_all_titles($type) 
	{
		global $wpdb;
		//reset
		$sql = "";
		$this->html = "";
		switch ($type) 
		{
	        case 'post':
				$sql = "select id, post_title from $wpdb->posts where post_status = 'publish' AND post_type = 'post' ORDER BY post_title";
	            break;
	        case 'page':
	            $sql = "select id, post_title from $wpdb->posts where post_status = 'publish' AND post_type = 'page' ORDER BY post_title";
	            break;
	        case 'category':
	            $sql = "SELECT term_id as id, name as post_title FROM $wpdb->terms ORDER BY name";
	            break;
	    }
		
		$this->result = $wpdb->get_results($sql, ARRAY_A );
	}
	/* 
		To generate A to Z html with links 
	*/
	public function generateAtoZHtml()
	{
		
		$startCapital = 65;
		$startSmall = 97;

		$this->html .= "<div id='wp-alphabet-listing'>";
		$this->html .= "<section style=\"background-color:". $this->bg_color .";\">";
		$this->html .= "<h2>". $this->header_text ."</h2>";
		$this->html .= "<ol>\n";
		
		for($i = 0;$i<26;$i++)
		{
			$hasItem = FALSE;
			$tempHtml = "";
			$this->html .= "<li><a style=\"color:". $this->text_color.";background-color:". $this->bg_color .";\" href='#'>" . chr($startCapital + $i) . "</a>\n";
			foreach($this->result as $row)
			{
				if (( $row['post_title'][0] == chr($startCapital + $i)) || ( $row['post_title'][0] == chr($startSmall + $i)))
				{
					$tempHtml .= "<li><a href='?". $this->link_text ."=". $row['id'] ."'>" .  substr($row['post_title'],0,20) . "</a></li>\n";
					$hasItem = TRUE;
				}
			}
			if ($hasItem)
			{
				$this->html .= "<div>\n" . "<ul>\n" . $tempHtml . "</ul>\n" . "</div>\n";
			}
			
			$this->html .= "</li>\n";
		}
		$this->html .= "</ol>\n";
		$this->html .= "</section>";
		$this->html .= "</div>";	
	}
	
	//to use shortcode and return html accordingly
	//also sets default values, for default options priority is short code then user settings
	public function atoz_shortcode($atts) 
	{
		$wp_al_options = get_option('alphabet-listing-settings');
		extract(shortcode_atts(array(
						'type' => '',
						'title' => '',
						'bg_color' => '',
						'text_color' => '' 
					), $atts));
		//set type
		if ($type == '')
			$type = $wp_al_options['type'];
		//set title
		if ($title == '')
			$this->header_text = $wp_al_options['title'];
		else
			$this->header_text = $title;
		
		//set background color
		if ($bg_color == '')
			$this->bg_color = $wp_al_options['bg_color'];
		else
			$this->bg_color = $bg_color;
		
		//set text color
		if ($text_color == '')
			$this->text_color = $wp_al_options['text_color'];
		else
			$this->text_color = $text_color;
			
		//set text color
		if ($alpha_bg_color == '')
			$this->alpha_bg_color = $wp_al_options['alpha_bg_color'];
		else
			$this->text_color = $alpha_bg_color;	
			
							     
	    // check what type user entered
	    switch (strtolower($type)) 
		{
	        case 'post':
				$this->get_all_titles('post');
				$this->link_text = "p";
	            break;
	        case 'page':
	            $this->get_all_titles('page');
	            $this->link_text = "page_id";
				break;
	        case 'category':
	            $this->get_all_titles('category');
				$this->link_text = "cat";
	            break;
			default:
				$this->get_all_titles('post');
				$this->link_text = "p";
	    }
		$this->generateAtoZHtml();
		return $this->html;
	
	}		
	

}

?>