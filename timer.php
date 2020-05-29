<?php

$FILE = "https://dx13.army.jp/todo.txt";
$test = json_decode(file_get_contents($FILE));
$today = date("H:i");

$dateTime1 = date("H:i");
$objDatetime1 = new DateTime($dateTime1);

for ($i = 0; $i < count($test); $i++) {
    echo $test[$i][2]."<br>";
    //echo isset($test[$i][2])."<br>";
    if($test[$i][2]){
        $dateTime2 = $test[$i][2];
        $objDatetime2 = new DateTime($dateTime2);
        $objInterval = $objDatetime1->diff($objDatetime2);
        $sa = $objInterval->format('%H%I');
        $plus = $objInterval->format('%R');
        echo($sa.$plus);
        if ($plus="+"){
            if($sa<=15){
                Teams(sprintf($test[$i][1]."の予定まで、あと%02d分です", $sa));
            }
        }
    }else{
        echo "false";
    }
}

function Teams($messege){
    $webhook_url = 'https://outlook.office.com/webhook/cba76fbf-f3d0-4a8f-84d0-ece26dcd27ed@bdda4ca5-4ffd-4f47-9e0d-ce56ad194b37/IncomingWebhook/43a04d2336a8404bb97eb4ddabd59476/0b8000aa-2c5c-4023-8572-43d12a42d3bf';
    $options = [
        'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode([
        'text' => $messege
        ]),
        ]
    ];
    file_get_contents($webhook_url, false, stream_context_create($options));
}

?>