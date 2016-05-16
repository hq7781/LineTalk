<?php
   /************************************************
     * LineTalk.php
     * ClassName: LineTalk
     *
     * ClassDetail:
     *       Line Talk API
     *
     ***********************************************/



class LineTalk
{


    const APIBASE_URL = 'https://api.line.me/v1/';
    const TO_CHANNEL = '1341301715';
    const EVENT_TYPE = '137299299800026303';

    public $api_endpoint;
    public $channel_id;
    public $channel_secret;
    public $channel_mid;
    public $access_token;
    
    public $request_header;
    public $request_to;
    
    public $user_profile;

   /****************************************************
    * construct
    * @access public
    * @param  String $access_token
    * @param  String $to_mid
    * @return none
    ****************************************************/
    public function __construct(
                                $access_token = null, 
                                $to_mid = null
                               )
    {
        $this->access_token   = $access_token;
        $this->request_header = array(
            'Content-Type: application/json; charset=UTF-8',
            'X-Line-ChannelToken: ' . $this->access_token
        );

        $this->request_to = $to_mid;
        
        // init user profile
        $this->user_profile = array (
            'displayName'  => '',
            'mid'          => '',
            'pictureUrl'   => '',
            'mailaddr'     => '',
            'statusMessage'=> ''
        );

       // $content = $this->getContent();
    }



   /****************************************************
    * 
    * get Content
    * 
    *
    * @access public
    * @return Object $content
    ****************************************************/
    public function getContent()
    {
        $contents = file_get_contents('php://input');
        $json = json_decode($contents);
        $content = $json->result{0}->content;

        return $content;
    }



   /****************************************************
    *
    * get Profile
    * 
    *
    * @access public
    * @param  none
    * @return boolean success：true , failed：false
    ****************************************************/
    public function getProfile()
    {
        $this->api_endpoint = self::APIBASE_URL . 'profile' ;
        $this->request_header = array('Authorization: Bearer '. $this->access_token );

        $opts = array(
            'http'=>array(
                'method' => 'GET',
                'header' => $this->request_header
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents($this->api_endpoint, false, $context);

        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status_code = $matches[1];
        switch ($status_code) {
            case '200':
             // echo 'http_status_ok:200';
             // echo '<br>';
                break;
            case '401':
            case '402':
            case '403':
            case '404':
             // echo 'http_status_error!:' . $status_code;
             // echo '<br>';
                break;
            default:
                break;
        }
        $result = json_decode($response,true);

        if (isset( $result['displayName'] ))
        {
            $this->user_profile = $result;
         // var_dump( $this->user_profile );
            return true;
        }
        else {
            $this->user_profile = array_fill('displayName', 4, '');
         // var_dump( $this->user_profile );
            return false;
        }
    }


   /****************************************************
    * add friend
    *
    * 
    *
    * @access public
    * @param  none
    * @return boolean success：true , failed：false
    ****************************************************/
    public function addFriend()
    {

        $this->api_endpoint = self::APIBASE_URL . 'officialaccount/contacts' ;
        $this->request_header = array('Authorization: Bearer '. $this->access_token );

        $opts = array(
            'http'=>array(
                'method'  => 'POST',
                'header'  => implode('\r\n', $this->request_header),
                'content' => ''
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents($this->api_endpoint, false, $context);

        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status_code = $matches[1];
        switch ($status_code) {
            case '200':
                //echo 'http_status_ok:200';
                //echo '<br>';
                break;
            case '401':
            case '402':
            case '403':
            case '404':
                //echo 'http_status_error!:' . $status_code;
                //echo '<br>';
                break;
            default:
                break;
        }

        $result = json_decode($response,true);
        if (isset($result['result'] )) {
            return true;
        }
        elseif (isset( $result['statusCode'] )) {
            echo ' statusCode:' . $result['statusCode'];
            echo '<br>';
            echo ' statusMessage:' . $result['statusMessage'];
            echo '<br>';

            return false;
        }
        else 
            return false;
    }




   /****************************************************
    * 
    * send link message
    * 
    *
    * @access public
    * @param  Array  $aray_linkmsg 
    *                       templateId    :  
    *                       previewUrl    :  preview
    *                       date          :  send date
    *                       name          :  name
    *                       type          :  type
    *                       price         :  price
    *                       subtext_param :  custom param
    * @return boolean success：true , fail：false
    ****************************************************/
    public function sendLinkMsg( $aray_linkmsg = null )
    {
        if ($aray_linkmsg) {
        } else {
            return false;
        }

        $content = array(
            'templateId'  => $aray_linkmsg['templateId'],
            'previewUrl'  => $aray_linkmsg[‘preview’],
            'textParams'  => array(
                'id'          => $aray_linkmsg['id'],
                'name'        => $aray_linkmsg['name'],
                'price'       => $aray_linkmsg['price'],
                ),
            'subTextParams' => array(
                'subtext_param'  => $aray_linkmsg{'subtext_param'},
            ),
            'altTextParams' => array(
                'alttext_param' => ‘none’, 
            ),
            'linkTextParams' => array(
                'lt_p'  => ‘none’, 
            ),
            'aLinkUriParams' => array(
                'alu_p' => $aray_linkmsg[‘weblink'],
            ),
            'iLinkUriParams' => array(
                'ilu_p' => $aray_linkmsg[‘weblink'],
            ),
            'linkUriParams' => array(
                'lu_p'  => $aray_linkmsg[‘weblink'],
            )
        );

        $post_data = array(
            'to'        => array($this->request_to),
            'toChannel' => self::TO_CHANNEL,
            'eventType' => self::EVENT_TYPE,
            'content'   => $content,
        );

        $this->api_endpoint = self::APIBASE_URL . 'events' ;
        $this->request_header = array(
                                   "Content-Type: application/json; charset=UTF-8",
                                   "X-Line-ChannelToken: " . $this->access_token
                                );

        $result = $this->request($post_data);
        if ( isset($result['failed']) && empty($result['failed'] )) {
            var_dump( $result );
            return false;
        }
        elseif ( isset($result['error'] )) {
            var_dump( $result );
            return false;
        }
        elseif ( isset( $result['statusCode'] )) {
            echo ' statusCode:' . $result['statusCode'];
            echo '<br>';
            echo ' statusMessage:' . $result['statusMessage'];
            echo '<br>';
       
            return false;
        }
        else {
            var_dump( $result );
            return false;
        }
    }



   /****************************************************
    * CURL request
    *
    * 
    *
    * @access public
    * @param  Array  $post_dataß
    * @return Object $result
    ****************************************************/
    public function request($post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->request_header);
      //curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1');
        if (curl_errno($ch)) {
           throw new \Exception(curl_error($ch));
        }
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }


   /****************************************************
    * open Line TalkRoom
    *
    * 
    *
    * @access public
    * @param  String  $lineat_id Line@ id
    * @return none
    ****************************************************/
    public function openTalkroom($lineat_id)
    {
        $lineat_id = trim( $lineat_id, " \t\n\r\0\x0B/" );

        if ( mb_substr( $lineat_id,0,1) !== '@' )
        {
            $lineat_id = '@' . $lineat_id;
            echo $lineat_id;
        }

        // todo open the Line Talkroom
        $url = 'https://line.me/R/oaMessage/' . $lineat_id. '/';
        header("Location: {$url}", true,301);
    }



}
