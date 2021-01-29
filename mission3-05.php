<?php   
        //ファイルに書き込む際の処理 既存の書き込みを編集する時とそうで無い時とで場合分けをする
        if(!empty($_POST["comment"])) {
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date = date("Y/m/d/ H:i:s");
            $pwd = $_POST["pwd"];
            $filename = "mi3_5_commentsheet.txt";
            $lines = file($filename);
            
            //コメント番号の処理
            if(file_exists($filename)) {
                $lastLine = $lines[count($lines) - 1];
                $num = explode('<>', $lastLine)[0] + 1;
            }
            
            else {
                $num = 1;
            }
        
            $commentdata = $num."<>".$name."<>".$comment."<>".$date."<>".$pwd."<>".PHP_EOL;
            
            //ここから、編集する場合の処理
            if(!empty($_POST["edit_post"])) {
                $file = fopen($filename,"w");
                foreach($lines as $line){
                    $mofu = explode("<>", $line);
                    //edit_postは、edit_numをそのまま代入している値なので、編集したい投稿の番号である
                    if($mofu[0] == $_POST["edit_post"]) {
                        $new_number = $_POST["edit_post"];
                        $newcommentdata = $new_number."<>".$name."<>".$comment."<>".$date.
                        "\t"."編集済み"."<>".$pwd."<>".PHP_EOL;
                        $line = $newcommentdata;
                    }
                    fwrite($file, $line);
                    
                }
                echo "編集しました<br><br>";
                fclose($file);
            }
            //ここから普通の書き込みの場合の処理
            else {
                $file = fopen($filename,"a");
                fwrite($file, $commentdata);
                echo "書き込みました<br><br>";
                fclose($file);
            }
        }
        
        
        
        //削除機能の処理
        elseif(!empty($_POST["del_num"])) {
            $del_num = $_POST["del_num"];
            $filename = "mi3_5_commentsheet.txt";
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            //使うかわからないけど一応違う変数に入れる
            $pwd_lines = file($filename, FILE_IGNORE_NEW_LINES);
            $del_pwd_key = 0; //この値を、パスワード一致の判定に使う　ここで宣言する必要性は微妙
            $file = fopen($filename, "w");
            
            //パスワードが一致する場合、del_pwd_keyを設定して、それがあるか無いかの場合分けを行う
            foreach($pwd_lines as $pwd_line){
                $del_pwd = $_POST["del_pwd"];
                $mofumofu = explode("<>", $pwd_line);
                //削除するコメント番号とパスワードが一致したとき、del_pwd_keyの値を変化させる
                if($mofumofu[0] == $del_num && $mofumofu[4] == $del_pwd){
                    $del_pwd_key = 1;
                    echo "パスワード一致しました<br><br>";
                }
            }
            
            //パスワードが一致したとき、削除対象の行以外を書き込む処理
            if($del_pwd_key == 1){ //issetを使ってもいいかなと思った
                foreach($lines as $line){
                    $mofu = explode("<>", $line);
                    if($mofu[0] != $del_num) {
                    fwrite($file, $line.PHP_EOL);
                    }
                }
                echo "削除しました<br><br>";
            
            // パスワードが一致しなかったとき、全ての行を書き込む   
            }else{
                foreach($lines as $line){
                    fwrite($file, $line.PHP_EOL);
                }
                echo "削除できませんでした<br><br>";
            }
            fclose($file);
        }
        
        //編集機能の処理
        elseif(isset($_POST["edit_num"])) {
            //echo "編集モードです<br><br><hr>";
            $edit_pwd = $_POST["edit_pwd"];
            $edit_num = $_POST["edit_num"];
            $filename = "mi3_5_commentsheet.txt";
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            foreach($lines as $line) {
                $mofu = explode("<>", $line);
                //編集したい番号の投稿があって、そのパスワードが一致している時
                if($mofu[0] == $edit_num && $mofu[4] == $edit_pwd){
                    echo "編集モードです<br><br><hr>";
                    $edit_name = $mofu[1];
                    $edit_comment = $mofu[2];
                    //hidden postの値をここで設定する edit_numと同じ値にする
                    $edit_key = $edit_num;
                    break;
                }
                //編集したい番号の投稿があるが、パスワードが違う時
                elseif($mofu [0] == $edit_num){
                    echo "パスワードが違います<br><br><hr>";
                }
            }
        }
        
        //表示プログラム
        if(file_exists($filename)) {
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            foreach($lines as $line) {
                $mofu = explode("<>", $line);
                echo $mofu[0]."\t".$mofu[1]."\t".$mofu[2]."\t".$mofu[3]."<br>";
            }
        }
        
        //何もしていなくても、投稿は最初から表示されていた方がいいよね
        if(empty($_POST["comment"]) && empty($_POST["del_num"]) && empty($_POST["edit_num"])){
            $filename = "mi3_5_commentsheet.txt";
            if(file_exists($filename)) {
                $lines = file($filename, FILE_IGNORE_NEW_LINES);
                foreach($lines as $line) {
                    $mofu = explode("<>", $line);
                    echo $mofu[0]."\t".$mofu[1]."\t".$mofu[2]."\t".$mofu[3]."<br>";
                }
            }
        }
        
        ?>



<DOCTYPE html>
<html>
    <head>
        <meta charset = "utf-8">
        <title>mission_3-05</title>
    </head>
    
    <body>
        <form action = "" method = "post">
            
            <!-- 編集番号を記録する処理 -->
            <input type = "hidden" name = "edit_post" value = <?php if(isset($edit_key))
            {echo $edit_key;}?>>

            <p>お名前</p>
            <input type = "text" name = "name" value = <?php if(isset($edit_name))
            {echo $edit_name;} else{echo "名無し";}?>>
            
            <p>コメント</p>
            <input type = "text" name = "comment" placeholder = "コメントを入力してください" 
            value = <?php if(isset($edit_comment)){echo $edit_comment;}?>>
            
            <p>パスワード</p>
            <input type = "text" name = "pwd">
            
            <input type = "submit" name = "submit" value = "送信">
        </form>
        
        
        <form action = "" method = "post">
            <p>削除対象番号</p>
            <input type = "number" name = "del_num">
            <p>パスワード</p>
            <input type = "text" name = "del_pwd">
            <input type = "submit" name = "submit_del" value = "削除">
        </form>
        
        <form action = "" method ="post">
            <p>編集対象番号</p>
            <input type = "number" name = "edit_num">
            <p>パスワード</p>
            <input type = "text" name = "edit_pwd">
            <input type = "submit" name = "submit_edit" value = "編集">
            
        </form>
        
    </body>
</html>