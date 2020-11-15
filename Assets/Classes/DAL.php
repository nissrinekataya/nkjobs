<?php
class DAL{ // MJZ last edit 2020-10-13 4 PM / working on idad
    public static $c=null; // to avoid reaching max allowed per hour use
    public static function getDefaultConnection(){
        global $DBOCFG;
        if(isset(DAL::$c) && DAL::$c != null) return DAL::$c;
        DAL::$c = self::getConnection($DBOCFG);
        return DAL::$c;
    }
    public static function getConnection($DBOCFG){
        $db = $DBOCFG['database'];
        $hs = $DBOCFG['host'];
        $us = $DBOCFG['user'];
        $ps = $DBOCFG['password'];
        $DSN = "mysql:host=$hs;dbname=$db";
        $c = new PDO($DSN,$us,$ps,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") 
        );
		$c->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $c;
    }
    public static function call_sp($q,$p = [],$c = null){
        if ($c == null) $c = self::getDefaultConnection();
        $s = $c->prepare($q);
        foreach($p as $a=>$b){
			$s->bindParam(":".$p[$a]['k'],$p[$a]['v']);
        }
        try{
            if($s->execute()){
                return $s->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        catch(Exception $e){
            p($e->getMessage());
            p($c->errorInfo());
            return;
        }
    }
    public static function execute_query($q,$p = [],$c=null){
        if ($c == null) $c = self::getDefaultConnection();
        $s = $c->prepare($q);
        foreach($p as $a=>$b){
            $s->bindParam(":".$p[$a]['k'],$p[$a]['v']);
        }
        $r =  $s->execute();
        if(strpos($q,"INSERT") > -1 && $r > 0){
            return $c->lastInsertId();
        }
        else return $r;
    }
    public static function getTables($c = null){
        $d = self::call_sp('SHOW TABLES;');
		if(count($d) == 0){ return []; }
		$t = [];
		foreach($d[0] as $a=>$b){
			$k = $a;
		}
		for($i =0; $i<count($d);$i++){
			$t[count($t)] = $d[$i][$k];
		}
		return $t;
    }
    public static function table_exist($table){
        $t = trim($table);
        $t = strtolower($t);
        $tables = self::getTables();
        if(in_array($t,$tables)) 
            return true;
        else
            return false;
    }
    public static function TableFields($t,$c=null){
        $d = self::call_sp("DESCRIBE $t");
        $f = [];
        for($i = 0 ; $i < count($d);$i++){
            array_push($f,$d[$i]['Field']);
        }
        return $f;
    }
    public static function insert($table,$data,$c=null,$force = true,$trace = true){
        if ($c == null) $c = self::getDefaultConnection();
        if(count($data)==0) return -1;
        if(self::table_exist($table) == false ) return -2;
        $ks = array();
        foreach($data as $k=>$v){
            array_push($ks,$k);
        }
        $q = "INSERT INTO $table (".implode( ",", $ks).") VALUES(:". implode( ",:", $ks) .");";
        $s = $c->prepare($q);
        foreach($data as $a=>$b){
			$s->bindParam(":".$a,$data[$a]);
        }
        try{
            if($s->execute()){
                return $c->lastInsertId();
            }
        }
        catch(Exception $e){
            p($e->getMessage());
            p($c->errorInfo());
            return -1;
        }
    }
    public static function update($table,$data,$updatekey,$updatekeyvalue,$c=null){
        if ($c == null) $c = self::getDefaultConnection();
        if(count($data)==0) return -1;
        if(self::table_exist($table) == false ) return -1;
        $ks = array();
        $pqs = array();
        foreach($data as $k=>$v){
            array_push($pqs,$k."=:".$k);
            array_push($ks,$k);
        }
        $q = "UPDATE $table SET ".implode( ",", $pqs)." WHERE $updatekey=:$updatekey";
        $s = $c->prepare($q);
        foreach($data as $a=>$b){
            $s->bindParam(":".$a,$data[$a]);
        }
        $s->bindParam(":".$updatekey,$updatekeyvalue);
        try{
            return $s->execute();
        }
        catch(Exception $e){
            p($e->getMessage());
            p($c->errorInfo());
            return -1;
        }
    }
    public static function delete($t,$key,$val,$c=null){
        if ($c == null) $c = self::getDefaultConnection();
        if(self::table_exist($t) == false ) return -1;
        $fs = self::TableFields($t);
        if(!in_array($key,$fs)) return -1;
        try{
            return self::execute_query("DELETE FROM $t WHERE `".$key."` = :i",[
                ["k"=>"i","v"=>$val]
            ]);
        }
        catch(PDOException $e){
            if(ADMIN) {p($e->getFile() . " " . $e->getLine() . " " . $e->getMessage() );}
            else  p('Please Contact Admin');
		}catch(Error $e){
			if(ADMIN) {p($e->getFile() . $e->getLine() . $e->getMessage() );}
            else  p('Please Contact Admin');
		}
    }
    //ADMIN PANEL GENERIC FUNCTIONS
    public static function genViewTable($t,$op = [],$d = null, $hideCols = [], $calc = []){
        if($d === null){
            $d = DAL::call_sp(DAL::getViewQuery($t));
        }
        array_push($hideCols,"password");   // always hide password
        array_push($hideCols,"salt");       // always hide salt
        array_push($hideCols,"active");     // always hide active
        $col_fks = [];
        if($t != "" && $t != "query"){
            $cols = DAL::TableFields($t);
            foreach($cols as $col){
                if(strpos($col,"_fk") > -1){
                    $col_fks[$col] = DAL::getDALT(substr($col,0,strpos($col,"_fk")));
                }
            }
        }
        if(count($d) == 0) return "EMPTY";
        $e = "TblCtrl".time();
        $html = '<div class="container-fluid" id="'.$e.'" >';
        $html .= '<script> SYS.TableHtml = []; </script>';
        $html .= '<button style="float:right" class="btn btn-success" onclick="$(`#DataT'.time().'`).DataTable();$(this).remove();" >
        Transform table to datatable</button>';
        $html .= '<div id="MsgBox"></div><table id="DataT'.time().'" class="table" >';
        $html .= '<thead>';
        foreach($d[key($d)] as $k=>$v){
            if(in_array($k,$hideCols)) continue;
            $title = self::columnAlias($k);
            $title = str_replace("_"," ",$title);
            $html .= '<th class="tableHeader" >' .$title. '</th>';
        }
        foreach($calc as $c=>$v){
            $html .= '<th class="tableHeader" >'.$c.'</th>';
        }
        if(count($op) > 0 ){
            $html .= '<th class="tableHeader" ><i class="fa fa-cog"></i> </th>';
        }
        $html .= '</thead>';
        $html .= '<tbody>';
        for($i = 0 ; $i < count($d) ; $i++){
            if(isset($d[$i]['active']) && $d[$i]['active'] == 0 && $_SESSION[SI]['user']['id'] > 1){
                continue;
            }
            $adminrecord = ($t == "account" && $d[$i][key($d[$i])] == 1);
            if($adminrecord) continue;
            $html .= '<tr>';
            foreach($d[$i] as $c=>$v){
                if(in_array($c,$hideCols)) continue;
                if($c == "cv"){
                    $html .= '<td> <a download="'.$d[$i]['first_name'].'.pdf" href="'.SELF_DIR."Assets/cvs/" . $v . '">Download CV </a></td>';
                }
                elseif(strpos($c,"active") > -1){
                    $html .= '<td> '. ($v == 1 ? "Yes" : "No").' </td>';
                }
                elseif(strpos($c,"image") >  -1){
                    $ext = UTIL::ext($v);
                    $url = SELF_DIR . "Assets/Images/$ext/$v";
                    $html .= "<td><a targe=_blank href='$url'><img height=50 src='$url' ></a></td>";
                }
                elseif(strpos($c,"_fk") >  -1){
                    $html .= "<td>".(isset($col_fks[$c][$v]) ? $col_fks[$c][$v]['name'] : "") ."</td>";
                }
                elseif(strpos($c,"html") > -1){
                    $html_for_id = trim($d[$i][key($d[$i])] ."_". $c);
                    $html .= '<script> SYS.TableHtml["'.$html_for_id.'"] = '.json_encode($v).'; </script>';
                    $html .= '<td> 
                            <button class="btn btn-info" onclick="SYS.dialog(SYS.TableHtml[\''.$html_for_id.'\'],\''.$c.'\');">see</button>
                        </td>';
                }
                else{
                    $html .= '<td>' . $v . '</td>';
                }
            }
            foreach($calc as $c=>$cv){
                $value = "";
                foreach($cv as $k=>$v){
                    if($v["k"] == "field" && isset($d[$i][$v["v"]])){
                        if(isset($v["m"])){
                            $m = $v['m'];
                            $value .= $m(trim($d[$i][$v["v"]]));
                        }
                        else{
                            $value .= $d[$i][$v["v"]];
                        }
                    }
                    else{
                        $value .= $v["v"];
                    }
                }
                $html .= '<td>' . $value . '</td>';
            }
            if(count($op) == 0){}
            else if(!$adminrecord){
                $html .= '<td>';
                foreach($op as $opo=>$callback){
                    $c = "$callback/$t/".key($d[$i]).'/'.$d[$i][key($d[$i])];
                    $cls = "btn-info";
                    if(strpos("edit",$opo) > -1){
                        $cls = "btn-info";
                    }
                    elseif(strpos("delete",$opo) > -1 && isset($_SESSION[SI]['user']['id']) &&$_SESSION[SI]['user']['id'] < 2 ){
                        $cls = "btn-danger";
                    }
                    elseif(strpos("disable",$opo) > -1){
                        $cls = "btn-warning";
                    }
                    elseif(strpos("remove",$opo) > -1){
                        $cls = "btn-warning";
                    }
                    //else{continue;}
                    $html .= '<button type="button" class="btn btn-sm '.$cls.'" 
                    onclick="SYS.LoadXHR(\''.$e.'\',\''.$c.'\');" >'.$opo.'</button>';
                }
                $html .= '</td>';
            }
            else{
                $html .= '<td>admin record</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= '<p style="padding:3em" ></p> </div>';
        return $html;
    }
    public static function getEditTable($t,$k,$v){
        $d = self::call_sp("select * from $t where $k = :v",[
            ["k"=>"v","v"=>$v]
        ]);
        if(count($d) == 0) die("not found");
        $d = $d[0];
        return self::getFormForTable($t,$d);
    }
    public static function getFormForTable($t,$d = null, $hidecols = [], $altsub = null){
        $html = '<form action="" method="post" class="DALForm">';
        $fs = self::call_sp("describe $t");
        if($altsub == null){
            $altsub = "DBSave";
        }
        $html .= '<input type="hidden" name="key" value="'.$altsub.'" >';
        $html .= '<input type="hidden" name="table" value="'.$t.'" >';
        foreach($fs as $fld){
            $F = $fld['Field'];
            if(in_array($F,$hidecols)) continue;
            if($F == "id"){
                if(!isset($d[$F])) continue;
                $html .= '<input type="hidden" name="'.$F.'" value="'.$d[$F].'" class="form-control">';
            }
            elseif(strpos($F,"password") > -1){
                $html .= '<div class="form-group row">
                    <label class="col-sm-2 col-form-label">'.$F.'</label>
                    <div class="col-sm-10"><input type="password" name="'.$F.'" value="'.( (isset($d[$F])) ? $d[$F] : '' ).'" class="form-control"></div>
                </div>';
            }
            elseif(strpos($F,"image") > -1){
                $html .= self::getViewControlForImage($F,(isset($d[$F])) ?$d[$F] : '', $F);
            }
            elseif(strpos($F,"active") > -1){
                $altnme = (strpos($F,"_")) ? substr($F,strpos($F,"_")+1) : $F;
                $html .= '<div class="form-group row">
                    <label class="col-sm-2 col-form-label">'.self::columnAlias($altnme).'</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="'.$F.'" >
                            <option '.((isset($d[$F])) ? ($d[$F] == "1") ? "selected=selected":'': '') .' value="1">Yes</option>
                            <option '.((isset($d[$F])) ? ($d[$F] == "0") ? "selected=selected":'': '').' value="0">No</option>
                        </select>
                    </div>
                </div>';
            }
            elseif(strpos($F,"timestamp") > -1){
                //ignore timestamp
            }
            elseif(strpos($F,"salt") > -1){
                //ignore salt
            }
            elseif(strpos($F,"enum_") > -1){
                $label = substr($F,strlen("enum_"));
                $enums = $fld['Type'];
                $enums = str_replace("enum(","",$enums);
                $enums = str_replace(")","",$enums);
                $enums = str_replace("'","",$enums);
                $enums = explode(",",$enums);
                $html .= '<div class="form-group row">
                <label class="col-sm-2 col-form-label">'.self::columnAlias($F).'</label>
                <div class="col-sm-10">
                <select type="text" name="'.$F.'" class="form-control">';
                foreach($enums as $enum){
                    $sl = "";
                    if(isset($d[$F]) && $enum == $d[$F] ) $sl = "selected=selected";
                    $html .= '<option value="'.$enum.'" '. $sl .' >'.$enum.'</option>';
                }
                $html .= '</select></div></div>';
            }
            elseif(strpos($F,"html") > -1){
                $html .= '<div class="form-group row">
                    <label class="col-sm-2 col-form-label">'.self::columnAlias($F).'</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="'.$F.'" >
                        '.( (isset($d[$F])) ? $d[$F] : '' ).'
                        </textarea>
                    </div>
                    <script>CKEDITOR.replace("'.$F.'");</script>
                    </div>';
            }
            elseif(strpos($F,"_fk") > -1){
                $html .= '<div class="form-group row">
                    <label class="col-sm-2 col-form-label">'.$F.'</label>
                    <div class="col-sm-10">
                    <select type="text" name="'.$F.'" class="form-control">';
                $fktable = substr($F,0,strpos($F,"_fk"));
                $fks = self::call_sp("select id,name,active from $fktable");
                foreach($fks as $fk){
                    if($fk['active'] == 0) continue;
                    $sl = "selected=selected";
                    if(isset($d[$F]) && $fk['id'] == $d[$F] ) $sl = "selected=selected";
                    else $sl = "";
                    $html .= '<option value="'.$fk['id'].'" '. $sl .' >'.$fk['name'].'</option>';
                }
                $html .= '</select></div></div>';
            }
            else{
                $html .= '<div class="form-group row">
                    <label class="col-sm-2 col-form-label">'.$F.'</label>
                    <div class="col-sm-10"><input type="text" name="'.$F.'" value="'.( (isset($d[$F])) ? $d[$F] : '' ).'" class="form-control"></div>
                </div>';
            }
        }
        $html .= '<button to="CT'.time().'" type="button" class="btn btn-lg btn-success formSubmitter" onclick="SYS.XHRForm(this);" ><i class="fa fa-save">&nbsp; Save</i></button>';
        $html .= '<div id="CT'.time().'"></div>';
        return $html;
    }
    public static function getViewQuery($t){
        $q = [];
        if(!isset($q[$t])){
            return "select * from $t";
        }
        else{
            return $q[$t];
        }
    }
    public static function getViewControlForImage($F,$v,$altName = null){
        $html = "";
        $html .= '
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">'.($altName == null ? $F : $altName).'</label>
            <input type="hidden" name="'.$F.'" value="'.$v.'">
            <div class="col-sm-8">
                <input type="file" name="tmp_input_'.$F.'" class="form-control">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-success" type="button" onclick="SYS.handleDALFileUpload(this);">
                    <i class="fa fa-upload"></i>
                </button>
            </div>';
        $html .= '<div class="image-view-thumbnail">';
        if($v != ""){
            $ext = UTIL::ext($v);
            $url = SELF_DIR ."Assets/Images/$ext/$v";
            $html .= "<a href='' target=_blank ><img height=50 src='$url' /></a>";
        }
        $html .= '</div>'; // end image-view-thumbnail
        $html .= '</div>'; // end form-group row
        return $html;
    }
    public static function columnAlias($c){
        $v = [];
        $v['name'] = "name";
        $c = str_replace("_fk","",$c);
        $c = str_replace("_"," ",$c);
        return (isset($v[$c])) ? $v[$c] :$c;
    }
    //get Data Access Layer Table
    public static function getDALT($t,$id=null){
        if(method_exists(new DALT,$t)){
            return DALT::$t($id);
        }
        else{
            return DALT::default_view($t,$id);
        }
    }
}
// Data Access Layer Table control : each table can have special query
class DALT{
    public static function default_view($t,$id = null){
        if(! DAL::table_exist($t)){
            return [];
        }
        $addedQ = "";
        $Parms = [];
        if($id != null){
            $addedQ = " and id = :input_id LIMIT 1";
            $Parms = [
                ["k"=>"input_id","v"=>$id]
            ];
        }
        $data_r = DAL::call_sp("SELECT * FROM $t WHERE 1=1 $addedQ",$Parms);
        $data = [];
        foreach($data_r as $d){
            $data[$d['id']] = $d;
        }
        return $data;
    }
}
?>