
<?php 
global $addedMetas;
    $addedMetas = '<link href="'.SELF_DIR.'Assets/Resources/contact.css" rel="stylesheet">';
    WEB::load_view("Part","head");
?>
<?php WEB::load_view("Part","nav");         ?>
<?php WEB::load_view("Part","jumbotron");   ?>
<div class="container">
    <form class="form container-fluid">
        <input type="hidden" name="key" value="contact/submit">
        <div class="row">
            <div class="col-sm-2">
                <label for="inputEmail"> Email  </label>
            </div>
            <div class="col-sm-10">
                <input name="email" type="email" class="form-control" placeholder="Email address" required autofocus>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-2">
                <label> Message  </label>
            </div>
            <div class="col-sm-10">
                <textarea name="message" class="form-control" ></textarea>
            </div>
        </div>
        <hr>
        <div class="buttons">
            <button to="CT<?= time() ?>" class="btn btn-lg btn-primary btn-block" type="button" onclick="SYS.XHRForm(this);">Send Message</button>
            <div id="CT<?= time() ?>" class="CT1"></div>
        </div>
    </form>
</div>
<?php WEB::load_view("Part","foot");   ?>