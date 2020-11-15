<?php
//MJZ automated dashboard side nav
global $sideNav;
if(!isset($sideNav)){
    $sideNav = [];
}
?>
<nav id="PDemia-Dashboard-NAV" class="col-md-2  bg-light sidebar">
    <div class="sidebar-sticky">
        <?php foreach($sideNav as $navTitle=>$elements) { ?> 
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span><?=$navTitle?></span>
                <a class="d-flex align-items-center text-muted" href="#" onclick="$('#<?=md5($navTitle)?>').toggle();" >
                    <span class="fa fa-plus"></span>
                </a>
            </h6>
            <ul id="<?=md5($navTitle)?>" class="nav flex-column" 
                <?= ($navTitle == "Dashboard" ? "" : "style='display:none'") ?>>
                <?php foreach($elements as $e){ ?> 
                    <li class="nav-item" onclick="SYS.XHRFct('<?=$e['fct'] ?>','DashContents');" >
                        <a class="nav-link" href="#" >
                            <span class="fa fa-<?=$e['fa'] ?>"></span> &nbsp; <?=$e['name'] ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
</nav>