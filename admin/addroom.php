<?php
session_start();
if(!isset($_SESSION["user"])) {
    header("location:/hostay/admin/login.php");
} else {
    if(!isset($_SESSION["user"]["permission"]) || $_SESSION["user"]["permission"] < 1) {
        header("location:/hostay/admin/login.php?err=permis");
    }
}
//
$_SESSION["pos"] = "room";
$_SESSION["active"] = "roadd";
//

require_once "layouts/header.php";
?>
<!--Start main page-->
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between">
      <h1>Thêm mới</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/hostay/admin/">Trang chủ</a></li>
          <li class="breadcrumb-item active">Phòng</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
		        <div class="card">
		            <div class="card-body">
                        <h1>Addroom</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!--End main page-->
<?php
require_once "layouts/footer.php";
?>