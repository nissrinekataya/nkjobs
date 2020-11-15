<main id="PDemia-Dashboard-MAIN" role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
    <div id="MainDahsHeader" class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
        <div style="display:none;" class="btn-group mr-2">
            <button class="btn btn-sm btn-outline-secondary">Share</button>
            <button class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <div class="dorpdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span data-feather="calendar"></span>Fast Access
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="<?=SELF_DIR?>">Home</a>
            </div>
        </div>
        </div>
    </div>
    <div id="DashContents" class="DashContents"></div>
</main>