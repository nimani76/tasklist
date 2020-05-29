<?php
function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$FILE = 'todo.txt';
$id = uniqid(); 

$DATA = []; 
$BOARD = []; //全ての投稿の情報を入れる

if(file_exists($FILE)) {
    $BOARD = json_decode(file_get_contents($FILE));
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //$_POSTはHTTPリクエストで渡された値を取得する
    //リクエストパラメーターが空でなければ
    if(!empty($_POST['txt'])){
        //投稿ボタンが押された場合
        //$textに送信されたテキストを代入
        $text = $_POST['txt'];
        $tim = $_POST['tim'];
        //新規データ
        $DATA = [$id, $text, $tim];
        $BOARD[] = $DATA;
        $webhook_url = 'https://outlook.office.com/webhook/cba76fbf-f3d0-4a8f-84d0-ece26dcd27ed@bdda4ca5-4ffd-4f47-9e0d-ce56ad194b37/IncomingWebhook/43a04d2336a8404bb97eb4ddabd59476/0b8000aa-2c5c-4023-8572-43d12a42d3bf';
        $options = [
            'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode([
            'text' => $DATA[2].'時に'.$DATA[1].'の予定が入っています'
            ]),
            ]
        ];
        file_get_contents($webhook_url, false, stream_context_create($options));
        file_put_contents($FILE, json_encode($BOARD));

    }else if(isset($_POST['del'])){
        //削除ボタンが押された場合
        //新しい全体配列を作る
        $NEWBOARD = [];

        foreach($BOARD as $DATA){
            if($DATA[0] !== $_POST['del']){
                $NEWBOARD[] = $DATA;
            }
        }
        //全体配列をファイルに保存する
        file_put_contents($FILE, json_encode($NEWBOARD));
    }
    //Webページを更新）
    header('Location: '.$_SERVER['SCRIPT_NAME']);
    //プログラム終了
    exit;
}
?>

<!DOCTYPE html>
<html lang= "ja">
<head>
    <meta name= "viewport" content= "width=device-width, initial-scale= 1.0">
    <meta http-equiv= "content-type" charset= "utf-8">
    <link rel="stylesheet" type="text/css" href="./todo.css" media="all">
    <title>予定登録</title>
</head>
<body>
    <h1>予定登録</h1>

    <section class= "main">
        <!--投稿-->
        <form method= "post">
            <input type= "text" name= "txt">
            <input type= "time" name= "tim">
            <input type= "submit" value= "投稿">
        </form>    
        <table style= "border-collapse: collapse">
        <!--tableの中でtr部分をループ-->
        <?php foreach((array)$BOARD as $DATA): ?>
        <tr>
        <form method= "post">
            <td>
                <!--テキスト-->
                <?php 
                echo h($DATA[1]." ".$DATA[2]); 
                ?>
            </td>
            <td>
                <!--削除-->
                <input type= "hidden" name= "del" value= "<?php echo $DATA[0]; ?>">
                <input type= "submit" value= "削除">
            </td>
        </form>
        </tr>
        <?php endforeach; ?>
        </table>
    </section>