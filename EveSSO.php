<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2012 HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

/**
* Hybrid_Providers_EveSSO (By Steve Anderson)
*/
class Hybrid_Providers_EveSSO extends Hybrid_Provider_Model_OAuth2
{
        // default permissions
        public $scope = "";

        /**
        * IDP wrappers initializer
        */
        function initialize()
        {
                parent::initialize();

                // Provider api end-points
                $this->api->api_base_url  = "https://login.eveonline.com/oauth/";
                $this->api->authorize_url = "https://login.eveonline.com/oauth/authorize";
                $this->api->token_url     = "https://login.eveonline.com/oauth/token";
        }


        private function request( $url, $params = array(), $type="GET", $http_headers = null )
        {
                if( $type == "GET" ){
                        $url = $url . ( strpos( $url, '?' ) ? '&' : '?' ) . http_build_query($params, '', '&');
                }

                $this->http_info = array();
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL            , $url );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
                curl_setopt($ch, CURLOPT_TIMEOUT        , $this->api->curl_time_out );
                curl_setopt($ch, CURLOPT_USERAGENT      , $this->api->curl_useragent );
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , $this->api->curl_connect_time_out );
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , $this->api->curl_ssl_verifypeer );
                curl_setopt($ch, CURLOPT_HTTPHEADER     , $this->api->curl_header );

        if (is_array($http_headers)) {
            $header = array();
            foreach($http_headers as $key => $parsed_urlvalue) {
                $header[] = "$key: $parsed_urlvalue";
            }

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
        }
                else{
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->api->curl_header );
                }

                if($this->api->curl_proxy){
                        curl_setopt( $ch, CURLOPT_PROXY        , $this->api->curl_proxy);
                }

                if( $type == "POST" ){
                        curl_setopt($ch, CURLOPT_POST, 1);
                        if($params) curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
                }

                $response = curl_exec($ch);

                $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $this->http_info = array_merge($this->http_info, curl_getinfo($ch));

                curl_close ($ch);

                return $response;
        }
        private function parseRequestResult( $result )
        {
                if( json_decode( $result ) ) return json_decode( $result );

                parse_str( $result, $output );

                $result = new StdClass();

                foreach( $output as $k => $v )
                        $result->$k = $v;

                return $result;
        }

        /**
        * load the user profile from the IDP api client
        */
        function getUserProfile(){
                $http_headers = array();
                $http_headers['Authorization'] = 'Bearer ' . $this->api->access_token;

                $response = $this->request( $this->api->api_base_url."/verify", array(), 'GET', $http_headers );

                $response = $this->parseRequestResult( $response );

                if ( ! $response || ! isset( $response->CharacterName ) ){
                        throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
                }

                $this->user->profile->identifier  = @ $response->CharacterID;
                $this->user->profile->displayName = @ $response->CharacterName;
                $this->user->profile->profileURL  = "https://forums.eveonline.com/profile/".urlencode($response->CharacterName);
                $this->user->profile->photoURL  = "https://image.eveonline.com/character/".$response->CharacterID."_64.jpg";

                if( $this->user->profile->identifier ){
                        return $this->user->profile;
                }
        }
}
