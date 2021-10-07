<?php
namespace Src\Helpers;

require_once __DIR__ . "/../../bootstrap.php";
use Curl\Curl;
//use Symfony\Component\Config;
//use CURLFile;
use Noodlehaus\Config;
use Src\Enums\ENV;
use Src\Enums\ROLES;
use Src\Models\User;
use Src\Models\ZohoBill;
use Src\Models\ZohoInvoice;
use Src\Models\ZohoUser;
use Src\TableGateways\logger;
use Src\TableGateways\zohoGateway;

class zohoHelper{

    const CONFIG_URI = __DIR__ . "/../../config.json";
    const API_NAME = "Zoho_Helper";

    // request sources
    const BILL_CREATE = 31;
    const INVOICE = 52;

    // URLS
    const INVOICES_URL = "https://books.zoho.com/api/v3/invoices";
    const BILLS_URL = "https://books.zoho.com/api/v3/bills";
    const TOKEN_URL = "https://accounts.zoho.com/oauth/v2/token";
    const CONTACTS_URL = "https://books.zoho.com/api/v3/contacts";
    const CONTACTPERSON_URL = "https://books.zoho.com/api/v3/contacts/contactpersons";

    private string $authToken;
    private string $zohoClientItemId;
    public Curl $curl;
    public $zohoGateway;
    private Curl $refreshCurl;
    private Config $config;
    private logger $logger;
    private common $common;

