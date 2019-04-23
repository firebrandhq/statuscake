<?php
/**
 * Created by PhpStorm.
 * User: maximkerstens
 * Date: 10/10/16
 * Time: 15:19
 */

namespace StatusCake;


class Call
{
    /**
     * @var string
     */
    protected $url = "https://app.statuscake.com/API";

    protected $credentials;
    
    public function registerCredentials(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }
    
    /**
     * @param        $path
     * @param string $method
     * @param null   $data
     *
     * @return mixed
     * @throws \Exception
     */
    protected function callApi($path, $method = 'GET', $data = null)
    {
        // Create the CURL String
        $ch = curl_init($this->url . '/' . $path);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data !== null)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Username: " . $this->credentials->user,
            "API: " . $this->credentials->token,
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch) !== 0)
        {
            throw new Exception(curl_error($ch));
        }
        
        return json_decode($response);
    }
}
