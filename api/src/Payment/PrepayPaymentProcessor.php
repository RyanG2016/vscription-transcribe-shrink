<?php


namespace Src\Payment;

//require '../../../api/vendor/autoload.php';
require __DIR__.'/../../../api/bootstrap.php';

use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Src\Enums\PAYMENT_STATUS;
use Src\Models\Package;
use Src\Models\User;
use Src\System\Mailer;
use Src\TableGateways\paymentGateway;
use Src\Models\Payment;
use Src\Helpers\common;

define("AUTHORIZENET_LOG_FILE", __DIR__."/authorizeNet.log");

class PrepayPaymentProcessor
{

    private $paymentGateway;
    private $internalRefID;
    private $common;
    private $totalPrice;
    private $taxesArr = array();
    private User $userModel;
    private $mailer;

    public function __construct(
        private $fname,
        private $lname,
        // private $address,
        // private $city,
        // private $state,
        // private $country,
        private $zip,

        private $nameOnCard,
        private $cardNumber,
        private $cardCvv,
        private $cardExpiryMMSlYY,
        private $amount,

        private Package $package,
        private $selfAccount,

        private $db
    )
    {
        $this->cardNumber = str_replace(" ", "", $this->cardNumber);
        $this->paymentGateway = new paymentGateway($db);
        $this->common = new common();
        $this->totalPrice = $this->amount;
        $this->internalRefID = $this->common->generateUniqueRefID($_SESSION["uid"] . "_");
        $this->mailer = new Mailer($this->db);
    }


//define("AUTHORIZENET_LOG_FILE", "phplog");

    /**
     * formats MM/YY date to YYYY-MM for Authorize.Net specs
     * @param $MMSlYY string MM/YY date
     * @return string YYYY-MM
     */
    private function formatExpiryDate($MMSlYY)
    {
        $arr = explode("/", $MMSlYY);
        return "20" . $arr[1] . "-" . $arr[0];
    }

    public function saveUserAddress()
    {
        $user = User::withID($_SESSION["uid"], $this->db);
        $this->userModel = $user;
        // $user->setAddress($this->address);
//        $user->setFirstName($this->fname);
//        $user->setLastName($this->lname);
        // $user->setAddress($this->address);
        // $user->setCity($this->city);
        // $user->setState($this->state);
        $user->setZipcode($this->zip);
        // $user->setCountry($this->country);
        // $user->setCardNumber($this->cardNumber);
        // $user->setExpirationDate($this->cardExpiryMMSlYY);

        $user->save();


    }

