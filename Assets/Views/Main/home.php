<?php 
global $addedMetas;
$addedMetas = "";
$addedMetas .= '<link href="'.SELF_DIR.'Assets/System/home.css" rel="stylesheet">';

WEB::load_view("Part","head"); ?>
<?php 
// WEB::load_view("Part","nav"); 
?>
<?php WEB::load_view("Part","jumbotron"); ?>
<div class="container mainBodyContainer">
    <div class="floatNavButton">
        <?php if(isset($_SESSION[SI]["user"])){
                echo '<a  class="btn btn-primary" href="'.SELF_DIR.'login">Login <i class="fa fa-user"></i> </a>';
            }
            else{
                echo '<a  class="btn btn-primary" href="'.SELF_DIR.'profile">Profile <i class="fa fa-user"></i> </a>';
            }
        ?>
    </div>
    <h1 class="home-header-h1" >JOB SEARCH</h1>
    <form action="" class="form">
        <input type="hidden" name="key" value="jobs/search">
        <div class="container">
            <div class="row form-group">
                <div class="col-sm-3">
                    Category
                </div>
                <div class="col-sm-9">
                    <select name="" id="" class="form-control">
                        <option value=""> &middot; &middot; &middot;</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-3">
                    JOB TYPE
                </div>
                <div class="col-sm-9">
                    <select name="" id="" class="form-control">
                        <option value=""> &middot; &middot; &middot;</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="container buttonContainer" to="search_table_container" onclick="SYS.XHRForm(this)">
            <div class="row">
                <div class="col-sm-8" >
                    Find Job
                </div>
                <div class="col-sm-4" >
                    <i class="fa fa-search"></i>
                </div>
            </div>
        </div>
    </form>
    <div id="search_table_container" class="container">

    </div>
</div>
<?php WEB::load_view("Part","foot");   ?>