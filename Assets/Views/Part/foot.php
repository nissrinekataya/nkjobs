<footer class="pdemia-footer page-footer font-small blue pt-4">
    <div class="container-fluid text-center text-md-left">
        <div class="row">
            <div class="col-md-6 mt-md-0 mt-3">
                <p><img  class="img-fluid" src="<?= web::getImageUrl(LOGO) ?>" /></p>
            </div>
            <hr class="clearfix w-100 d-md-none pb-3">
            <div class="col-md-3 mb-md-0 mb-3">
                <h5 class="text-uppercase">Social</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="facebook.com"><i class="fa fa-facebook"></i></a>
                        <a href="instagram.com"><i class="fa fa-instagram"></i></a>
                        <a href="twitter.com"><i class="fa fa-twitter"></i></a>
                    </li>
                </ul>
                <h5 class="text-uppercase">Contact Us</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="<?= SELF_DIR ?>contact"><i class="fa fa-envelope"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-copyright text-center py-3">
        <p> <?= date("Y",time()) ?> <a href="pdemia.com"> &copy; PDemia</a>, by <a href="pdemia.com/mjz">MJZ</a>.</p>
    </div>
</footer>