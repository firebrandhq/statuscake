<?php

namespace StatusCake;

use Exception;

class Test extends Call
{
    const STATUS_UNKNOWN = null;
    const STATUS_UP      = 'Up';
    const STATUS_DOWN    = 'Down';
    
    const TYPE_HTTP = 'HTTP';
    const TYPE_PING = 'PING';
    const TYPE_TCP  = 'TCP';
    
    public function __construct(Credentials $credentials = null)
    {
        $this->credentials = $credentials;
    }
    
    /**
     * @var int|null
     */
    public $TestID = null;  // null == new check | update, LISTED ON getTests()
    
    /**
     * @var int
     */
    public $Paused = 0; // LISTED ON getTests()
    
    /**
     * @var string
     */
    public $WebsiteName = "UNSET"; // REQUIRED ON UPDATE, LISTED ON getTests()
    
    /**
     * @var string
     */
    public $WebsiteURL = "UNSET"; // REQUIRED ON UPDATE
    
    /**
     * @var int
     */
    public $Public = 0; // LISTED ON getTests()
    
    /**
     * @var string
     */
    public $TestType = self::TYPE_HTTP;  // HTTP,TCP,PING - REQUIRED ON UPDATE, LISTED ON getTests()
    
    /**
     * @var string|null
     */
    public $ContactGroup = null; // e.g. "PagerDuty"; // LISTED ON getTests()
    
    /**
     * @var int|null
     */
    public $Port = null;
    
    /**
     * @var int
     */
    public $Timeout = 30;
    
    /**
     * @var null|string
     */
    public $PingURL = null;
    
    /**
     * @var int
     */
    public $CheckRate = 300; // REQUIRED ON UPDATE
    
    /**
     * @var null|string
     */
    public $BasicUser = null;
    
    /**
     * @var null|string
     */
    public $BasicPass = null;
    
    /**
     * @var null|string
     */
    public $LogoImage = null;
    
    /**
     * @var int
     */
    public $Branding = 0; // 1 to disable branding
    
    /**
     * @var null|string
     */
    public $WebsiteHost = null;
    
    /**
     * @var int
     */
    public $Virus = 0;
    
    /**
     * @var string|null
     */
    public $FindString = null; //'SUCCESS';
    
    /**
     * @var int
     */
    public $DoNotFind = 0; // 1 to trigger alert if found
    
    /**
     * @var int
     */
    public $RealBrowser = 0; // 1 to enable real browser testing
    
    /**
     * @var int
     */
    public $TriggerRate = 5; // minutes to wait before sending an alert
    
    /**
     * @var null|string
     */
    public $TestTags = null;
    
    // theses are not in api settable? ... are returned by the list call =>
    
    /**
     * @var null|int
     */
    public $ContactID = null; // LISTED ON getTests()
    
    /**
     * @var null|string
     */
    public $Status = self::STATUS_UNKNOWN; // LISTED ON getTests()
    
    /**
     * @var null|int
     */
    public $Uptime = null; // LISTED ON getTests()
    
    /**
     * A period of data is two time stamps in which status has remained the same.
     *
     * @return array|mixed
     */
    public function getPeriods()
    {
        $response = $this->callApi('Tests/Periods?TestID=' . $this->TestID, 'GET');
        
        if (is_array($response))
        {
            return $response;
        }
        
        throw new Exception('StatusCake API Error - Test periods retrieval failed.');
    }
    
    /**
     * Retrieves a list of checks performed for the current site.
     * NOTE: this is a premium feature.
     *
     * @param $parameters
     *
     * @return mixed
     */
    public function getPerformance($parameters)
    {
        $parameters['TestID'] = $this->TestID;
    
        $response = $this->callApi('Tests/Periods', 'GET', $parameters);
    
        if (is_array($response))
        {
        
            return $response;
        }
    
        throw new Exception('StatusCake API Error - Test performance retrieval failed.');
    }
    
    /**
     * Retrieve a list of alerts that have been sent for this test.
     *
     * @return mixed
     */
    public function getAlerts()
    {
        $response = $this->callApi('Tests/Periods?TestID='.$this->TestID, 'GET');
    
        if (is_array($response))
        {
            return $response;
        }
    
        throw new Exception('StatusCake API Error - Test Alerts retrieval failed.');
    }

    /**
     * Fetch test detailed test data
     *
     * @return $this
     * @throws Exception
     */
    public function getDetailedData()
    {
        $response = $this->callApi('Tests/Details?TestID='.$this->TestID, 'GET');

        if (!is_object($response))
        {
            throw new Exception('StatusCake API Error - Test Alerts retrieval failed.');
        }

        foreach ($response as $key => $testDataValue) {
            $this->{$key} = $testDataValue;
        }

        return $this;
    }
}
