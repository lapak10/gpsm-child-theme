<?php defined('ABSPATH') or exit;
/*
Plugin Name: OnTracks Zappier WebEndPoint
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Command Media
Version: 0.1
Author URI: http://ma.tt/
*/

class CMD_OnTracks_WebHooks{
    private static $group_id = '1';
    private static $account_id = '44291';
    
    static function add_new_event_in_greenrope($gtw_event_data){
        $xml = '<AddEventsRequest response="json">';
        $xml .= sprintf('<Event account_id="%s" group_id="%s">',self::$account_id,self::$group_id);
        $xml .= '<TeamID>1</TeamID>';
        $xml .= sprintf('<EventTitle>%s</EventTitle>',$gtw_event_data['webinarTitle']);
        $xml .= '<EventType>Seminar</EventType><PublicEvent>Y</PublicEvent><Reminders>Y</Reminders><Attendees></Attendees><EventFee>Free!</EventFee><ChargeNoShows>N</ChargeNoShows><LocationID>23</LocationID>';
        $xml .= sprintf('<Date>%s</Date>',self::parse_datetime_string($gtw_event_data['times'][0]['startTime']));
        $xml .= sprintf('<EndDate>%s</EndDate>',self::parse_datetime_string($gtw_event_data['times'][0]['endTime']));
        $xml .= sprintf('<StartTime>%s</StartTime>',self::parse_datetime_string($gtw_event_data['times'][0]['startTime'],'g:ia'));
        $xml .= sprintf('<EndTime>%s</EndTime>',self::parse_datetime_string($gtw_event_data['times'][0]['endTime'],'g:ia'));
        $xml .= '</Event></AddEventsRequest>';
        return $xml;
    }

    private static function parse_datetime_string($datetime,$format = 'Y-m-d'){
        // we need to remove the Z from the end
        return date($format, strtotime( substr( $datetime,0,-1 )  ) );
    }
}





add_action( 'rest_api_init', function () {

  register_rest_route( 'ontracks/v1', '/gtw-link-gr', array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request){
    
    $meta_input =  $request->get_params();
    unset( $meta_input['rest_route'] );
    
		wp_insert_post(array(
			'post_title'=> 'GTW-links-GR',
            'post_status'=>'publish',
            'post_type'=>'ontracks_webhook',
            'post_author'=>1,
      'post_content'=> json_encode( $request->get_params() ),
      'meta_input' => $meta_input
		));

		return array('query_params'=>$request->get_query_params(),'all_headers'=> getallheaders());
	}
  ) );

  register_rest_route( 'ontracks/v1', '/zappier-webook', array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request){

		wp_insert_post(array(
			'post_title'=> 'ZAPPIER DUMP',
            'post_status'=>'publish','post_type'=>'ontracks_webhook',
            'post_author'=>1,
			'post_content'=> json_encode( $request->get_params() )
		));

		return array('query_params'=>$request->get_query_params(),'all_headers'=> getallheaders());
	}
  ));

  register_rest_route( 'ontracks/v1', '/new_webinar', array(
    'methods' => array('GET','POST'),
    'callback' => function(WP_REST_Request $request){

		wp_insert_post(array(
			'post_title'=> 'NEW WEBINAR HOOK CALLED via ' . $request->get_method() ,
            'post_status'=>'publish','post_type'=>'ontracks_webhook',
            'post_author'=>1,
			'post_content'=> json_encode( $request->get_params() ),
			'meta_input'=> $request->get_params()
		));
		
		if( 'GET' === $request->get_method() ){
		    $myfile = fopen( plugin_dir_path( __FILE__ ) . "logs.txt", "a") or die("Unable to open file!");
            $txt =  'IP ADDRESS=>'.$_SERVER['REMOTE_ADDR'] .'    USER_AGENT=> '. $_SERVER['HTTP_USER_AGENT'] .' ,   TIME => '. current_time('mysql',true) ;
            fwrite($myfile, "\n". $txt);
            fclose($myfile);
		    //die('200 OK');
		}

		return array( 'result'=>'success' ,'method' => $request->get_method() );
	}
  ));

  register_rest_route( 'ontracks/v1', '/greenrope-get-auth-token', array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request){

		// wp_insert_post(array(
		// 	'post_title'=> 'ZAPPIER DUMP',
    //         'post_status'=>'draft','post_type'=>'ontracks_webhook',
    //         'post_author'=>1,
		// 	'post_content'=> json_encode( $request->get_params() )
		// ));

		return wp_remote_post('https://staging.greenrope.com/api-xml',[
      'body' => [
        'email'=>'aaron@commandmedia.net',
        'password'=>'aiG#ie3th',
        'xml'=>'<GetAuthTokenRequest></GetAuthTokenRequest>'
      ]
    ]);
	}
  ));

  register_rest_route( 'ontracks/v1', '/test-add-event-xml', array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request){
        
        $json_string = '{"eventName":"webinar.created","eventVersion":"1.0.0","product":"g2w","eventKey":"628bd252-b208-44a8-8de8-c989993c6cbc","webinarKey":2456981187019095056,"webinarTitle":"Test webinar 4","experienceType":"BROADCAST","recurrenceType":"single_session","webinarCreationDate":"2020-08-15T17:11:24.190Z","times":[{"startTime":"2020-08-15T20:00:00Z","endTime":"2020-08-15T21:00:00Z"}],"status":"NEW","timeZone":"America/Los_Angeles","organizerKey":5064544480539859206,"accountKey":5833285627285494028,"timestamp":"2020-08-15T17:11:24.994Z"}';

        $gtw_event_data = json_decode( $json_string, true );

        return CMD_OnTracks_WebHooks :: add_new_event_in_greenrope( $gtw_event_data );

		// wp_insert_post(array(
		// 	'post_title'=> 'ZAPPIER DUMP',
    //         'post_status'=>'draft','post_type'=>'ontracks_webhook',
    //         'post_author'=>1,
		// 	'post_content'=> json_encode( $request->get_params() )
		// ));

	// 	return wp_remote_post('https://staging.greenrope.com/api-xml',[
    //   'body' => [
    //     'email'=>'aaron@commandmedia.net',
    //     'password'=>'aiG#ie3th',
    //     'xml'=>'<GetAuthTokenRequest></GetAuthTokenRequest>'
    //   ]
    // ]);
	}
  ));

} );

