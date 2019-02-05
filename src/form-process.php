<?php

class ReCaptchaResponse
{
    public $success;
    public $errorCodes;
}
class ReCaptcha
{
    private static $_signupUrl = "https://www.google.com/recaptcha/admin";
    private static $_siteVerifyUrl =
        "https://www.google.com/recaptcha/api/siteverify?";
    private $_secret;
    private static $_version = "php_1.0";
    /**
     * Constructor.
     *
     * @param string $secret shared secret between site and ReCAPTCHA server.
     */
    function ReCaptcha($secret)
    {
        if ($secret == null || $secret == "") {
            die("To use reCAPTCHA you must get an API key from <a href='"
                . self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
        }
        $this->_secret=$secret;
    }
    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data array of string elements to be encoded.
     *
     * @return string - encoded request.
     */
    private function _encodeQS($data)
    {
        $req = "";
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }
        // Cut the last '&'
        $req=substr($req, 0, strlen($req)-1);
        return $req;
    }
    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     *
     * @return array response
     */
    private function _submitHTTPGet($path, $data)
    {
        $req = $this->_encodeQS($data);
        $response = file_get_contents($path . $req);
        return $response;
    }
    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $remoteIp   IP address of end user.
     * @param string $response   response string from recaptcha verification.
     *
     * @return ReCaptchaResponse
     */
    public function verifyResponse($remoteIp, $response)
    {
        // Discard empty solution submissions
        if ($response == null || strlen($response) == 0) {
            $recaptchaResponse = new ReCaptchaResponse();
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = 'missing-input';
            return $recaptchaResponse;
        }
        $getResponse = $this->_submitHttpGet(
            self::$_siteVerifyUrl,
            array (
                'secret' => $this->_secret,
                'remoteip' => $remoteIp,
                'v' => self::$_version,
                'response' => $response
            )
        );
        $answers = json_decode($getResponse, true);
        $recaptchaResponse = new ReCaptchaResponse();
        if (trim($answers ['success']) == true) {
            $recaptchaResponse->success = true;
        } else {
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = $answers [error-codes];
        }
        return $recaptchaResponse;
    }
}

$errorMSG = "";

// NAME
if (empty($_POST["name"])) {
    $errorMSG = "Введите имя ";
} else {
    $name = $_POST["name"];
}

// EMAIL
if (empty($_POST["email"])) {
    $errorMSG .= "Введите email ";
} else {
    $email = $_POST["email"];
}

// MESSAGE
if (empty($_POST["message"])) {
    $errorMSG .= "Введите текст вопроса ";
} else {
    $message = $_POST["message"];
}

if (empty($_POST["g-recaptcha-response"])) {
    $errorMSG .= "Заполните reCAPTCHA ";
}

$secret = '6LftGI8UAAAAAHisBi93QzVD_ZKZd-OsbesMP1GJ';
$recaptcha = $_POST["g-recaptcha-response"];
$response = null;
$reCaptcha = new ReCaptcha($secret);

$url = 'https://www.google.com/recaptcha/api/siteverify';
$key = '6LftGI8UAAAAAHisBi93QzVD_ZKZd-OsbesMP1GJ';
$query = $url . '?secret=' . $key . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

$data = json_decode(file_get_contents($query));

if ($data->success == false) {
    $errorMSG .= "Ошибка reCAPTCHA ";
    exit('Капча введена неверно');
}

$EmailTo = "yopii.main@gmail.com";
$Subject = "Вопрос c abitur.ictis.sfedu.ru";

// prepare email body text
$Body = "";
$Body .= "ФИО: ";
$Body .= $name;
$Body .= "\n";
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";
$Body .= "Вопрос: ";
$Body .= $message;
$Body .= "\n";

// send email
$success1 = mail($EmailTo, $Subject, $Body, "From:" . " abitur.ictis.sfedu.ru");

// redirect to success page
if ($success1 && $errorMSG == "") {
    echo "success";
} else {
    if ($errorMSG == "") {
        echo "Что-то пошло не так :(";
    } else {
        echo $errorMSG;
    }
}
?>