<?php
namespace Src\Helpers;

require_once __DIR__ . "/../../bootstrap.php";
use Curl\Curl;
//use Symfony\Component\Config;
use Noodlehaus\Config;
use Src\TableGateways\logger;

class zohoHelper{

    const CONFIG_URI = __DIR__ . "/../../config.json";
    const API_NAME = "Zoho_Helper";

    // URLS
    const INVOICES_URL = "https://books.zoho.com/api/v3/invoices";
    const TOKEN_URL = "https://accounts.zoho.com/oauth/v2/token";
    const CONTACTS_URL = "https://books.zoho.com/api/v3/contacts";

    private string $authToken;
    public Curl $curl;
    private Curl $refreshCurl;
    private Config $config;
    private logger $logger;

    public function __construct($db)
    {
        $this->db = $db;
        $this->curl = new Curl();
        $this->refreshCurl = new Curl();
        $this->logger = new logger($db);
        $this->config = Config::load(self::CONFIG_URI);
        $this->authToken = $this->config->get("zoho_auth");
    }
    public function __destruct()
    {
        $this->curl->close();
    }

    /**
     * Fetches a new auth token from Zoho
     *
     * @return false|string|null
     * string: zoho access token on success <br>
     * false: if errored out <i> (error saved in actlog) </i>
     */
    function refreshZohoToken(){
        $this->refreshCurl->setOpts( array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $this->refreshCurl->post(self::TOKEN_URL, [
            'refresh_token' => $_ENV["REFRESH_TOKEN"],
            'client_id' => $_ENV["CLIENT_ID"],
            'client_secret' => $_ENV["CLIENT_SECRET"],
            'redirect_uri' => 'http://www.zoho.com/books',
            'grant_type' => 'refresh_token',
        ]);

        if ($this->refreshCurl->error) {
//            echo 'Error: ' . $this->refreshCurl->errorCode . ': ' . $this->refreshCurl->errorMessage . "\n";
//            echo ("<pre>".print_r($this->refreshCurl->response,true)."</pre>");

            $this->logger->insertAuditLogEntry(self::API_NAME, "failed to refresh token | Code: "
                . $this->refreshCurl->errorCode. " | "
                . $this->refreshCurl->rawResponse);
            return false;

        } else { // success
//            echo 'Response:' . "\n";
            //    print_r($this->refreshCurl->response);
            //    json_decode($this->refreshCurl->response);
            //    echo ("<pre>".print_r($this->refreshCurl->response,true)."</pre>");
//            echo ("<pre>".print_r($this->refreshCurl->response,true)."</pre>");
            $token = $this->refreshCurl->response->access_token;
            $this->updateAuthToken($token);
            return $token;

        }

    }

    function updateAuthToken($newValue)
    {
        $this->authToken = $newValue;
        $this->config->set("zoho_auth", $newValue);
        $this->config->set("zoho_auth_last_refresh", date("Y-m-d H:i:s"));
        $this->config->toFile(self::CONFIG_URI);
    }

    // ================ GET Functions ===========================


    function getInvoices()
    {
        return $this->handleCurl(self::INVOICES_URL);
    }

    function getInvoice($id)
    {
        return $this->handleCurl(self::INVOICES_URL, $id);
    }

    function getContacts(){
        return $this->handleCurl(self::CONTACTS_URL);
    }

    function getContact($id){
        return $this->handleCurl(self::CONTACTS_URL, $id);
    }

    function handleCurl(...$args)
    {
        $result = $this->curlGet(...$args);

        if($result)
        {
            if(is_integer($result) && $result == 401)
            {
                $this->refreshZohoToken();
                $result = $this->curlGet(...$args);
            }
        }
        return $result;
    }

    /**
     * @param $url
     * @param string $id
     * @return false|int|null
     */
    function curlGet($url, $id = ""){
        $this->curl->setOpts(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->authToken"
            )));

        $this->curl->get("$url/$id" , [
            'organization_id' => $_ENV["ORG_ID"],
        ]);