    public function chargeCreditCardNow():bool
    {
        $this->calculateTotalPrice();

        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(getenv('AUTHNET_API_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(getenv('AUTHNET_TRANS_KEY'));

        // Set the transaction's refId
        $refId = $this->internalRefID;

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($this->cardNumber);
        $creditCard->setExpirationDate($this->formatExpiryDate($this->cardExpiryMMSlYY));
        $creditCard->setCardCode($this->cardCvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($refId);
        $order->setDescription($this->package->getSrpName() . " SR Package");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        // $customerAddress->setFirstName($this->fname);
        // $customerAddress->setLastName($this->lname);
//        $customerAddress->setCompany("Souveniropolis");
        // $customerAddress->setAddress($this->address);
        // $customerAddress->setCity($this->city);
        // $customerAddress->setState($this->state);
        $customerAddress->setZip($this->zip);
        // $customerAddress->setCountry($this->country);

        // Set the customer's identifying information
//    $customerData = new AnetAPI\CustomerDataType();
//    $customerData->setType("individual");
//    $customerData->setId("99999456654");
//    $customerData->setEmail("EllenJohnson@example.com");

        // Add values for transaction settings
//    $duplicateWindowSetting = new AnetAPI\SettingType();
//    $duplicateWindowSetting->setSettingName("duplicateWindow");
//    $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
//    $merchantDefinedField1 = new AnetAPI\UserFieldType();
//    $merchantDefinedField1->setName("customerLoyaltyNum");
//    $merchantDefinedField1->setValue("1128836273");

//    $merchantDefinedField2 = new AnetAPI\UserFieldType();
//    $merchantDefinedField2->setName("favoriteColor");
//    $merchantDefinedField2->setValue("blue");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($this->totalPrice);
        $transactionRequestType->setCurrencyCode("CAD");
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
//    $transactionRequestType->setCustomer($customerData);
//    $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
//    $transactionRequestType->addToUserFields($merchantDefinedField1);
//    $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);


        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
//                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
//                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
//                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
//                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
//                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";

                    $processResponseResult = $this->processResponse($tresponse->getMessages()[0]->getDescription(),
                        false,
                        $tresponse->getTransId()
                    );

                } else {
//                    echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
//                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
//                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";

                        $processResponseResult = $this->processResponse(
                            $tresponse->getErrors()[0]->getErrorCode()  . ": " .
                            $tresponse->getErrors()[0]->getErrorText()
                            , true);

                    }
                    $processResponseResult = $this->processResponse(
                        "Unknown error occurred", true);
                }
                // Or, print errors if the API request wasn't successful
            } else {
//                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
//                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
//                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    $processResponseResult = $this->processResponse(
                        $tresponse->getErrors()[0]->getErrorCode() . ": " .
                        $tresponse->getErrors()[0]->getErrorText()
                        , true);

                } else {
//                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
//                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                    $processResponseResult = $this->processResponse(
                        $response->getMessages()->getMessage()[0]->getCode() . ": " .
                        $response->getMessages()->getMessage()[0]->getText()
                        , true);
                }
            }
        } else {
//            echo "No response returned \n";
            $processResponseResult = $this->processResponse("Server failed to respond", true);
        }

        return $processResponseResult;
    }

    function processResponse(
        string $msg,
        bool $error = false,
        string $transID = null
    ):bool
    {

        $json = json_encode(array(
            "trans_id" => $transID,
            "ref_id" => $this->internalRefID,
            "taxes" => $this->taxesArr,
            "error" => $error,
            "card" => $this->ccMasking($this->cardNumber, "x"),
            "name" => $this->fname . " " . $this->lname,
            "email" => $this->userModel->getEmail(),
            "total_price" => $this->totalPrice,
            "pkg_name" => $this->package->getSrpName(),
            "pkg_price" => $this->package->getSrpPrice(),
            "pkg_minutes" => $this->package->getSrpMinutes(),
            "acc_name" => $this->selfAccount?$_SESSION["userData"]["admin_acc_name"]:$_SESSION["acc_name"],
            "acc_id" => $this->selfAccount?$_SESSION["userData"]["account"]:$_SESSION["accID"],
            "msg" => $msg
        ));

        $payment = new Payment(
            0,
            $_SESSION["uid"],
            $this->totalPrice,
            $this->internalRefID,
            $transID,
            $json,
            $this->package->getSrpId(),
            $error?PAYMENT_STATUS::FAILED:PAYMENT_STATUS::PAID,
            $this->db
        );
        $pid = $payment->save();

        if (!$error) {
            $this->mailer->sendEmail(17, $this->userModel->getEmail(), "", $pid);
        }

        return $error;
    }

    function ccMasking($number, $maskingCharacter = 'X') {
        return str_repeat($maskingCharacter, strlen($number) - 4) . substr($number, -4);
//        return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
    }

    function calculateTotalPrice()
    {
        $this->totalPrice = $this->amount;
        if(isset($this->zip) && strlen($this->zip) !=0)
        {
            $contents = file_get_contents(__DIR__."/../../../transcribe/data/json/canada_taxes.json");
            $json = json_decode($contents, true);

            $totalTaxePercentage = 0;

            foreach ($json as $caStateEntry) {
                if(strtolower($caStateEntry["code"]) === strtolower(substr($this->zip, 0,1)))
                {
                    $stateTaxProfile = $caStateEntry;

                    foreach ($stateTaxProfile["taxes"] as $tax) {
//                        if($tax["code"] !== "PST")
//                        {
                            $totalTaxePercentage += $tax["tax"];
                            array_push($this->taxesArr, $tax);
//                        }
                    }
                    $this->totalPrice = ($this->amount * $totalTaxePercentage) + $this->amount;

                    break;
                }
            }

        }

        return $this->totalPrice;
    }
    public function createCustomerProfile($email)
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(getenv('AUTHNET_API_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(getenv('AUTHNET_TRANS_KEY'));        
        // Set the transaction's refId
        $refId = $this->internalRefID;


        // Create a Customer Profile Request
        //  1. (Optionally) create a Payment Profile
        //  2. (Optionally) create a Shipping Profile
        //  3. Create a Customer Profile (or specify an existing profile)
        //  4. Submit a CreateCustomerProfile Request
        //  5. Validate Profile ID returned

        // Set credit card information for payment profile
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($this->cardNumber);
        $creditCard->setExpirationDate($this->cardExpiryMMSlYY);
        $creditCard->setCardCode($this->cardCvv);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        // Create the Bill To info for new payment type
        $billTo = new AnetAPI\CustomerAddressType();
        // $billTo->setFirstName("Ellen");
        // $billTo->setLastName("Johnson");
        // $billTo->setCompany("Souveniropolis");
        // $billTo->setAddress("14 Main Street");
        // $billTo->setCity("Pecan Springs");
        // $billTo->setState("TX");
        $billTo->setZip($this->zip);
        // $billTo->setCountry("USA");
        // $billTo->setPhoneNumber("888-888-8888");
        // $billTo->setfaxNumber("999-999-9999");

        // Create a customer shipping address
        $customerShippingAddress = new AnetAPI\CustomerAddressType();
        // $customerShippingAddress->setFirstName("James");
        // $customerShippingAddress->setLastName("White");
        // $customerShippingAddress->setCompany("Addresses R Us");
        // $customerShippingAddress->setAddress(rand() . " North Spring Street");
        // $customerShippingAddress->setCity("Toms River");
        // $customerShippingAddress->setState("NJ");
        $customerShippingAddress->setZip($this->zip);
        // $customerShippingAddress->setCountry("USA");
        // $customerShippingAddress->setPhoneNumber("888-888-8888");
        // $customerShippingAddress->setFaxNumber("999-999-9999");

        // Create an array of any shipping addresses
        $shippingProfiles[] = $customerShippingAddress;


        // Create a new CustomerPaymentProfile object
        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setCustomerType('individual');
        $paymentProfile->setBillTo($billTo);
        $paymentProfile->setPayment($paymentCreditCard);
        $paymentProfiles[] = $paymentProfile;


        // Create a new CustomerProfileType and add the payment profile object
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setDescription("Customer 2 Test PHP");
        $customerProfile->setMerchantCustomerId("M_" . time());
        $customerProfile->setEmail($email);
        $customerProfile->setpaymentProfiles($paymentProfiles);
        $customerProfile->setShipToList($shippingProfiles);


        // Assemble the complete transaction request
        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setProfile($customerProfile);

        // Create the controller and get the response
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
      
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            // echo "Succesfully created customer profile : " . $response->getCustomerProfileId() . "\n";
            // $paymentProfiles = $response->getCustomerPaymentProfileIdList();
            // echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
        } else {
            echo "ERROR :  Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
        return $response;
    }
    public function getCustomerPaymentProfile($customerProfileId="1929905607",$customerPaymentProfileId= "1842074814")
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(getenv('AUTHNET_API_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(getenv('AUTHNET_TRANS_KEY'));        
        // Set the transaction's refId
        $refId = $this->internalRefID;

        //request requires customerProfileId and customerPaymentProfileId
        $request = new AnetAPI\GetCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setCustomerProfileId($customerProfileId);
        $request->setCustomerPaymentProfileId($customerPaymentProfileId);

        $controller = new AnetController\GetCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if(($response != null)){
            if ($response->getMessages()->getResultCode() == "Ok")
            {
                // echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
                // echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
                // echo "Customer Payment Profile Billing Address: " . $response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
                // echo "Customer Payment Profile Card Last 4 " . $response->getPaymentProfile()->getPayment()->getCreditCard()->getCardNumber(). "\n";

                if($response->getPaymentProfile()->getSubscriptionIds() != null) 
                {
                    if($response->getPaymentProfile()->getSubscriptionIds() != null)
                    {

                        echo "List of subscriptions:";
                        foreach($response->getPaymentProfile()->getSubscriptionIds() as $subscriptionid)
                            echo $subscriptionid . "\n";
                    }
                }
            }
            else
            {
                echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
                $errorMessages = $response->getMessages()->getMessage();
                echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
            }
        }
        else{
            echo "NULL Response Error";
        }
        return $response;
    }
}