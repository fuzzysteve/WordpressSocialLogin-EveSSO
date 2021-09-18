<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2017 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Data;
use Hybridauth\User;

/**
 * Eve Online OAuth2 provider adapter.
 */
class Eveonline extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $scope = '';

    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = '';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://login.eveonline.com/v2/oauth/authorize/';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://login.eveonline.com/v2/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://docs.esi.evetech.net/docs/sso/web_based_sso_flow.html';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        if ($this->isRefreshTokenAvailable()) {
            $this->tokenRefreshParameters += [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getUserProfile()
    {

        $accesstoken=strval($this->getStoredData('access_token'));
        $jwtexplode=json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.',$accesstoken )[1]))));
        $charactername=$jwtexplode->name;
        $characterid=explode(":",$jwtexplode->sub)[2];
        $names=explode(" ",$charactername);
        $lastname=array_pop($names);
        $firstname=join(" ",$names);


        $chardetailsUrl="https://esi.evetech.net/latest/characters/".$characterid."/?datasource=tranquility";
        $response = $this->httpClient->request($chardetailsUrl);

        $data = json_decode($response);

        $allowed_corporations = str_replace(' ','',get_option("allowed_corporations",""));
        $allowed_alliances = str_replace(' ','',get_option("allowed_alliances",""));
        $allowed_characters = str_replace(' ','',get_option("allowed_characters",""));
        $allallowed=get_option("allowed_all",true);

        $allowed_corporations=explode(",",$allowed_corporations);
        $allowed_alliances=explode(",",$allowed_alliances);
        $allowed_characters=explode(",",$allowed_characters);

        $allowed=0;

        if ($allallowed) {
            $allowed=1;
        } elseif (in_array($characterid, $allowed_characters)) {
            $allowed=2;
        } elseif (isset($data->alliance_id) && in_array($data->alliance_id, $allowed_alliances)) {
            $allowed=3;
        } elseif (in_array($data->corporation_id, $allowed_corporations)) {
            $allowed=4;
        }

        if ($allowed == 0) {
               throw new \Exception( "The Character is not in a permitted list" );
        }

        $userProfile = new User\Profile();
        $userProfile->identifier  = $characterid;
        $userProfile->displayName = $charactername;
        $userProfile->firstName = $firstname;
        $userProfile->lastName = $lastname;
        $userProfile->photoURL = "https://images.evetech.net/characters/".$characterid."/portrait?size=128";
        return $userProfile;

    }

}
