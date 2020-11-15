<?php
    if(!isset($_SESSION[SI]['user'])){
        echo '<script>location = "'.SELF_DIR.'login";</script>';
        die("only login can access this page");
    }
    elseif(!isset($_SESSION[SI]['user']['account_role_fk'])){
        die("unknown role");
    }
    else{}
    global $addedMetas;
    $addedMetas = "";
    $addedMetas .= '<script src="//cdn.ckeditor.com/4.11.3/full/ckeditor.js"></script>';
    $addedMetas .= '<script> BASE_DIR = "'.SELF_DIR.URI1.'"; </script>';
    //$addedMetas .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>';
    $addedMetas .= '<link href="'.SELF_DIR.'Assets/System/dashboard.css" rel="stylesheet">';
    $addedMetas .= '<link href="'.SELF_DIR.'Assets/Resources/dashboard.css" rel="stylesheet">';
    
    global $sideNav;
    $sideNav = [];
    if(isset($_SESSION[SI]['user']) && $_SESSION[SI]['user']['id'] <= 1){
        $sideNav["SuperAdmin"] = [];
        array_push($sideNav["SuperAdmin"],["name"=>"sql","fct"=>"sql","fa"=>"lock"]);
    }
    $sideNav["Dashboard"] = [];
    if($_SESSION[SI]['user']['account_role_fk'] <= 2){
        array_push($sideNav["Dashboard"],["name"=>"account","fct"=>"DBView/account","fa"=>"lock"]);
        array_push($sideNav["Dashboard"],["name"=>"contact_us","fct"=>"DBView/contact_us","fa"=>"lock"]);
        //array_push($sideNav["Dashboard"],["name"=>"project_status","fct"=>"DBView/project_status","fa"=>"lock"]);
        array_push($sideNav["Dashboard"],["name"=>"project_type","fct"=>"DBView/project_type","fa"=>"lock"]);
        array_push($sideNav["Dashboard"],["name"=>"projects","fct"=>"DBView/project","fa"=>"lock"]);
        //array_push($sideNav["Dashboard"],["name"=>"project_users","fct"=>"DBView/project_users","fa"=>"lock"]);
        //array_push($sideNav["Dashboard"],["name"=>"project_log","fct"=>"DBView/project_log","fa"=>"lock"]);
        //array_push($sideNav["Dashboard"],["name"=>"calendar events","fct"=>"DBView/calender_event","fa"=>"lock"]);
    }
    
    //array_push($sideNav["Dashboard"],["name"=>"change password","fct"=>"change_password","fa"=>"lock"]);
    
    array_push($sideNav["Dashboard"],["name"=>"my calandar","fct"=>"my_calandar","fa"=>"lock"]);
    array_push($sideNav["Dashboard"],["name"=>"my events","fct"=>"my_events","fa"=>"lock"]);
    array_push($sideNav["Dashboard"],["name"=>"global chat","fct"=>"chat","fa"=>"lock"]);
    $sideNav["Projects"] = [];
    array_push($sideNav["Projects"],["name"=>"my projects","fct"=>"my_projects","fa"=>"lock"]);
    array_push($sideNav["Projects"],["name"=>"my projects","fct"=>"my_projects_tabs","fa"=>"lock"]);
    if($_SESSION[SI]['user']['account_role_fk'] <= 4){
        array_push($sideNav["Projects"],["name"=>"add_project","fct"=>"add_project","fa"=>"lock"]);
        array_push($sideNav["Projects"],["name"=>"assign_users","fct"=>"assign_users","fa"=>"lock"]);
    }
    elseif($_SESSION[SI]['user']['account_role_fk'] <= 5){

    }
    $sideNav["WorkSpace"] = [];
    $d = DAL::call_sp("select id,name from project where account_fk_manager = :id 
    or id in (select project_fk from project_users where account_fk = :id) ",[
        ["k"=>"id","v"=>$_SESSION[SI]["user"]["id"]]
    ]);
    foreach($d as $r){
        array_push($sideNav["WorkSpace"],["name"=>$r["name"],"fct"=>"view_project/1/2/".$r["id"],"fa"=>"lock"]);
    }
    WEB::load_view("Main","dashboard");
?>
<script id="removethisalso">$("#MainDahsHeader,#removethisalso").empty();</script>

