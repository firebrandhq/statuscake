<?php

namespace StatusCake;

use Exception;

class Client extends Call
{
    /**
     * Set the API credentials
     *
     * @param string $username
     * @param string $token
     */
    public function __construct($username, $token)
    {
        $credentials = new Credentials();
        $credentials->user = $username;
        $credentials->token = $token;
        
        $this->registerCredentials($credentials);
    }

    /**
     * @param mixed $response
     * @return array
     * @throws Exception
     */
    protected function buildTestsFromResponse($response)
    {
        // Check for success
        if (!is_array($response)) {

            throw new Exception('StatusCake API Error - No list response on getTests.');
        }

        $testList = array();

        foreach ($response as $testData) {

            $testItem = new Test($this->credentials);

            foreach ($testData as $key => $testDataValue) {

                $testItem->{$key} = $testDataValue;
            }

            $testList[] = $testItem;
        }

        return $testList;
    }

    /**
     * Returns a list overview of all tests
     */
    public function getTests()
    {
        $response = $this->callApi('Tests');
        return $this->buildTestsFromResponse($response);
    }

    /**
     * @param array|null $tags
     * @param bool $matchAny
     * @param string $status [Up/Down]
     * @return Test[]
     * @throws Exception
     */
    public function findTests(array $tags = null, $matchAny = true, $status = null)
    {
        $query = array(
            'matchany' => $matchAny ? 'true' : 'false',
        );
        if ($tags) {
            $query['tags'] = join(',', $tags);
        }

        if ($status) {
            $query['status'] = $status;
        }

        $response = $this->callApi('Tests?' . http_build_query($query));
        return $this->buildTestsFromResponse($response);
    }
    
    /**
     * updates or creates a test
     *
     * @param \StatusCake\Test $test
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateTest(Test $test)
    {

        $data = array();

        // convert Test data to API structure
        foreach ($test as $key => $value) {

            if ($value != null) {

                $data[ucfirst($key)] = $value;
            }
        }

        $response = $this->callApi('Tests/Update', 'PUT', $data);

        // Check for success
        if (is_object($response) && ($response->Success == true)) {
            
            if(property_exists($response, 'InsertID'))
            {
                $test->ContactID = $response->InsertID;
                $test->registerCredentials($this->credentials);
            }
            
            return $response;
        }

        throw new Exception('StatusCake API Error - Test update failed.');

    }

    /**
     * Deletes a test
     *
     * @param \StatusCake\Test $test
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteTest(Test $test)
    {
        if ((int)$test->TestID == 0) {

            throw new Exception('Illegal Test/TestID.');
        }

        $response = $this->callApi(
            'Tests/Details/?TestID=' . (int)$test->TestID,
            'DELETE'
        );

        // Check for success
        if (is_object($response) && ($response->Success == true)) {

            return $response;
        }
    
        throw new Exception('StatusCake API Error - Test deletion failed.');
    }
    
    /**
     * Return user info
     *
     * @return mixed
     */
    public function account()
    {
        try {
            $response = $this->callApi('Auth');
        
            if(is_object($response) && $response->Success)
            {
                return $response->Details;
            }
    
            throw new Exception('StatusCake API Error - Account authentication failed.');
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Simply test the credentials.
     *
     * @return mixed
     */
    public function validAccount()
    {
        try {
            $response = $this->account();
            
            return is_object($response);
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Get the contact groups.
     *
     * @return array
     * @throws \Exception
     */
    public function getContactGroups()
    {
        $groups = [];
        
        $data = $this->callApi('ContactGroups');
    
    
        // Check for success
        if (!is_array($data)) {
        
            throw new Exception('StatusCake API Error - No list response on getContactGroups.');
        }
        
        // No groups found
        if(count($data) == 0)
            return [];
        
        // Loop over existing groups
        foreach($data as $contactGroup)
        {
            // Initiate
            $group = new ContactGroup();
            
            // Set attributes
            foreach($contactGroup as $key => $val)
            {
                $group->{$key} = $val;
            }
            
            // Add to list
            $groups[] = $group;
        }
        
        return $groups;
    }
    
    /**
     * Create/update a contact group.
     *
     * @param \StatusCake\ContactGroup $contactGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateContactGroup(ContactGroup $contactGroup)
    {
        $data = $contactGroup->toArray();
        
        $response = $this->callApi('ContactGroups/Update', 'PUT', $data);
        
        // Check for success
        if (is_object($response) && ($response->Success == true)) {
            
            return $response;
        }
        
        throw new Exception('StatusCake API Error - ContactGroup update failed.');
    }
    
    /**
     * Deletes a ContactGroup
     *
     * @param \StatusCake\ContactGroup $contactGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteContactGroup(ContactGroup $contactGroup)
    {
        if ($contactGroup->isNew() || (int)$contactGroup->ContactID == 0) {
            throw new Exception('Illegal Contact/ContactID.');
        }
        
        $response = $this->callApi(
            'ContactGroups/Update/?ContactID=' . (int)$contactGroup->ContactID,
            'DELETE'
        );
        
        // Check for success
        if (is_object($response) && ($response->Success == true)) {
            
            return $response;
        }
        
        throw new Exception('StatusCake API Error - ContactGroup deletion failed.');
    }

    
}