        if ($this->curl->error) {
//            echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
//            echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            if($this->curl->errorCode == 401)
            {
                return 401;
            }else{
                // log error
                // $this->curl->rawResponse; // raw string
                // $this->curl->response; // array obj
                $this->logger->insertAuditLogEntry(self::API_NAME, "failed to retrieve invoices | Code: "
                    . $this->curl->errorCode. " | "
                    . $this->curl->rawResponse);
                return false;
            }

        } else {
            echo 'Response:' . "\n";
            //    print_r($this->curl->response);
            //    json_decode($this->curl->response);
            //    echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");
            return $this->curl->response;
        }
    }


    // ================ POST Functions ===========================

            /*$data = array(
                "aid" => "1",
                "uid" => "4",
                "zipcode" => "21544",
                "acc_name" => "test account name SAM",
                "state"=> "ALEX",
                "country"=> "EG",
                "city"=> "Alexandria",
                "address"=> "address test",
                "first_name"=> "Hossam",
                "last_name"=> "Elwahsh",
                "name"=> "Hossam Elwahsh",
                "email"=> "test@gmail.com",
                "admins" => array(
                0 => array(
                "first_name"=> "a first name",
                "last_name"=> "a last name",
                "email"=> "admin@vscription.com"
                ),
                1 => array(
                "first_name"=> "a first name",
                "last_name"=> "a last name",
                "email"=> "admin2@vscription.com"
                )
                )
            );*/

    function createContact($data)
    {
        $jsonArr = array (
            'contact_name' => $data["acc_name"],
            'company_name' => $data["acc_name"],
//            'website' => 'www.bowmanfurniture.com',
            'contact_type' => 'customer',
            'customer_sub_type' => 'business',
            'notes' => 'Created by vScription Transcribe',
            'billing_address' =>
                array (
                    'attention' => $data["name"],
                    'address' => $data["address"],
//                    'street2' => 'Suite 310',
//                    'state_code' => 'CA',
                    'city' => $data["city"],
                    'state' => $data["state"],
                    'zip' => $data["zipcode"],
                    'country' => $data["country"],
//                    'fax' => '+1-925-924-9600',
//                    'phone' => '+1-925-921-9201',
                ),
            'contact_persons' =>
                array (
                    0 =>
                        array (
//                            'salutation' => 'Mr',
                            'first_name' => $data["first_name"],
                            'last_name' => $data["last_name"],
                            'email' => $data["email"],
//                            'phone' => '+1-925-921-9201',
//                            'mobile' => '+1-4054439562',
//                            'designation' => 'Sales Executive',
//                            'department' => 'Sales and Marketing',
//                            'skype' => 'Zoho',
                            'is_primary_contact' => true,
//                            'enable_portal' => true,
                        )
                ),

            //            'credit_limit' => 1000,
//            'tags' =>
//                array (
//                    0 =>
//                        array (
//                            'tag_id' => 462000000009070,
//                            'tag_option_id' => 462000000002670,
//                        ),
//                ),
//            'is_portal_enabled' => true,
//            'currency_id' => 460000000000097,
//            'payment_terms' => 15,
//            'payment_terms_label' => 'Net 15',
//            'shipping_address' =>
//                array (
//                    'attention' => 'Mr.John',
//                    'address' => '4900 Hopyard Rd',
//                    'street2' => 'Suite 310',
//                    'state_code' => 'CA',
//                    'city' => 'Pleasanton',
//                    'state' => 'CA',
//                    'zip' => 94588,
//                    'country' => 'U.S.A',
//                    'fax' => '+1-925-924-9600',
//                    'phone' => '+1-925-921-9201',
//                ),
//            'default_templates' =>
//                array (
//                    'invoice_template_id' => 460000000052069,
//                    'estimate_template_id' => 460000000000179,
//                    'creditnote_template_id' => 460000000000211,
//                    'purchaseorder_template_id' => 460000000000213,
//                    'salesorder_template_id' => 460000000000214,
//                    'retainerinvoice_template_id' => 460000000000215,
//                    'paymentthankyou_template_id' => 460000000000216,
//                    'retainerinvoice_paymentthankyou_template_id' => 460000000000217,
//                    'invoice_email_template_id' => 460000000052071,
//                    'estimate_email_template_id' => 460000000052073,
//                    'creditnote_email_template_id' => 460000000052075,
//                    'purchaseorder_email_template_id' => 460000000000218,
//                    'salesorder_email_template_id' => 460000000000219,
//                    'retainerinvoice_email_template_id' => 460000000000220,
//                    'paymentthankyou_email_template_id' => 460000000000221,
//                    'retainerinvoice_paymentthankyou_email_template_id' => 460000000000222,
//                ),
//            'custom_fields' =>
//                array (
//                    0 =>
//                        array (
//                            'index' => 1,
//                            'value' => 'GBGD078',
//                        ),
//                ),
//            'opening_balance_amount' => 1200,
//            'exchange_rate' => 1,
//            'owner_id' => 460000000016051,
//            'tax_reg_no' => 12345678912345,
//            'gst_no' => '22AAAAA0000A1Z5',
//            'gst_treatment' => 'business_gst',
//            'tax_exemption_id' => 11149000000061054,
//            'tax_authority_id' => 11149000000061052,
//            'tax_id' => 11149000000061058,
//            'is_taxable' => true,
//            'facebook' => 'zoho',
//            'twitter' => 'zoho',
        );

        // Fill in System Admins
        foreach ($data['admins'] as $admin) {
            array_push($jsonArr['contact_persons'], $admin);
        }

        return $this->handleCurlPost(self::CONTACTS_URL, $data, json_encode($jsonArr));
    }



    function handleCurlPost($url, $dataArr, $jsonStr)
    {
        $result = $this->curlPost($url, $dataArr, $jsonStr);

        if($result)
        {
            if(is_integer($result) && $result == 401)
            {
                $this->refreshZohoToken();
                $result = $this->curlPost($url, $dataArr, $jsonStr);
            }
        }
        return $result;
    }

    // for create requests
    function curlPost($url, $dataArr, $jsonStr){
        $this->curl->setOpts(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => ,
//            CURLOPT_POSTFIELDS =>  array('JSONString' => $jsonStr),

            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->authToken",
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
//                'Content-Type: application/form-data;charset=UTF-8'
//                'Content-Type: multipart/form-data;charset=UTF-8'
            )));


        $this->curl->post("$url?organization_id=".$_ENV["ORG_ID"],
            "JSONString=$jsonStr"
        );

        if ($this->curl->error) {
//            echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
//            echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            if($this->curl->errorCode == 401)
            {
                return 401;
            }else{
                // log error
                // $this->curl->rawResponse; // raw string
                // $this->curl->response; // array obj
                $this->logger->insertAuditLogEntry(self::API_NAME,
                    "Failed to create contact for uid: " . $dataArr["uid"] . " | acc_id: " . $dataArr["aid"] .
                    " | Code: ". $this->curl->errorCode. " | "
                    . $this->curl->rawResponse);
                return false;
            }

        } else {
            if($this->curl->httpStatusCode == 201) // contact created :)
            {
                //
            }
            //  echo 'Response:' . "\n";
            //  print_r($this->curl->response);
            //  json_decode($this->curl->response);
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            return $this->curl->response;
        }
    }

}