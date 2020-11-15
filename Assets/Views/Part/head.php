<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" sizes="16x16" href="<?= web::getImageUrl(LOGO) ?>" >
        <title>Project Tracking Platform</title>
        <link rel="canonical" href="<?=SELF_DIR?>">
        <script>let BASE_DIR = "<?= SELF_DIR ?>"; </script>
        <!-- FontAwesome -->
        <link href="<?= SELF_DIR ?>Assets/Libraries/FontAwesome/fa.css" rel="stylesheet">
        <!-- jquery -->
        <script src="<?= SELF_DIR ?>Assets/Libraries/JQuery/jquery-3.4.0.min.js" ></script>
        <!-- bootstrap -->
        <link href="<?= SELF_DIR ?>Assets/Libraries/Bootstrap/css/bootstrap-4.1.3.min.css" rel="stylesheet">
        <script src="<?= SELF_DIR ?>Assets/Libraries/Bootstrap/js/bootstrap.min.js"></script>
        <!-- popper [drop down menu] -->
        <script src="<?= SELF_DIR ?>Assets/Libraries/Bootstrap/js/popper.min.js"></script>
        <!-- DataTable -->
        <script src="<?= SELF_DIR ?>Assets/Libraries/DataTable/datatables.min.js" ></script>
        <link href="<?= SELF_DIR ?>Assets/Libraries/DataTable/datatables.min.css" rel="stylesheet">
        <!-- jquery-ui -->
        <link href="<?= SELF_DIR ?>Assets/Libraries/JQuery/jquery-ui.min.css" rel="stylesheet">
        <script src="<?= SELF_DIR ?>Assets/Libraries/JQuery/jquery-ui.min.js" ></script>
        <!-- fullcalendar -->
        <link href="<?= SELF_DIR ?>Assets/Libraries/FullCalendar/fullcalendar.min.css" rel="stylesheet" />
        <script src="<?= SELF_DIR ?>Assets/Libraries/FullCalendar/lib/moment.min.js"></script>
        <script src="<?= SELF_DIR ?>Assets/Libraries/FullCalendar/fullcalendar.min.js"></script>
        <!-- System -->
        <script src="<?= SELF_DIR ?>Assets/System/sys.js"></script>
        <link href="<?= SELF_DIR ?>Assets/System/web.css" rel="stylesheet">
        <!-- Resources -->
        <?php global $addedMetas; if(isset($addedMetas)) echo $addedMetas; ?>
    </head>
<body>