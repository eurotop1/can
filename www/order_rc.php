<?php
if($_POST['phone']!='') {
    session_start();
//!!!!!    заполнить !!!!!
    //
    //

    $crmDomain = 'https://stereo-mercadomallmx.retailcrm.ru';
    $crmKey = 'KXOfJHvLtPWklhdA8PFOY5ECFzXeq9rB';
    $offerId=105; // внутренний ID торгового предложения
////////////////////////////////////////
///
///
///
///
///


    $banned=0;
    $dublicate=0;
    $srch = array("'", "\"", "_");
    $phone = trim(htmlspecialchars($_POST['phone']));
    $phone = preg_replace('/[^0-9\/-]+/', '', $phone);
    $name = str_replace($srch, "", $_POST['name']);
    $name = trim(htmlspecialchars($name));
    $patronymic = str_replace($srch, "", $_POST['patronymic']);
    $patronymic = trim(htmlspecialchars($patronymic));
	$email = str_replace($srch, "", $_POST['email']);
    $email = trim(htmlspecialchars($email));
	$address = str_replace($srch, "", $_POST['address']);
    $address = trim(htmlspecialchars($address));
    $postData = http_build_query(array(
       'order' => json_encode(array(
            'firstName' => $name,
            'phone' => $phone,
			'patronymic' => $patronymic,
            'source' => array(
                'source' => $_COOKIE['utm_source'],
                'medium' => $_COOKIE['utm_medium'],
                'campaign' => $_COOKIE['utm_campaign'],
                'keyword' => $_COOKIE['utm_term'],
                'content' => $_COOKIE['utm_content']),
                  'items' => array(0 => array('offer' => array('id'=>$offerId))

            )

        )),
        'apiKey' => $crmKey,
    ));

    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postData
        )
    );
    if( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, $crmDomain.'/api/v5/orders?filter[customer]='.$phone.'&apiKey='.$crmKey.'');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);
        $res=json_decode($out, true);
    }

    if($res['success']==1){
        $spam=$res['pagination']['totalCount'];
        if($spam>0){ $dublicate=1; }
    }

    $black=fopen("blacklist.txt","r");
    if ($black) {
        while (($blackPhone = fgets($black)) !== false) {
            $blackPhone=trim($blackPhone);
            if($phone==$blackPhone){
                $banned=1;
                break;
            }
        }
    }
    fclose($black);
    if(!$banned && !$dublicate)
    {
        $context = stream_context_create($opts); 
        $result = json_decode(
            file_get_contents(
                $crmDomain . '/api/v5/orders/create',
                false,
                $context
            ),
            true
        );
    }
    $_SESSION['order-OK'] = $result['id'];
    $_SESSION['order-name'] = $name;

    /*
    $params = array(
        'apiKey' => $crmKey,
    );

    $result = json_decode(
        file_get_contents($crmDomain . '/api/v5/store/products' . '?' . http_build_query($params)),
        true
    );

    echo '<pre>';
        print_r($result);
    echo '<pre>';

    */
  header("Location: success.html");
    exit();
}
?>