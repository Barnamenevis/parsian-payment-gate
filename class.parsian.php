<?php

class bnBankParsian
{

    protected
     $Authority =    0
    ,$wsdl =    "https://pec.shaparak.ir/pecpaymentgateway/eshopservice.asmx?wsdl"  // set the URL or path to the WSDL document

    ,$paymentPage =  "https://pec.shaparak.ir/pecpaymentgateway"
    ,$callBackUrl
    ,$pin

    ;


    public function __construct($pin,$callBackUrl) 
    {

        $this->pin         = $pin;
        $this->callBackUrl = $callBackUrl;
    }
    public function Step1_PinPaymentRequest(int $amount, int $orderID,string $pin, string $returnPage) 
    {

        try
        {
            $soap = new nusoap_client($this->wsdl,"wsdl");
        }
        catch(Exception $e)
        {
            return $e;
        }
        // get the SOAP proxy object, which allows you to call the methods directly
        $proxy = $soap->getProxy();


        $parameters = array(
            'pin'          => $this->pin
            ,'amount'       => $amount
            ,'orderId'      => $orderID
            ,'callbackUrl'  => $returnPage
            ,'authority'    => 0
            ,'status'       => 0
        );

        $result =  $proxy->PinPaymentRequest($parameters);



    }

    protected function Step2_GoToPaymentPage($parsianResult)
    {

        if($parsianResult['status'] == 0)
        {
            header('location: ' . $this->$paymentPage . $parsianResult['authority']) ;
        }
        else
        {
            return $this->bankMessage($parsianResult['status']);
        }
    }
    public function Step3_PinPaymentEnquiry($authorityCode) 
    {


        try
        {
            $soap = new nusoap_client($this->wsdl,"wsdl");
        }
        catch(Exception $e)
        {
            return $e;
        }


        $proxy = $soap->getProxy();

        // set parameter parameters (PinPaymentEnquiry^)
        $parameters = array(
            'pin'          => $this->pin
            ,'authority'    => $authorityCode
            ,'status'       => 0
        );

        // get the result, a native PHP type, such as an array or string

        return $proxy->PinPaymentEnquiry($parameters);


    }




    public function PinPaymentEnquiry($authorityCode, $status )
    {

       


        // instantiate the SOAP client object
        $soap = new soapclient($this->wsdl,"wsdl");

        // get the SOAP proxy object, which allows you to call the methods directly
        $proxy = $soap->getProxy();

        // set parameter parameters (PinPaymentEnquiry^)
        $parameters = array(pin=>$this->pin,authority=>$authorityCode,status=>$status);

        // get the result, a native PHP type, such as an array or string
        $result = $proxy->PinPaymentEnquiry($parameters);
        return $result;



    }
    public function bankMessage($messageID)
    {
        switch($messageID)
        {
            case '0':
                return array(
                    "Persian" => 'پرداخت موفقیت آمیز'
                    ,"English" => "Success" 
                );
                break;

            case '1':
                return array(
                    "Persian" => 'پرداخت موفقیت آمیز'
                    ,"English" => "Success" 
                );
                break;

            default:
                return array(
                    "Persian" => "کد ناشناخته : $messageID "
                    ,"English" => "UnKnown Code : $messageID "
                );
                break;

        }   



    }

}

?>

