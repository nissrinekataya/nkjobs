<?php
define('MAIN_CTRL','WEB');  require_once("base.php");
class WEB extends PDemiaBaseMVC{
    //CUSTOM WEB PAGES
    public static function home(){
        self::load_view("Main","home");
    }
    public static function login($f = null){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(GORP == "GET"){
            self::load_view("Main","login");
        }
        elseif(isset($f[1]) && $f[1] == "submit"){
            if(!isset($_SESSION[SI]['login_attempt'])) $_SESSION[SI]['login_attempt'] = 1;
            $usr = $_POST['email'];
            $pas = $_POST['password'];
            $d= DAL::call_sp("select * from account where email=:usr or name=:usr",[
                ["k"=>"usr","v"=>$usr]
            ]);
            if(count($d) > 0){
                $d = $d[0];
                if($d["password"] == md5($pas.$d['salt'])){ //validate the hash of the password
                    $_SESSION[SI]['user'] = $d;
                    p('login successful');
                    echo '<script>location="'.SELF_DIR.'admin";</script>';
                }
                else {
                    p($_POST['password']);
                    p($d["password"]);
                    echo '<script>SYS.dialog("wrong password","Error");</script>';
                }
            }
            else echo '<script>SYS.dialog("'.$usr.' is not registered","Error");</script>';
        }
    }
    public static function register(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(GORP == "GET"){
            self::load_view("Main","register");
        }
        elseif(isset($f[1]) && $f[1] == "submit"){
            $d = $_POST;
            p($d);
            die();//
            if($table == "account"){
                $exist = DAL::call_sp("select count(*) exist from account where email=:email",[
                    ["k"=>"email","v"=>$d['email']]
                ]);
                if($exist[0]['exist'] > 0){
                    die("email used by another account use to login");
                }
                $time = time();
                $d['salt'] = md5($time);
                $d['password'] = md5($d['password'].$d['salt']);
                $r = DAL::insert($table,$d);
                if($f > 0){
                    p("account registered");
                }
                else{
                    p("account not registered");
                }
            }
        }
        else{
            die("unknown request $f");
        }
    }
    public static function profile(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 : 0;
        if(!isset($_SESSION[SI]['user'])) die("need to login");
        
        function my_log($projectId){
            $d = DAL::call_sp("select * from project_log where project_fk=:project_fk and account_fk_logger = :account_fk_logger",[
                ["k"=>"project_fk","v"=>$projectId],
                ["k"=>"account_fk_logger","v"=>$_SESSION[SI]["user"]["id"]]
            ]);
            echo '<div class="container">';
            echo DAL::genViewTable("project_log",[],$d,["active","name"]);
            echo '</div>';
        }
        function project_users_table($projectId){
            $d = DAL::call_sp("select * from project_users where project_fk=:project_fk ",[
                ["k"=>"project_fk","v"=>$projectId]
            ]);
            echo '<div class="container">';
            echo DAL::genViewTable("project_users",[],$d,["active","name","project_fk",""]);
            echo '</div>';
        }
        if(GORP == "GET"){
            self::load_view("Main","profile");
        }
        if(!isset($f[$IX])){}
        elseif($f[$IX]=="assign_users_submit"){
            $d = $_POST;
            unset($d['key']);
            $t = $d['table'];
            unset($d['table']);
            $r = DAL::insert($t,$d);
            if($r > 0){
                p("user added");
                echo '<script>SYS.XHRFct("my_projects","DashContents");</script>';
            }
            else{
                p("user not added");
            }
        }
        elseif($f[$IX]=="assign_users"){
            $html = DAL::getFormForTable("project_users",[],
            ["active","active_approved",""]
            ,"assign_users_submit");
            echo $html;
        }
        elseif($f[$IX]=="view_project"){
            $id = $f[3];
            $_SESSION[SI]["project_id"] = $id;
            echo '<button class="btn btn-success" onclick="SYS.XHRFct(\'log_work/'.$id.'\',\'DashContents\');" >Log work</button>';
            echo '<h1>My Log</h1>';
            my_log($id);
            echo '<h1>Users in project</h1>';
            project_users_table($id);
        }
        elseif($f[$IX] == "my_projects_tabs"){
            $d = DAL::call_sp("select project.id, project.name as name, account.name as manager, project_type.name as type, project_status.name as status from project, account, project_type, project_status WHERE
            project.account_fk_manager = account.id AND
            project.project_type_fk = project_type.id AND
            project.project_status_fk = project_status.id
            and (account_fk_manager = :id 
            or project.id in (select project_fk from project_users where account_fk = :id) )",[
                ["k"=>"id","v"=>$_SESSION[SI]["user"]["id"]]
            ]);

            foreach($d as $p){
                echo '<div class="container project_container">';
                    echo '<h2>'.$p["name"].'</h2>';
                    echo '<p>'.$p["manager"].'</p>';
                    echo '<p>'.$p["type"].'</p>';
                    echo '<p>'.$p["status"].'</p>';
                    if($p["manager"] == $_SESSION[SI]["user"]["name"]){
                        echo '<p><button class="btn btn-warning">edit</button></p>';
                        echo '<p><button class="btn btn-danger">delete</button></p>';
                    }
                echo '</div>';
            }

        }
        elseif($f[$IX]=="log_work_submit"){
            $d = $_POST;
            $d["account_fk_logger"] = $_SESSION[SI]["user"]["id"];
            $d["project_fk"] = $_SESSION[SI]["project_id"];
            $d["active_approved"] = "0";
            unset($d['key']);
            $t = $d['table'];
            unset($d['table']);
            $r = DAL::insert($t,$d);
            if($r > 0){
                p("log added");
                echo '<script>SYS.XHRFct("view_project/1/2/'.$d["project_fk"].'","DashContents");</script>';
            }
            else{
                p("log not added");
            }
        }
        elseif($f[$IX]=="log_work"){
            $id = $f[$IX+1];
            $_SESSION[SI]["project_id"] = $id;
            $html = DAL::getFormForTable("project_log",[],
            ["name","active","active_approved", "project_fk","account_fk_logger"]
            ,"log_work_submit");
            echo $html;
        }
        elseif($f[$IX]=="my_projects"){
            $d = DAL::call_sp("select * from project where account_fk_manager = :id 
            or id in (select project_fk from project_users where account_fk = :id) ",[
                ["k"=>"id","v"=>$_SESSION[SI]["user"]["id"]]
            ]);
            $op = ["view"=>"view_project"];
            $html = DAL::genViewTable("project",$op,$d,[]);
            echo $html;
        }
        elseif($f[$IX] == "add_project"){
            $html = DAL::getFormForTable("project",[],
            ["active","account_fk_manager"]
            ,"add_project_submit");
            echo $html;
        }
        elseif($f[$IX] == "change_password"){
            p("change_password");
        }
        elseif($f[$IX] == "add_project_submit"){
            $d = $_POST;
            $d["account_fk_manager"] = $_SESSION[SI]["user"]["id"];
            unset($d['key']);
            $t = $d['table'];
            unset($d['table']);
            $r = DAL::insert($t,$d);
            if($r > 0){
                p("project added");
                echo '<script>SYS.XHRFct("my_projects","DashContents");</script>';
            }
            else{
                p("project not added");
            }

        }
        elseif($f[$IX] == "my_calandar"){
            WEB::load_view("Part","calandar");
        }
        elseif($f[$IX] == "delete_event"){
            $data = ["active"=>0];
            $r = DAL::update("calender_event",$data, $f[$IX+2],$f[$IX+3]);
            echo 'ok';
            echo '<script><script>';
            // p($r);
            // if($r > -1){
            //     p("event removed");
            // }
            // else{
            //     p("could not remove event");
            // }
        }
        elseif($f[$IX] == "my_events"){
            $d = DAL::call_sp("select id, name as title, date_start as start, date_end as end from calender_event
            where account_fk = :account_fk and active = 1
            ",[["k"=>"account_fk","v"=>$_SESSION[SI]["user"]["id"]]]);
            $op = ["delete"=>"delete_event"];
            $html = DAL::genViewTable("project",$op,$d,[]);
            echo $html;
        }
        elseif($f[$IX] == "add_event"){
            $d = $_POST;
            $data = [
                "name"=>$d["title"],
                "date_start"=>$d["start"],
                "date_end"=>$d["end"],
                "account_fk"=>$_SESSION[SI]["user"]["id"]
            ];
            $r = DAL::insert("calender_event",$data);
            if($r > -1){
                echo "event added";
            }
            else{
                echo "event not saved to database";
            }
        }
        else{
            include(join(DIRECTORY_SEPARATOR, array(Controllers,"admin.php" )));
            $m  = $f[$IX];
            if(method_exists(new Admin(),$m)){
                Admin::$m();
            }
            else{
                p("unkonw key ");p($f[$IX]);
            }
        }
    }
    public static function admin(){
        self::profile();
    }
}
?>