    public function __construct($db)
    {
        $this->db = $db;
        $this->curl = new Curl();
        $this->refreshCurl = new Curl();
        $this->zohoGateway = new zohoGateway($db);
        $this->logger = new logger($db);
        $this->common = new common();
        $this->config = Config::load(self::CONFIG_URI);

        $this->zohoClientItemId = $this->config->get("zoho_client_billing_item_id");
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
//            echo 'Response:' . "\n";
            //    print_r($this->curl->response);
            //    json_decode($this->curl->response);
            //    echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");
            return $this->curl->response;
        }
    }


    // ================ POST Functions ===========================

            /*
             * Create Contact Sample
               $data = array(
                "fail_msg" => "Failed to create invoice",
                "fail_ref" => "uid",
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
                "client_admins" => array(
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

    /**
     * @param array $data contact data
     * @param string $contactType customer|vendor
     * @return false|int|null|mixed
     */
    function createContact($data, $contactType)
    {
        $jsonArr = array (
            'contact_name' => $data["acc_name"],
            'company_name' => $data["acc_name"],
            // 'website' => 'www.bowmanfurniture.com',
            'contact_type' => $contactType,
            'customer_sub_type' => 'business',
            'notes' => 'Created by vScription Transcribe',
            'billing_address' =>
                array (
                    'attention' => $data["name"],
                    'address' => $data["address"],
                    //  'street2' => 'Suite 310',
                    //  'state_code' => 'CA',
                    'city' => $data["city"],
                    'state' => $data["state"],
                    'zip' => $data["zipcode"],
                    'country' => $data["country"],
                    //  'fax' => '+1-925-924-9600',
                    //  'phone' => '+1-925-921-9201',
                ),
            'contact_persons' => $data['client_admins'],
                        /*,
                        'contact_persons' =>
                            array (
                                0 =>
                                    array (
            //          'salutation' => 'Mr',
                                        'first_name' => $data["first_name"],
                                        'last_name' => $data["last_name"],
                                        'email' => $data["email"],
            //          'phone' => '+1-925-921-9201',
            //          'mobile' => '+1-4054439562',
            //          'designation' => 'Sales Executive',
            //          'department' => 'Sales and Marketing',
            //          'skype' => 'Zoho',
                                        'is_primary_contact' => true,
            //          'enable_portal' => true,
                                    )
                            ),*/

                        //            'credit_limit' => 1000,
            //            'tags' =>
            //                array (
            //  0 =>
            //      array (
            //          'tag_id' => 462000000009070,
            //          'tag_option_id' => 462000000002670,
            //      ),
            //                ),
            //            'is_portal_enabled' => true,
            //            'currency_id' => 460000000000097,
            //            'payment_terms' => 15,
            //            'payment_terms_label' => 'Net 15',
            //            'shipping_address' =>
            //                array (
            //  'attention' => 'Mr.John',
            //  'address' => '4900 Hopyard Rd',
            //  'street2' => 'Suite 310',
            //  'state_code' => 'CA',
            //  'city' => 'Pleasanton',
            //  'state' => 'CA',
            //  'zip' => 94588,
            //  'country' => 'U.S.A',
            //  'fax' => '+1-925-924-9600',
            //  'phone' => '+1-925-921-9201',
            //                ),
            //            'default_templates' =>
            //                array (
            //  'invoice_template_id' => 460000000052069,
            //  'estimate_template_id' => 460000000000179,
            //  'creditnote_template_id' => 460000000000211,
            //  'purchaseorder_template_id' => 460000000000213,
            //  'salesorder_template_id' => 460000000000214,
            //  'retainerinvoice_template_id' => 460000000000215,
            //  'paymentthankyou_template_id' => 460000000000216,
            //  'retainerinvoice_paymentthankyou_template_id' => 460000000000217,
            //  'invoice_email_template_id' => 460000000052071,
            //  'estimate_email_template_id' => 460000000052073,
            //  'creditnote_email_template_id' => 460000000052075,
            //  'purchaseorder_email_template_id' => 460000000000218,
            //  'salesorder_email_template_id' => 460000000000219,
            //  'retainerinvoice_email_template_id' => 460000000000220,
            //  'paymentthankyou_email_template_id' => 460000000000221,
            //  'retainerinvoice_paymentthankyou_email_template_id' => 460000000000222,
            //                ),
            //            'custom_fields' =>
            //                array (
            //  0 =>
            //      array (
            //          'index' => 1,
            //          'value' => 'GBGD078',
            //      ),
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

        return $this->handleCurlPost(self::CONTACTS_URL, $data,
            array('JSONString' => json_encode($jsonArr))
        );
    }


    function updateContact($data, ZohoUser $oldContact)
    {
        $jsonArr = array (
            'billing_address' =>
                array (
                    'attention' => $data["name"],
                    'address' => $data["address"],
                    //  'street2' => 'Suite 310',
                    //  'state_code' => 'CA',
                    'city' => $data["city"],
                    'state' => $data["state"],
                    'zip' => $data["zipcode"],
                    'country' => $data["country"]
                    //  'fax' => '+1-925-924-9600',
                    //  'phone' => '+1-925-921-9201',
                )
        );

        return $this->handleCurlPut(self::CONTACTS_URL, $data,
            array('JSONString' => json_encode($jsonArr))
        );
    }

    function createContactPerson($personData)
    {
        $jsonArr =array (
            'first_name' => $personData['first_name'],
            'last_name' => $personData['last_name'],
            'email' => $personData['email'],
            'designation' => $personData['role'],
            'enable_portal' => $personData['portal']
        );

        return $this->handleCurlPost(self::CONTACTPERSON_URL, $personData,
            array('JSONString' => json_encode($jsonArr))
        );
    }

    /*
     * Data Sample
     $attachInvoiceData = array(
        "fail_msg" => "Failed to attach file to invoice",
        "fail_ref" => "2784469000000088016 with C:/Users/Hossam/Downloads/Documents/newattach.pdf",
        "invoice_id" => "2784469000000088016",
        "file_path" => "C:/Users/Hossam/Downloads/Documents/newattach.pdf"
    );

    */
    function attachToInvoice($data)
    {
//        $curlFile = new CURLFILE($data["file_path"],"application/pdf", basename($data["file_path"]));
        $curlFile = curl_file_create($data["file_path"],"application/pdf", basename($data["file_path"]));
        $postData = array(
            'can_send_in_mail' => 'true',
            'attachment' => $curlFile
        );


        return $this->handleCurlPost(self::INVOICES_URL."/".$data["invoice_id"]."/attachment", $data,
                $postData, '');
    }

    function emailInvoice($invoiceID)
    {
//        $curlFile = new CURLFILE($data["file_path"],"application/pdf", basename($data["file_path"]));

        return $this->handleCurlPost(self::INVOICES_URL."/".$invoiceID."/email",null,
                array(), '');
    }


    /**
     * @param $data array Model:
     *  $data = array (
            "fail_msg" => ,
            "fail_ref" => ,
            "aid" => ,
            "zoho_contact_id" => ,
            "uid" => ,
            "bill_rate1" => ,
            "minutes" => ,
            "admin_ids" => array()
        );
     * returns false if failed
     * returns response mixed string if success
     */
    function createInvoice($data) : mixed
    {

        $jsonArr = array(
            'customer_id' => $data["zoho_contact_id"],
            'contact_persons' => $data['admin_ids'],
            'date' => date("Y-m-d"),
            //            'invoice_number' => 'INV-00003',
            //            'reference_number' => ' ',
            'notes' => 'Created by vScription Transcribe.',
            'terms' => 'Terms & Conditions apply',
            'line_items' =>
                array (
                    0 =>
                        array (
                            'item_id' => $this->zohoClientItemId,
//                            'product_type' => 'services',
                            //  'description' => '500GB, USB 2.0 interface 1400 rpm, protective hard case.',
                            //  'item_order' => 1,
                            //  'bcy_rate' => 120,
                            'rate' => $data["bill_rate1"],
                            'quantity' => $data["minutes"],
//                            'unit' => 'minute',
                            //  'discount_amount' => 0,
                            //  'discount' => 0,
                            //  'tags' =>
                            //      array (
                            //          0 =>
                            //              array (
                            //                  'tag_id' => 982000000009070,
                            //                  'tag_option_id' => 982000000002670,
                            //              ),
                            //      ),
                            //  'tax_id' => 982000000557028,
                            //  'tax_name' => 'VAT',
                            //  'tax_type' => 'tax',
                            //  'tax_percentage' => 12.5,
                            //  'tax_treatment_code' => 'uae_others',
                            //  'header_name' => 'Electronic devices',
                        ),
                ),
            //    'payment_options' =>
            //        array (
            //            'payment_gateways' =>
            //                array (
            //  0 =>
            //      array (
            //          'additional_field1' => 'standard',
            //          'gateway_name' => 'paypal',
            //      ),
            //                ),
            //        ),
            //    'allow_partial_payments' => true,
            //    'custom_body' => ' ',
            //    'custom_subject' => ' ',
            //    'shipping_charge' => 0,
            //    'adjustment' => 0,
            //    'adjustment_description' => ' ',
            //    'reason' => ' ',
            //    'tax_authority_id' => 11149000000061052,
            //    'tax_exemption_id' => 11149000000061054,
            //    'tax_id' => 982000000557028,
            //    'expense_id' => ' ',
            //    'salesorder_item_id' => ' ',
            //    'time_entry_ids' =>
            //        array (
            //        ),
            //    'template_id' => 982000000000143,
            //    'payment_terms' => 15,
            //    'payment_terms_label' => 'Net 15',
            //    'due_date' => '2013-12-03',
            //    'discount' => 0,
            //    'is_discount_before_tax' => true,
            //    'discount_type' => 'item_level',
            //    'is_inclusive_tax' => false,
            //    'exchange_rate' => 1,
            //    'recurring_invoice_id' => ' ',
            //    'invoiced_estimate_id' => ' ',
            //    'salesperson_name' => ' ',
            //    'custom_fields' =>
            //        array (
            //            0 =>
            //                array (
            //  'customfield_id' => '46000000012845',
            //  'value' => 'Normal',
            //                ),
            //        ),
        );

/*        // Fill in System Admins
        foreach ($data['admin_ids'] as $admin) {
            array_push($jsonArr['contact_persons'], $admin);
        }*/

        return $this->handleCurlPost(self::INVOICES_URL, $data,
                array('JSONString' => json_encode($jsonArr))
            , reqSrc: self::INVOICE);
    }

    /**
     * @param $data array Model:
     *  $data = array (
            "fail_msg" => "Failed to create bill",
            "fail_ref" => "step2",
            "zoho_contact_id" => $zohoPrimaryUser->getZohoContactId(),
            "uid" => $zohoPrimaryUser->getUid(),
            "quantity" => $billData['quantity'],
            "attachment" => $billData['attachment']
        );
     * returns false if failed
     * returns response mixed string if success
     */
    function createBill($data) : mixed
    {

        $jsonArr = array(
            'vendor_id' => $data["zoho_contact_id"],
            'bill_number' => $this->zohoGateway->getNextBillNumber(),
            'date' => date("Y-m-d"),
            //            'invoice_number' => 'INV-00003',
            //            'reference_number' => ' ',
            'notes' => 'Created by vScription Transcribe.',
            'terms' => 'Terms & Conditions apply',
            'line_items' =>
                array (
                    0 =>
                        array (
                            'item_id' => $this->zohoClientItemId,
//                            'product_type' => 'services',
                            //  'description' => '500GB, USB 2.0 interface 1400 rpm, protective hard case.',
                            //  'item_order' => 1,
//                              'bcy_rate' => 120,
                            'rate' => $data["total_payment"],
                            'quantity' => $data["quantity"]
//                            'unit' => 'minute',
                            //  'discount_amount' => 0,
                            //  'discount' => 0
                        ),
                ),
        );

        /* PDF File Creation */
        $file_tmp = $_FILES['pdf']['tmp_name'];
        $targetDir = "../../data/bills/";
        if(!is_dir($targetDir))
        {
            mkdir($targetDir, 0777, true);
        }
        $pdfTarget = $targetDir . $_POST['pdfName']. time() . ".pdf";
        move_uploaded_file($file_tmp, $pdfTarget);

//        unlink($pdfTarget); // delete file from server

        // PDF END ================================


//        $curlFile = curl_file_create($data["attachment"],"application/pdf", basename($data["attachment"]));
        $curlFile = curl_file_create($pdfTarget,"application/pdf", basename($pdfTarget));


//        $postData = array(
//            'JSONString' => json_encode($jsonArr),
////            'can_send_in_mail' => 'true',
//            'attachment' => $curlFile
//        );

//        $postData = array('JSONString' => json_encode($jsonArr));
        $postData = array('JSONString' => json_encode($jsonArr),
            'attachment' => $curlFile
        );

        $result = $this->handleCurlPost(self::BILLS_URL, $data,
            $postData, 'Type:application/json;charset=UTF-8',
            reqSrc: self::BILL_CREATE);

        unlink($pdfTarget);

        return $result;
//            , self::BILL_CREATE);
    }

    function attachToBill($data)
    {
//        $curlFile = new CURLFILE($data["file_path"],"application/pdf", basename($data["file_path"]));
        $curlFile = curl_file_create($data["file_path"],"application/pdf", basename($data["file_path"]));
        $postData = array(
//            'can_send_in_mail' => 'true',
            'attachment' => $curlFile
        );


        return $this->handleCurlPost(self::BILLS_URL."/".$data["bill_id"]."/attachment", $data,
            $postData, '');
    }
    function emailBill($billID)
    {
//        $curlFile = new CURLFILE($data["file_path"],"application/pdf", basename($data["file_path"]));

        return $this->handleCurlPost(self::BILLS_URL."/".$billID."/email",null,
            array(), '');
    }


    function handleCurlPost($url, $dataArr, array $postData, $contentType = 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8', $reqSrc = 0)
    {
//        $this->refreshZohoToken();

        $result = $this->curlPost($url, $dataArr, $postData, $contentType, $reqSrc);

        if($result)
        {
            if(is_integer($result) && $result == 401)
            {
                $this->refreshZohoToken();
                $result = $this->curlPost($url, $dataArr, $postData, $contentType, $reqSrc);
                if($result === 401)
                {
                    $this->logger->insertAuditLogEntry(self::API_NAME, "Zoho auth refresh failed.");
                    $result = false;
                }
            }
        }
        return $result;
    }

    function handleCurlPut($url, $dataArr, array $postData, $contentType = 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8', $reqSrc = 0)
    {
//        $this->refreshZohoToken();

        $result = $this->curlPut($url, $dataArr, $postData, $contentType, $reqSrc);

        if($result)
        {
            if(is_integer($result) && $result == 401)
            {
                $this->refreshZohoToken();
                $result = $this->curlPut($url, $dataArr, $postData, $contentType, $reqSrc);
                if($result === 401)
                {
                    $this->logger->insertAuditLogEntry(self::API_NAME, "Zoho auth refresh failed.");
                    $result = false;
                }
            }
        }
        return $result;
    }

    // for create requests
    function curlPost($url, $dataArr, array $postData, $contentType, $req_src = 0){
        $this->curl->setOpts(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',

            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->authToken",
                $contentType
            //  'Content-Type: application/json;charset=UTF-8'
            //  'Content-Type: application/form-data;charset=UTF-8'
            //  'Content-Type: multipart/form-data;charset=UTF-8'
            )));


//        echo $jsonStr;
//        echo ("<pre>".print_r($postData)."</pre>");


        $this->curl->post("$url?organization_id=".$_ENV["ORG_ID"],
//            array('JSONString' => $jsonStr)
            $postData
        );

//        echo ("<pre>".print_r($jsonStr,true)."</pre>");
//        echo ("<pre>".print_r(json_decode($jsonStr, true),true)."</pre>");

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
                    $dataArr["fail_msg"] . " | ref: " . $dataArr["fail_ref"] .
                    " | Code: ". $this->curl->errorCode. " | "
                    . $this->curl->rawResponse);
                if($req_src === self::INVOICE ){
                    (new ZohoInvoice(
                        id: 0,
                        invoice_number: 'FAIL',
                        zoho_contact_id: $dataArr['zoho_contact_id'],
                        local_invoice_data: json_encode(array('local'=>$dataArr, 'formatted'=> json_decode($postData['JSONString']))),
                        zoho_invoice_data: json_encode($this->curl->response),
                        db: $this->db
                    ))->save();
                }
                else if($req_src === self::BILL_CREATE ){
                    (new ZohoBill(
                        id: 0,
                        bill_number: 'FAIL',
                        zoho_contact_id: $dataArr['zoho_contact_id'],
                        local_bill_data: json_encode(array('local'=>$dataArr, 'formatted'=> json_decode($postData['JSONString']))),
                        zoho_bill_data: json_encode($this->curl->response),
                        db: $this->db
                    ))->save();
                }
                return false;
            }

        } else {
            if($this->curl->httpStatusCode == 201) // contact created :)
            {
                //
            }
            else if($this->curl->httpStatusCode == 200) // file attached etc.
            {

            }
            //  echo 'Response:' . "\n";
            //  print_r($this->curl->response);
            //  json_decode($this->curl->response);
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            return $this->curl->response;
        }
    }

    function curlPut($url, $dataArr, array $postData, $contentType, $req_src = 0){
        $this->curl->setOpts(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',

            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->authToken",
                $contentType
            //  'Content-Type: application/json;charset=UTF-8'
            //  'Content-Type: application/form-data;charset=UTF-8'
            //  'Content-Type: multipart/form-data;charset=UTF-8'
            )));


