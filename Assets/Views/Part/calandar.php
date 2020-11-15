<div class="container">
    <div class="CalandarContainer">
        <h1>My Calandar</h1>
        <div id='calendar_response'></div>
        <div id='calendar' to="calendar_response" ></div>
    </div>
    <?php
        $d = DAL::call_sp("select id, name as title, date_start as start, date_end as end from calender_event
        where account_fk = :account_fk and active = 1
        ",[["k"=>"account_fk","v"=>$_SESSION[SI]["user"]["id"]]]);
        $events = json_encode($d);
    ?>
    <script>SYS.calender_setup("#calendar",<?= $events ?>) </script>
</di>