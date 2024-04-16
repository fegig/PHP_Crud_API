<?

function checkToken($token): void  {
    $token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));

    //authorized tokens
    $tokenList = array(
        "fa3b2c9c-a96d-48a8-82ad-0cb775dd3e5d" => ""
    );
   

    if (!isset($token)) {
        echo json_encode("No token was received to authorize the operation. Verify the information sent");

        exit;
    }

    if (!isset($tokenList[$token])) {
        echo json_encode("The token  " . $token  . 
        " does not exists or is not authorized to perform this operation.");
        
        exit;
    }
};