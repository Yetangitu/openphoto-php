<?php
if(!class_exists('OAuthSimple'))
  require 'OAuthSimple.php';

class OpenPhotoOAuth
{
  protected $client;
  protected $host;
  protected $consumerKey;
  protected $consumerSecret;
  protected $token;
  protected $tokenSecret;
  protected $version = '1';
  protected $protocol = 'http';
  protected $requestTokenPath= '/v%d/oauth/token/request';
  protected $accessTokenPath = '/v%d/oauth/token/access';
  protected $authorizePath   = '/v%d/oauth/authorize';

  public function __construct($host, $consumerKey, $consumerSecret, $token = null, $tokenSecret = null)
  {
    $this->host = $host;
    $this->consumerKey = $consumerKey;
    $this->consumerSecret = $consumerSecret;
    $this->token = $token;
    $this->tokenSecret = $tokenSecret;
  }

  public function get($endpoint, $params = null)
  {
    $client = new OAuthSimple($this->consumerKey, $this->consumerSecret);
    $request = $client->sign(
      array(
        'action' => 'GET',
        'path' => $this->constructEndpoint($endpoint),
        'parameters' => $params,
        'signatures' => 
          array(
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'access_token' => $this->token,
            'access_secret' => $this->tokenSecret
          )
      )
    );
    $ch = curl_init($this->constructEndpoint($endpoint, true));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$request['header']}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
  }

  public function getAccessToken($params = null)
  {
    $client = new OAuthSimple($this->consumerKey, $this->consumerSecret);
  }

  public function getAuthorizeUrl($token, $params = null)
  {
    $client = new OAuthSimple($this->consumerKey, $this->consumerSecret);
    $request = $client->sign(
      array(
        'path' => '',
        'parameters' => $params,
        'signatures' => array(
          'consumer_key' => $this->consumerKey,
          'consumer_secret' => $this->consumerSecret
        )
      )
    );
    return $request['signed_url'];
  }

  public function getRequestToken($params = null)
  {

  }

  public function post($endpoint, $params = null)
  {
    $client = new OAuthSimple($this->consumerKey, $this->consumerSecret);
    $request = $client->sign(
      array(
        'action' => 'POST',
        'path' => $this->constructEndpoint($endpoint),
        'parameters' => $params,
        'signatures' => 
          array(
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'access_token' => $this->token,
            'access_secret' => $this->tokenSecret
          )
      )
    );
    $ch = curl_init($this->constructEndpoint($endpoint, true));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$request['header']}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
  }

  private function constructEndpoint($endpoint, $includeConsumerKey = false)
  {
    if($includeConsumerKey)
      return sprintf('http://%s%s?oauth_consumer_key=%s', $this->host, $endpoint, $this->consumerKey);
    else
      return sprintf('http://%s%s', $this->host, $endpoint);
  }
}
