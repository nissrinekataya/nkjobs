<!-- AUTHOR MJZ -->
<?php
global $addedMetas;

if(isset($addedMetas)) echo $addedMetas;
WEB::load_view("Part","head");
WEB::load_view("Part","nav2"); ?>
  <div class="container-fluid PDemiaDashboardContainer">
    <div class="row">
      <?php WEB::load_view("Part","dash_side_nav"); ?>
      <?php WEB::load_view("Part","dash_main_body"); ?>
    </div>
  </div>
</body>
</html>