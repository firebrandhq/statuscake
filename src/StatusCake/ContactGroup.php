<?php

namespace StatusCake;

class ContactGroup
{
    /**
     * [Required on update or delete]
     * Provide to Update a Contact Group Rather Than Insert New
     *
     * @var int|null
     */
    public $ContactID = null;
    
    /**
     * [Required]
     * The internal Group Name.
     * @var string
     */
    public $GroupName = 0;
    
    /**
     * @var int
     */
    public $DesktopAlert = 0; // Set to 1 To Enable Desktop Alerts
    
    /**
     * Comma Seperated List of Emails To Alert. (no whitespace)
     *
     * @var array
     */
    public $Email = [];
    
    /**
     * A Boxcar API Key
     *
     * @var string
     */
    public $BoxCar = '';
    
    /**
     * A Pushover Account Key
     *
     * @var string
     */
    public $PushOver = '';
    
    /**
     * A URL To Send a POST alert.
     *
     * @var string
     */
    public $PingURL = '';
    
    /**
     * Will be converted to a Comma Seperated List of International Format Cell Numbers
     * @var array
     */
    public $Mobile = [];
    
    public function isNew()
    {
        return $this->ContactID === null;
    }
    
    /**
     * Adds an email.
     *
     * @param $email
     *
     * @return $this
     */
    public function addEmail($email)
    {
        if (!in_array($email, $this->Email))
        {
            $this->Email[] = $email;
        }
        
        return $this;
    }
    
    /**
     * Removes an email.
     *
     * @param $email
     *
     * @return $this
     */
    public function removeEmail($email)
    {
        $key = array_search($email, $this->Email);
        
        if ($key === false)
        {
            return $this;
        }
        
        unset( $this->Email[$key] );
        
        return $this;
    }
    
    /**
     * Parses the contact group for the API request.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'ContactId' => $this->ContactID,
            'GroupName' => $this->GroupName,
            'DesktopAlert' => $this->DesktopAlert,
            'BoxCar' => $this->BoxCar,
            'PushOver' => $this->PushOver,
            'PingURL' => $this->PingURL,
            'Email' => implode(',', $this->Email),
            'Mobile' => implode(',', $this->Mobile),
        ];
    }
}
