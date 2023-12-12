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
$_SESSION["pos"] = "bill";
$_SESSION["active"] = "bilist";
//
if(!isset($_GET["id"]) || !is_numeric($_GET["id"]) || $_GET["id"] < 1) {
    header("location:/hostay/admin/bills.php");
}
//
require_once("../app/models/BillModel.php");
require_once("../app/models/RoomModel.php");
require_once("components/BillLibrary.php");
require_once("../libraries/Utilities.php");

$id = $_GET["id"];
$bm = new BillModel();
$bill = $bm->getBill($id);
if($bill == null) {
    header("location:/hostay/admin/bills.php?err=noexist");
}

$diff = getDateDiff($bill->getBill_start_date(), $bill->getBill_end_date());

$rm = new RoomModel();
$room = $rm->getRoom($bill->getBill_room_id());
$price = 0;
if($room != null) {
    $price = $room->getRoom_price();
}

require_once("layouts/header.php");
require_once("layouts/Toast.php");
?>
<!--Start main page-->
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between">
      <h1>Danh sách</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/hostay/admin/">Trang chủ</a></li>
          <li class="breadcrumb-item active"><a href="/hostay/admin/bills.php">Đơn đặt phòng</a></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
		        <div class="card">
		            <div class="card-body">
                        <!-- Start preview room -->
                        <div class="row mt-3">
                            <?php
                                if($room == null) {
                            ?>
                                <div class="col-sm-12 d-flex justify-content-center text-danger">
                                    Phòng không tồn tại hoặc đã bị xóa khỏi cơ sở dữ liệu!
                                </div>
                            <?php
                                } else {
                            ?>
                                <div class="col-sm-12 mb-3 room-img-container">
                                    <img src="<?=$room->getRoom_image()?>" class="room-img" alt="">
                                </div>
                                <div class="col-md-6 row mb-3">
                                    <div class="col-sm-12 fw-bold">Tên khách sạn</div>
                                    <div class="col-sm-12"><?=$room->getRoom_hotel_name()?></div>
                                </div>
                                <div class="col-md-6 row mb-3">
                                    <div class="col-sm-12 fw-bold">Loại phòng</div>
                                    <div class="col-sm-12"><?=$room->getRoom_type()?></div>
                                </div>
                                <div class="col-sm-12 row mb-3">
                                    <div class="col-sm-12 fw-bold">Địa chỉ</div>
                                    <div class="col-sm-12"><?=$room->getRoom_address()?></div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <a href="/hostay/views/room.php?id=<?=$room->getRoom_id()?>">
                                        Click để xem chi tiết.
                                    </a>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                        <!-- End preview room -->
                        <!-- Start bill detail -->
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Họ và tên</div>
                            <div class="col-md-9"><?=$bill->getBill_fullname()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Email</div>
                            <div class="col-md-9"><?=$bill->getBill_email()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Số điện thoại</div>
                            <div class="col-md-9"><?=$bill->getBill_phone()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Số phòng</div>
                            <div class="col-md-9"><?=$bill->getBill_number_room()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Số người trưởng thành</div>
                            <div class="col-md-9"><?=$bill->getBill_number_adult()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Số trẻ nhỏ</div>
                            <div class="col-md-9"><?=$bill->getBill_number_children()?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Ngày tạo</div>
                            <div class="col-md-9"><?=date("d/m/Y", strtotime($bill->getBill_created_at()))?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Ngày nhận phòng</div>
                            <div class="col-md-9"><?=date("d/m/Y", strtotime($bill->getBill_start_date()))?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Ngày trả phòng</div>
                            <div class="col-md-9"><?=date("d/m/Y", strtotime($bill->getBill_end_date()))?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Lợi nhuận ước tính</div>
                            <div class="col-md-9"><?=$price * $diff * $bill->getBill_number_room()?>$</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12 fw-bold">Ghi chú</div>
                            <div class="col-sm-12"><?=$bill->getBill_notes()?></div>
                        </div>
                        <form action="/hostay/actions/billupd.php" method="post">
                            <div class="row mb-3">
                                <label class="col-md-3 fw-bold" for="slcSta">Trạng thái</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="slcStatic" id="slcSta">
                                        <?=generateOption($bill->getBill_static())?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="idForPost" value="<?=$bill->getBill_id()?>">
                            <div class="row mb-3 d-flex justify-content-center">
                                <button class="col-md-3 btn btn-primary disabled btn-updsta"
                                    type="submit"
                                    name="updSta">
                                    Cập nhật trạng thái
                                </button>
                            </div>
                        </form>
                        <!-- End bill detail -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!--End main page-->
<script>
    let slc = document.querySelector("#slcSta")
    slc.addEventListener("change", () => {
        if(slc.value == <?=$bill->getBill_static()?>) {
            document.querySelector(".btn-updsta").classList.add("disabled");
        } else {
            document.querySelector(".btn-updsta").classList.remove("disabled");
        }
    });
</script>
<?php
require_once("layouts/footer.php");
?>