//        echo $jsonStr;
//        echo ("<pre>".print_r($postData)."</pre>");


        $this->curl->put("$url?organization_id=".$_ENV["ORG_ID"],
//            array('JSONString' => $jsonStr)
            $postData
        );

//        echo ("<pre>".print_r($jsonStr,true)."</pre>");
//        echo ("<pre>".print_r(json_decode($jsonStr, true),true)."</pre>");

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
                    $dataArr["fail_msg"] . " | ref: " . $dataArr["fail_ref"] .
                    " | Code: ". $this->curl->errorCode. " | "
                    . $this->curl->rawResponse);
                return false;
            }

        } else {
            if($this->curl->httpStatusCode == 201) // contact created :)
            {
                //
            }
            else if($this->curl->httpStatusCode == 200) // file attached etc.
            {

            }
            //  echo 'Response:' . "\n";
            //  print_r($this->curl->response);
            //  json_decode($this->curl->response);
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");
            //  echo ("<pre>".print_r($this->curl->response,true)."</pre>");

            return $this->curl->response;
        }
    }


    // ================ Procedure Functions ===========================

    /** Create Invoice from billing reports page request
     * <br>
     *  <b>Step 1:</b> check for user zoho_id if not exist -> createZohoContact -> save zoho_id locally
     *  <br><b>Step 2:</b>
     *  <br><b>Step x:</b>
     */
    function generateInvoiceBill($invoiceData)
    {
        // Step 1 - contact check
        $orgId = $invoiceData['org_id'];
        $orgName = $invoiceData['organization'];
        $contactType = $invoiceData['contact_type'];


//        return $this->common->generatePHPArrayResponse("Failed to create zoho invoice. (3-PDF)",true, 3);

//        return false;

        $zohoUser = $this->invoiceStep1Contact($orgId, $orgName, $contactType);
        if(!$zohoUser){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho contact. (1-User)",true, 1);
        }

        // create invoice
        $zohoInvoice = $this->invoiceStep2Invoice($invoiceData, $zohoUser);
        if(!$zohoInvoice){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho invoice. (2-Invoice)",true, 2);
        }

        // attach pdf to invoice
        $attached = $this->invoiceStep3AttachPdf($zohoInvoice, $zohoUser);
        if(!$attached){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho invoice. (3-PDF)",true, 3);
        }

        // send invoice mail
        $mailed = $this->invoiceStep4SendMail($zohoInvoice, $zohoUser);
        if(!$mailed){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho invoice. (4-MAIL)",true, 4);
        }

        // if all good mark as billed/bill
        $extraResponse = '';
        $marked = $this->invoiceStep5MAB($invoiceData['data']);
        if(!$marked){
            // failed and logged to actlog | Code is which step has failed
            $this->logger->insertAuditLogEntry(self::API_NAME, "Failed to MAB (client-billing-5) | Bill #".$zohoInvoice->getInvoiceNumber());
            $extraResponse = ' | Failed to mark files as billed, file ids can be retrieved from ZohoBill record';
//            return $this->common->generatePHPArrayResponse("Failed to create zoho bill. (5-Bill-MAIL)",true, 4);
        }


        $this->logger->insertAuditLogEntry(self::API_NAME, "#".$zohoInvoice->getInvoiceNumber()." Invoice Successfully Created by " . $_SESSION['uEmail']);
        return $this->common->generatePHPArrayResponse("Invoice Successfully Created | Invoice #" . $zohoInvoice->getInvoiceNumber() . $extraResponse);
    }


    function invoiceStep1Contact($orgId,$orgName, $contactType) : ZohoUser|bool
    {
        $zohoUser = ZohoUser::primaryWithAccID($orgId, $this->db);
        if(!$zohoUser)
        {
            // Get Client Admin of Org
            $clientAdmin = $this->zohoGateway->findMainClientAdminOfOrg($orgId);
            // create contact
            $data = array(
                "fail_msg" => "Failed to create invoice",
                "fail_ref" => "uid",
                "aid" => $clientAdmin->getAccount(),
                "uid" => $clientAdmin->getId(),
                "zipcode" => $clientAdmin->getZipcode(),
                "acc_name" => $orgName,
                "state"=> $clientAdmin->getState(),
                "country"=> $clientAdmin->getCountry(),
                "city"=> $clientAdmin->getCity(),
                "address"=> $clientAdmin->getAddress(),
                "first_name"=> $clientAdmin->getFirstName(),
                "last_name"=> $clientAdmin->getLastName(),
                "name"=> $clientAdmin->getFirstName() . " " . $clientAdmin->getLastName(),
                "email"=> $clientAdmin->getEmail(),
//                "client_admins" => $this->zohoGateway->findSystemAdmins()
                "client_admins" => array(
                        0 => array(
                            "first_name" => $clientAdmin->getFirstName(),
                            "last_name" => $clientAdmin->getLastName(),
                            "email" => $clientAdmin->getEmail(),
                            'is_primary_contact' => true // main client admin (who created the org)
                        )
                    )
                );
            // add other client admins if any
            $subClientAdmins = $this->zohoGateway->findAllSubClientAdminsOfOrg($orgId);
            if($subClientAdmins)
            {
                foreach ($subClientAdmins as $subClientAdmin) {
                    array_push($data['client_admins'],
                        array(
                            "first_name" => $subClientAdmin['first_name'],
                            "last_name" => $subClientAdmin['last_name'],
                            "email" => $subClientAdmin['email']
                        )
                    );
                }
            }

            $response = $this->createContact($data, $contactType);
            if(!$response)
            {
                // failed to create contact - exit
                $this->logger->insertAuditLogEntry(self::API_NAME,
                    "Failed to create invoice | on step 1: first time zoho contact creation | please check prev log record for details");
                return false;
            }
            // else
            // save zoho ids of contact to db
//            $responseArr = json_decode($response, true);

            // client admin (primary)
            $zohoPrimaryUser = new ZohoUser(
                    id: 0,
                    zoho_id: $response->contact->contact_persons[0]->contact_person_id,
                    zoho_contact_id: $response->contact->contact_id,
                    uid: $clientAdmin->getId(),
                    acc_id: $orgId,
                    type: ROLES::ACCOUNT_ADMINISTRATOR,
                    primary_contact: 1,
                    user_data: json_encode($response),
                    db: $this->db
                );
            $zohoPrimaryUser->save();

            // sub client admins
            if($subClientAdmins)
            {
                foreach ($subClientAdmins as $key=>$subClientAdmin) {
                    $zohoUser = new ZohoUser(
                        id: 0,
                        zoho_id: ($response->contact->contact_persons[$key + 1]->contact_person_id),
                        zoho_contact_id: $response->contact->contact_id,
                        uid: $subClientAdmin['id'],
                        acc_id: $orgId,
                        type: ROLES::ACCOUNT_ADMINISTRATOR,
                        primary_contact: 0,
//                            user_data: null,
                        db: $this->db
                    );
                    $zohoUser->save();
                }
            }

            return $zohoPrimaryUser;



        }else{
            return $zohoUser;
        }
    }
    function invoiceStep2Invoice($invoiceData, ZohoUser $zohoPrimaryUser) : ZohoInvoice|bool
    {
        $contactPersons = $this->zohoGateway->findAllZohoUsers($zohoPrimaryUser->getAccId());
        $adminsIds = array_column($contactPersons, "zoho_id");

        $data = array (
            "fail_msg" => "Failed to create invoice",
            "fail_ref" => "2784469000000090036",
            "aid" => $zohoPrimaryUser->getAccId(),
            "zoho_contact_id" => $zohoPrimaryUser->getZohoContactId(),
            "uid" => $zohoPrimaryUser->getUid(),
            "bill_rate1" => $invoiceData['bill_rate'],
            "minutes" => $invoiceData['quantity'],
            "admin_ids"=> $adminsIds
        );
        $response = $this->createInvoice($data);
        if(!$response)
        {
            // failed to create contact - exit
            $this->logger->insertAuditLogEntry(self::API_NAME,
                "Failed to create invoice | on step 2: invoice creation | please check prev log record for details");
            return false;
        }
        // else
        // save invoice id
//        $responseArr = json_decode($response, true);

        $invoice = new ZohoInvoice(
            id: 0,
            invoice_number: $response->invoice->invoice_number,
            zoho_contact_id: $zohoPrimaryUser->getZohoContactId(),
            zoho_invoice_id: $response->invoice->invoice_id,
            local_invoice_data: json_encode($invoiceData),
            zoho_invoice_data: json_encode($response),
            db: $this->db
        );
        $invoice->save();

        return $invoice;
    }
    function invoiceStep3AttachPdf(ZohoInvoice $invoice, ZohoUser $zohoPrimaryUser) : bool
    {

        $file_tmp = $_FILES['pdf']['tmp_name'];
        $targetDir = "../../data/invoices/";
        if(!is_dir($targetDir))
        {
            mkdir($targetDir, 0777, true);
        }
        $pdfTarget = $targetDir . $_POST['pdfName']. time() . ".pdf";
        move_uploaded_file($file_tmp, $pdfTarget);
//        unlink($file_tmp); // delete uploaded tmp file


        $data = array(
            "fail_msg" => "Failed to attach file to invoice",
            "fail_ref" => $invoice->getZohoInvoiceId() . " with " . $pdfTarget,
            "invoice_id" => $invoice->getZohoInvoiceId(),
            "file_path" => $pdfTarget
//            "file_path" => "C:/Users/Hossam/Downloads/Documents/newattach.pdf"
        );

        $response = $this->attachToInvoice($data);
        if(!$response)
        {
            // failed to create contact - exit
            $this->logger->insertAuditLogEntry(self::API_NAME,
                "Failed to create invoice | on step 3: invoice pdf attach | please check prev log record for details");
            return false;
        }

        unlink($pdfTarget); // delete file from server

        return true;
    }
    function invoiceStep4SendMail(ZohoInvoice $invoice) : bool
    {

        if(ENV::AUTO_EMAIL_ZOHO_INVOICES){
            $response = $this->emailInvoice($invoice->getZohoInvoiceId());
            if (!$response) {
                // failed to create contact - exit
                $this->logger->insertAuditLogEntry(self::API_NAME,
                    "Failed to create invoice | on step 4: invoice mail | please check prev log record for details");
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * @param $files
     * Model example: Array<br>
                    (
                        [0] => Array
                            (
                                [file_id] => 68
                                [mab] => 1
                                [bill] => 1
                            )
                            ...
                    )
     * @return bool success|fail
     */
    function invoiceStep5MAB($files) : bool
    {
        // filter get only MAB=1 files
        $mab = array_filter($files, function($v, $k) {
            return $v['mab'] == 1;
        }, ARRAY_FILTER_USE_BOTH);

        // get file ids only
        $mabIds = array_column($mab, "file_id");

        // implode to string
        $mabIdsStr = implode(",", $mabIds);
        return $this->zohoGateway->markAsBilled($mabIdsStr, 1); // reversible

    }


    // ===================== Bill Functions =========================== //

    /** Create Bill from typists billing reports page request
     * <br>
     *  <b>Step 1:</b> check for user zoho_id if not exist -> createZohoContact (type: vendor) -> save zoho_id locally
     *  <br><b>Step 2:</b>
     *  <br><b>Step x:</b>
     */
    function generateBill($billData)
    {
        // Step 1 - contact check
//        $orgId = $invoiceData['org_id'];
//        $orgName = $invoiceData['organization'];
//        $contactType = $invoiceData['contact_type'];
//        $uid = $billData['email'];
        $email = $billData['email'];
        $typist = User::withEmail($email, $this->db);
//        return $this->common->generatePHPArrayResponse("Failed to create zoho invoice. (3-PDF)",true, 3);

//        return false;

        $zohoUser = $this->billStep1Contact($typist->getId(), $typist, $billData['contact_type']);
        if(!$zohoUser){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho contact. (1-user-vendor)",true, 1);
        }
//        echo $zohoUser->toString();
        // create bill
        $zohoBill = $this->billStep2Bill($billData, $zohoUser);
        if(!$zohoBill){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho bill. (2-Bill)",true, 2);
        }

//        return;

        /*
        // attach pdf to invoice
        $attached = $this->billStep3AttachPdf($zohoInvoice, $zohoUser);
        if(!$attached){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho bill. (3-Bill-PDF)",true, 3);
        }*/

        // send invoice mail
        $mailed = $this->billStep4SendMail($zohoBill);
        if(!$mailed){
            // failed and logged to actlog | Code is which step has failed
            return $this->common->generatePHPArrayResponse("Failed to create zoho bill. (4-Bill-MAIL)",true, 4);
        }

        // if all good mark as billed/bill
        $extraResponse = '';
        $marked = $this->billStep5MAB($billData['data']);
        if(!$marked){
            // failed and logged to actlog | Code is which step has failed
            $this->logger->insertAuditLogEntry(self::API_NAME, "Failed to MAB (typist-billing-5) | Bill #".$zohoBill->getBillNumber());
            $extraResponse = ' | Failed to mark files as billed, file ids can be retrieved from ZohoBill record';
//            return $this->common->generatePHPArrayResponse("Failed to create zoho bill. (5-Bill-MAIL)",true, 4);
        }


        $this->logger->insertAuditLogEntry(self::API_NAME, "#".$zohoBill->getBillNumber()." Bill Successfully Created by " . $_SESSION['uEmail']);
        return $this->common->generatePHPArrayResponse("Bill Successfully Created | Bill #" . $zohoBill->getBillNumber().$extraResponse);
    }

    function billStep1Contact($uid, User $primaryUser, $contactType) : ZohoUser|bool
    {
        $zohoUser = ZohoUser::typistVendorWithUid($uid, $this->db);
        if(!$zohoUser)
        {
            // Get Client Admin of Org
            // create contact
            $data = array(
                "fail_msg" => "Failed to create bill",
                "fail_ref" => "uid",
//                "aid" => $primaryUser->getAccount(),
                "uid" => $primaryUser->getId(),
                "zipcode" => $primaryUser->getZipcode(),
                "acc_name" => $primaryUser->getFullName(), // zoho contact name
                "state"=> $primaryUser->getState(),
                "country"=> $primaryUser->getCountry(),
                "city"=> $primaryUser->getCity(),
                "address"=> $primaryUser->getAddress(),
                "first_name"=> $primaryUser->getFirstName(),
                "last_name"=> $primaryUser->getLastName(),
                "name"=> $primaryUser->getFullName(),
                "email"=> $primaryUser->getEmail(),
//                "client_admins" => $this->zohoGateway->findSystemAdmins()
                "client_admins" => array(
                        0 => array(
                            "first_name" => $primaryUser->getFirstName(),
                            "last_name" => $primaryUser->getLastName(),
                            "email" => $primaryUser->getEmail(),
                            'is_primary_contact' => true // main client admin (who created the org)
                        )
                    )
                );


            $response = $this->createContact($data, $contactType);
            if(!$response)
            {
                // failed to create contact/vendor - exit
                $this->logger->insertAuditLogEntry(self::API_NAME,
                    "Failed to create bill | on step 1: first time zoho contact/vendor creation | please check prev log record for details");
                return false;
            }
            // else
            // save zoho ids of contact to db

            // client admin (primary)
            $zohoPrimaryUser = new ZohoUser(
                    id: 0,
                    zoho_id: $response->contact->contact_persons[0]->contact_person_id,
                    zoho_contact_id: $response->contact->contact_id,
                    uid: $primaryUser->getId(),
                    acc_id: null,
                    type: ROLES::TYPIST,
                    primary_contact: 1,
                    user_data: json_encode($response),
                    db: $this->db
                );
            $zohoPrimaryUser->save();

            return $zohoPrimaryUser;


        }else{
            return $zohoUser;
        }
    }
    function billStep2Bill($billData, ZohoUser $zohoPrimaryUser) : ZohoBill|bool
    {
        $data = array (
            "fail_msg" => "Failed to create bill",
            "fail_ref" => "step2",
            "zoho_contact_id" => $zohoPrimaryUser->getZohoContactId(),
            "uid" => $zohoPrimaryUser->getUid(),
            "quantity" => $billData['quantity'], // always 1 for now
            "total_payment" => $billData['total_payment'] // hardcoded in rate for now
//            "attachment" => $billData['attachment']
        );
        $response = $this->createBill($data);
        if(!$response)
        {
            // failed to create contact - exit
            $this->logger->insertAuditLogEntry(self::API_NAME,
                "Failed to create bill | on step 2: bill creation | please check prev log record for details");
            return false;
        }
        // else
        // save invoice id
//        $responseArr = json_decode($response, true);

        $bill = new ZohoBill(
            id: 0,
            bill_number: $response->bill->bill_number,
            zoho_contact_id: $zohoPrimaryUser->getZohoContactId(),
            zoho_bill_id: $response->bill->bill_id,
            local_bill_data: json_encode($billData),
            zoho_bill_data: json_encode($response),
            db: $this->db
        );
        $bill->save();

        return $bill;
    }
    function billStep3AttachPdf(ZohoBill $bill) : bool
    {

        $file_tmp = $_FILES['pdf']['tmp_name'];
        $targetDir = "../../data/bills/";
        if(!is_dir($targetDir))
        {
            mkdir($targetDir, 0777, true);
        }
        $pdfTarget = $targetDir . $_POST['pdfName']. time() . ".pdf";
        move_uploaded_file($file_tmp, $pdfTarget);
//        unlink($file_tmp); // delete uploaded tmp file


        $data = array(
            "fail_msg" => "Failed to attach file to invoice",
            "fail_ref" => $bill->getZohoBillId() . " with " . $pdfTarget,
            "bill_id" => $bill->getZohoBillId(),
            "file_path" => $pdfTarget
//            "file_path" => "C:/Users/Hossam/Downloads/Documents/newattach.pdf"
        );

        $response = $this->attachToBill($data);
        if(!$response)
        {
            // failed to create contact - exit
            $this->logger->insertAuditLogEntry(self::API_NAME,
                "Failed to create bill | on step 3: bill pdf attach | please check prev log record for details");
            return false;
        }

        unlink($pdfTarget); // delete file from server

        return true;
    }
    function billStep4SendMail(ZohoBill $bill) : bool
    {

        if(ENV::AUTO_EMAIL_ZOHO_BILLS){
            $response = $this->emailBill($bill->getZohoBillId());
            if (!$response) {
                // failed to create contact - exit
                $this->logger->insertAuditLogEntry(self::API_NAME,
                    "Failed to create bill | on step 4: bill mail | please check prev log record for details");
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * @param $files
     * Model example: Array<br>
                    (
                        [0] => Array
                            (
                                [file_id] => 68
                                [mab] => 1
                                [bill] => 1
                            )
                            ...
                    )
     * @return bool success|fail
     */
    function billStep5MAB($files) : bool
    {
        // filter get only MAB=1 files
        $mab = array_filter($files, function($v, $k) {
            return $v['mab'] == 1;
        }, ARRAY_FILTER_USE_BOTH);

        // get file ids only
        $mabIds = array_column($mab, "file_id");

        // implode to string
        $mabIdsStr = implode(",", $mabIds);
        return $this->zohoGateway->markAsTypistBilled($mabIdsStr, 1); // reversible

    }

}