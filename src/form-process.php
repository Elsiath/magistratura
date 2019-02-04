<?php

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
$success = mail($EmailTo, $Subject, $Body, "From:"." abitur.ictis.sfedu.ru");

// redirect to success page
if ($success && $errorMSG == ""){
   echo "success";
}else{
    if($errorMSG == ""){
        echo "Что-то пошло не так :(";
    } else {
        echo $errorMSG;
    }
}

?>