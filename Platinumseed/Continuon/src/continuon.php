<?php
namespace Platinumseed\Continuon;

use Config;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;

class Continuon
{
    private $client;

    public function __construct() {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => Config::get('continuon.continuon.url') . '/api/v1/'
        ]);
    }

    public function get_access() {
        $data = [
            'grant_type'    => 'client_credentials',
            'scope'         => 'admin',
            'client_id'     => Config::get('continuon.continuon.client_id'),
            'client_secret' => Config::get('continuon.continuon.client_secret')
        ];

        try {
    		$response = $this->client->post('/oauth/access_token', [
                'form_params' => $data
            ]);

    		$server_output = $response->getBody();
    		$continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo "continuon response";
            echo $responseBodyAsString;
            die; //TODO Find proper pay to deal with these errors
        }
    }

    public function register($data)
	{
        $data['brand_user']['brand_id'] = Config::get('continuon.continuon.brand_id');
		$data['source']['token'] = Config::get('continuon.continuon.token');

		$query = '?' . http_build_query($data);
        try {
    		$response = $this->client->post('signup' . $query);
    		$server_output = $response->getBody();
    		$continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            // $response = $e->getResponse();
            // $responseBodyAsString = $response->getBody()->getContents();
            // echo $responseBodyAsString;
            // die; //TODO Find proper pay to deal with these errors
        }

        return false;
	}

    public function login($user_data, $provider)
    {
        $paramaters = [
            'grant_type'    => 'password',
            'scope'         => 'user',
            'client_id'     => Config::get('continuon.continuon.client_id'),
            'client_secret' => Config::get('continuon.continuon.client_secret'),
            'username'      => '',
            'password'      => ''
        ];

        //login with FB
        if ($provider == 'facebook')
        {
            $paramaters['facebook_access_token'] = $user_data->token;
        }
        //Login with twitter
        elseif ($provider == 'twitter')
        {
            $paramaters['twitter_oauth_token']    = $user_data->token;
            $paramaters['twitter_oauth_token_secret'] = $user_data->tokenSecret;
        }
        //Login with instagram
        elseif ($provider == 'instagram')
        {
            $paramaters['instagram_access_token'] = $user_data->token;
        }
        //Login with username and password
        elseif ($provider == 'email') {
            $paramaters['username'] = $user_data['credentials']['username'];
            $paramaters['password'] = $user_data['credentials']['password'];
        }

        //dd($paramaters);

        try {
            $response = $this->client->post('/oauth/access_token', ['form_params' => $paramaters]);
            $server_output = $response->getBody();
            $continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            // $response = $e->getResponse();
            // $responseBodyAsString = $response->getBody()->getContents();
            // echo $responseBodyAsString;
            // die; //TODO Find proper pay to deal with these errors
        }

        return false;
    }

    public function password_reset($email) {

        $paramaters = [
            'email' => $email
        ];

        try {
            $response = $this->client->post('/password/remind', ['form_params' => $paramaters]);
            $server_output = $response->getBody();
            $continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            //echo $e->getMessage();
            //die; //TODO Find proper pay to deal with these errors
        }

        return false;
    }

    public function get($endpoint, $params)
    {
        //Multiple params including access token
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        //Params is the access token
        else
        {
            $params = 'access_token=' . $params;
        }
        $query = $endpoint . '?' . $params;
        try {
            $response = $this->client->get($query);
    		$server_output = $response->getBody();
    		$continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            // $response = $e->getResponse();
            // $responseBodyAsString = $response->getBody()->getContents();
            // echo "continuon response";
            // echo $responseBodyAsString;
            // die; //TODO Find proper pay to deal with these errors
        }

        return false;
    }

    public function put($endpoint, $data, $access_token = null)
    {
        $options = [
            'form_params' => $data,
        ];

        if ($access_token != null)
        {
            $options['headers'] = [
                'Authorization' => ['Bearer ' . $access_token],
            ];
        }

        try {
            $response = $this->client->put($endpoint, $options);
            $server_output = $response->getBody();
    		$continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            // $response = $e->getResponse();
            // $responseBodyAsString = $response->getBody()->getContents();
            // echo "continuon response";
            // echo $responseBodyAsString;
            // die; //TODO Find proper pay to deal with these errors
        }

        return false;
    }

    public function post($endpoint, $data, $access_token = null)
    {
        $options = [
            'form_params' => $data
        ];
        if ($access_token != null)
        {
            $options['headers'] = [
                'Authorization' => ['Bearer ' . $access_token],
            ];
        }

        try {
            $response = $this->client->post($endpoint, $options);
            $server_output = $response->getBody();
    		$continuon_response = json_decode($server_output);
            return $continuon_response;
        } catch (RequestException $e) {
            // $response = $e->getResponse();
            // $responseBodyAsString = $response->getBody()->getContents();
            // echo 'Continuon response';
            // echo $responseBodyAsString;
            // die; //TODO Find proper pay to deal with these errors
        }

        return false;
    }

}