/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class WebhookGTWGRSync {
	private $webhook_gtw_gr_sync_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'webhook_gtw_gr_sync_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'webhook_gtw_gr_sync_page_init' ) );
	}

	public function webhook_gtw_gr_sync_add_plugin_page() {
		add_options_page(
			'Webhook GTW - GR Sync', // page_title
			'Webhook GTW - GR Sync', // menu_title
			'manage_options', // capability
			'webhook-gtw-gr-sync', // menu_slug
			array( $this, 'webhook_gtw_gr_sync_create_admin_page' ) // function
		);
	}

	public function webhook_gtw_gr_sync_create_admin_page() {
		$this->webhook_gtw_gr_sync_options = get_option( 'webhook_gtw_gr_sync_option_name' ); ?>

		<div class="wrap">
			<h2>Webhook GTW - GR Sync</h2>
			<p>Some default description</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'webhook_gtw_gr_sync_option_group' );
					do_settings_sections( 'webhook-gtw-gr-sync-admin' );
					submit_button();
				?>
			</form>
		</div>
  <?php }
  
  // Below this we have options page settings
	public function webhook_gtw_gr_sync_page_init() {
		register_setting(
			'webhook_gtw_gr_sync_option_group', // option_group
			'webhook_gtw_gr_sync_option_name', // option_name
			array( $this, 'webhook_gtw_gr_sync_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'webhook_gtw_gr_sync_setting_section', // id
			'Settings', // title
			array( $this, 'webhook_gtw_gr_sync_section_info' ), // callback
			'webhook-gtw-gr-sync-admin' // page
		);

		add_settings_field(
			'greenrope_username_0', // id
			'GreenRope Username', // title
			array( $this, 'greenrope_username_0_callback' ), // callback
			'webhook-gtw-gr-sync-admin', // page
			'webhook_gtw_gr_sync_setting_section' // section
		);

		add_settings_field(
			'greenrope_password_1', // id
			'GreenRope Password', // title
			array( $this, 'greenrope_password_1_callback' ), // callback
			'webhook-gtw-gr-sync-admin', // page
			'webhook_gtw_gr_sync_setting_section' // section
		);
	}

	public function webhook_gtw_gr_sync_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['greenrope_username_0'] ) ) {
			$sanitary_values['greenrope_username_0'] = sanitize_text_field( $input['greenrope_username_0'] );
		}

		if ( isset( $input['greenrope_password_1'] ) ) {
			$sanitary_values['greenrope_password_1'] = sanitize_text_field( $input['greenrope_password_1'] );
		}

		return $sanitary_values;
	}

	public function webhook_gtw_gr_sync_section_info() {
		
	}

	public function greenrope_username_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="webhook_gtw_gr_sync_option_name[greenrope_username_0]" id="greenrope_username_0" value="%s">',
			isset( $this->webhook_gtw_gr_sync_options['greenrope_username_0'] ) ? esc_attr( $this->webhook_gtw_gr_sync_options['greenrope_username_0']) : ''
		);
	}

	public function greenrope_password_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="webhook_gtw_gr_sync_option_name[greenrope_password_1]" id="greenrope_password_1" value="%s">',
			isset( $this->webhook_gtw_gr_sync_options['greenrope_password_1'] ) ? esc_attr( $this->webhook_gtw_gr_sync_options['greenrope_password_1']) : ''
		);
	}

}
if ( is_admin() )
	$webhook_gtw_gr_sync = new WebhookGTWGRSync();

/* 
 * Retrieve this value with:
 * $webhook_gtw_gr_sync_options = get_option( 'webhook_gtw_gr_sync_option_name' ); // Array of All Options
 * $greenrope_username_0 = $webhook_gtw_gr_sync_options['greenrope_username_0']; // GreenRope Username
 * $greenrope_password_1 = $webhook_gtw_gr_sync_options['greenrope_password_1']; // GreenRope Password
 */
