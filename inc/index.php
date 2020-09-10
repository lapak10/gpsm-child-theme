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
    private static $greenrop_api_url = 'https://staging.greenrope.com/api-xml';
    private static $debug = true;

    private static $email = 'aaron@commandmedia.net';
    private static $password = 'aiG#ie3th';

    static function get_greenrope_new_event_xml($gtw_event_data){
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

    private static function get_auth_token(){
        $response = wp_remote_post(self::$greenrop_api_url,[
                'body' => [
                  'email'=> self :: $email ,
                  'password'=> self :: $password ,
                  'xml'=>'<GetAuthTokenRequest response="json"></GetAuthTokenRequest>'
                ]
        ]);

      if( is_wp_error( $greenrope_response ) ){
            self::log_error( $greenrope_response->get_error_message() );
            die();
      }
      $token_response = json_decode( $response['body'],true);
      return $token_response['getauthtokenresponse']['token'];
    }

    private static function add_event_to_greenrope($gtw_event_data){

          $greenrope_response = wp_remote_post(self::$greenrop_api_url,[
            'body' => [
              'email'=> self :: $email ,
              'auth_token'=> self :: get_auth_token(),
              'xml'=> self :: get_greenrope_new_event_xml( $gtw_event_data )
            ]
          ]);

          if( is_wp_error( $greenrope_response ) ){
            self::log_error( $greenrope_response->get_error_message() );
          }

          $gr_event_data = json_decode( $greenrope_response['body'] ,true);

          return $gr_event_data['addeventsresponse']['events']['event'];
    }

    private static function log_error($message){
      self :: log_post( 'ERROR: ' . $message , 'error' );
    }

    private static function log_post($message,$type = 'success'){
      wp_insert_post(array(
          'post_title'=> $message ,
                'post_status'=>'publish',
                'post_type'=>'ontracks_webhook',
                'post_author'=>1,
          'post_content'=> $message,
          'meta_input'=> array( 'log_type'=> $type , 'message'=> $message ,'created_by'=>'ontracks_dump' )
      ));
    }

    private static function get_greenrope_event_id_by_webinarKey($webinarKey){
      $greenrope_id = 0;

      $event_posts = get_posts(array(
        'post_status'=>'publish',
        'post_type'=>'ontracks_webhook',
        'post_author'=>1,
        'meta_query'=> array(
          array(
            'key'=> 'webinarKey',
            'value'=> $webinarKey
          ),
          array(
            'key'=>'eventName',
            'value'=>'webinar.created'
          )
        )
      ));

      if( $event_posts ){
        $greenrope_id = $event_posts[0]->ID;
      }
      return $greenrope_id;
    }

    private static function log_success($message){
      self :: log_post( 'SUCCESS: ' . $message , 'success' );
    }

    public static function registrant_add_to_greenrope($webinarKey,$email){

      $xml = sprintf('<Attend><Email>%s</Email></Attend>',$email);

      self :: log_success(sprintf('Adding Registrant %s to event post with webinarKey %s',$email,$webinarKey));

      self :: update_event( self::get_greenrope_event_id_by_webinarKey( $webinarKey ), $xml );

    }
    public static function registrant_join_to_greenrope($webinarKey,$email){
       $xml = sprintf('<CheckIn><Email>%s</Email></CheckIn>',$email);

      self :: log_success(sprintf('Adding JOINED REGISTRANT %s to event post with webinarKey %s',$email,$webinarKey));

      self :: update_event( self::get_greenrope_event_id_by_webinarKey( $webinarKey ), $xml );
    }

    private static function update_event($event_post_id, $xml){
       

         $greenrope_event_id = get_post_meta( $event_post_id , 'greenrope_eventid' , true );
        // die( $greenrope_event_id . $xml . $event_post_id );

          $body = array(
                'email'=> self :: $email ,
                'auth_token'=> self :: get_auth_token(),
                'xml'=> sprintf('<UpdateEventAttendeesRequest EventID="%s" account_id="%s" group_id="%s" response="json">%s</UpdateEventAttendeesRequest>',$greenrope_event_id,self:: $account_id,self::$group_id,$xml)
          );
        // die( var_dump($body) );
          $greenrope_response = wp_remote_post(self::$greenrop_api_url,[
            'body' => $body
          ]);

          if( is_wp_error( $greenrope_response ) ){
            self::log_error( $greenrope_response->get_error_message() );
          }
          // die( $greenrope_response['body'] );
    }

    public static function create_new_event_post($request){

        $event_post_id =  wp_insert_post(array(
          'post_title'=> 'NEW WEBINAR EVENT ADDED' . $request->get_method() ,
                'post_status'=>'publish',
                'post_type'=>'ontracks_webhook',
                'post_author'=>1,
          'post_content'=> json_encode( $request->get_params() ),
          'meta_input'=> $request->get_params()
          ));
        self :: log_success('New Event deteced on GoToWebinar');

        $gr_event_data = self :: add_event_to_greenrope($request->get_params());
        
        add_post_meta( $event_post_id, 'greenrope_accountnumber', $gr_event_data['accountnumber'] );
        add_post_meta( $event_post_id, 'greenrope_eventid', $gr_event_data['eventid']);
        
        self :: log_success('Event synced to GreenRope');
        
        return $event_post_id;
        
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

    if( 'GET' === $request->get_method() ){
        return array( 'result'=>'success' ,'method' => $request->get_method() );
    }


    $data = $request->get_params();


    if( ! isset( $data['eventName'] ) ){
      return array( 'result'=>'failed' ,'message' => 'eventName key missing' );
    }

    switch ($data['eventName']) {
      case 'webinar.created':
        CMD_OnTracks_WebHooks :: create_new_event_post ( $request );
        return array( 'result'=>'success' ,'method' => $request->get_method() );
        break;
      case 'registrant.added':
        CMD_OnTracks_WebHooks :: registrant_add_to_greenrope ( $data['webinarKey'] , $data['email'] );
        return array( 'result'=>'success' ,'method' => $request->get_method() );
        break;
      case 'registrant.joined':
        CMD_OnTracks_WebHooks :: registrant_join_to_greenrope ( $data['webinarKey'] , $data['email'] );
        return array( 'result'=>'success' ,'method' => $request->get_method() );
        break;
      
      default:
        return array( 'result'=>'fail' ,'method' => 'unable to map eventName with hook' );
        break;
    }

    // if( $data['eventName'] === 'webinar.created' ){
      
    // }

    // if( $data['eventName'] === 'registrant.added' ){
    //    CMD_OnTracks_WebHooks :: registrant_add_to_greenrope ( $data['webinarKey'] , $data['email'] );
    // }

    // if( $data['eventName'] === 'registrant.joined' ){
    //   CMD_OnTracks_WebHooks :: registrant_join_to_greenrope ( $data['webinarKey'] , $data['email'] );
    // }

		// wp_insert_post(array(
		// 	'post_title'=> 'NEW WEBINAR HOOK CALLED via ' . $request->get_method() ,
    //         'post_status'=>'publish','post_type'=>'ontracks_webhook',
    //         'post_author'=>1,
		// 	'post_content'=> json_encode( $request->get_params() ),
		// 	'meta_input'=> $request->get_params()
		// ));
		
		// if( 'GET' === $request->get_method() ){
		//     $myfile = fopen( plugin_dir_path( __FILE__ ) . "logs.txt", "a") or die("Unable to open file!");
    //         $txt =  'IP ADDRESS=>'.$_SERVER['REMOTE_ADDR'] .'    USER_AGENT=> '. $_SERVER['HTTP_USER_AGENT'] .' ,   TIME => '. current_time('mysql',true) ;
    //         fwrite($myfile, "\n". $txt);
    //         fclose($myfile);
		//     //die('200 OK');
		// }

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
        
        $json_string = '{"eventName":"webinar.created","eventVersion":"1.0.0","product":"g2w","eventKey":"628bd252-b208-44a8-8de8-c989993c6cbc","webinarKey":2456981187019095056,"webinarTitle":"Test webinar 4","experienceType":"BROADCAST","recurrenceType":"single_session","webinarCreationDate":"2020-08-25T17:11:24.190Z","times":[{"startTime":"2020-08-25T20:00:00Z","endTime":"2020-08-25T21:00:00Z"}],"status":"NEW","timeZone":"America/Los_Angeles","organizerKey":5064544480539859206,"accountKey":5833285627285494028,"timestamp":"2020-08-15T17:11:24.994Z"}';

        $gtw_event_data = json_decode( $json_string, true );

        // return CMD_OnTracks_WebHooks :: get_greenrope_new_event_xml( $gtw_event_data );

		// wp_insert_post(array(
		// 	'post_title'=> 'ZAPPIER DUMP',
    //         'post_status'=>'draft','post_type'=>'ontracks_webhook',
    //         'post_author'=>1,
		// 	'post_content'=> json_encode( $request->get_params() )
		// ));
      $response = wp_remote_post('https://staging.greenrope.com/api-xml',[
      'body' => [
        'email'=>'aaron@commandmedia.net',
        'password'=>'aiG#ie3th',
        'xml'=>'<GetAuthTokenRequest response="json"></GetAuthTokenRequest>'
      ]
    ]);

      if ( is_wp_error( $response ) ){
        return $response;
      }
      $token_response = json_decode( $response['body'],true);
      // return $token_response['getauthtokenresponse']['token'];
      // return json_decode( $response['body'],true);
      // return $response['body'];

		$greenrope_response = wp_remote_post('https://staging.greenrope.com/api-xml',[
      'body' => [
        'email'=>'aaron@commandmedia.net',
        'auth_token'=> $token_response['getauthtokenresponse']['token'],
        'xml'=> CMD_OnTracks_WebHooks :: get_greenrope_new_event_xml( $gtw_event_data )
      ]
    ]);
    
    $gr_response_array = json_decode( $greenrope_response['body'] ,true);
    
    return $gr_response_array['addeventsresponse']['events']['event'];



    if( is_wp_error( $greenrope_response ) ){
      return $greenrope_response;
    }

    
    // return $greenrope_response;
    return $greenrope_response['addeventsresponse']['events']['event'];

	}
  ));

} );