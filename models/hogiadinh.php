<?php

/** 
 * This class stand for information of an hogiadinh those are needed for generating biên bản phúc tra
 * @property array<stdClass> $array_of_nhankhau: list of nhankhau (as stdClass instances) in an hogiadinh
 * @property string $so_hshk
 * @property string $so_seri
 * @property string $ten_chuho
 * @property int $so_nhankhau
 * @property string $tdp_cu
 * @property string $tdp_moi
 * @property string $thongtinphuctra
 */
class hogiadinh
{
  private $array_of_nhankhau;
  public $so_hshk, $so_seri, $ten_chuho, $so_nhankhau, $tdp_cu, $tdp_moi, $nguoi_ky_ten, $thongtin_phuctra;

  /**
   * Class hogiadinh constructor.
   */
  public function __construct(array $array_of_nhankhau)
  {
    global $SHK, $so_seri, $ho_ten, $tdp_cu, $tdp_moi, $nguoi_ky_ten;
    $this->array_of_nhankhau = $array_of_nhankhau;
    $nhankhau_dautien = $array_of_nhankhau[0];
    $this->so_hshk = $nhankhau_dautien->$SHK;
    $this->so_seri = $nhankhau_dautien->$so_seri;
    $this->ten_chuho = $nhankhau_dautien->$ho_ten;
    $this->so_nhankhau = count($array_of_nhankhau);
    $this->tdp_cu = $nhankhau_dautien->$tdp_cu;
    $this->tdp_moi = $nhankhau_dautien->$tdp_moi;
    $this->nguoi_ky_ten = $nhankhau_dautien->$nguoi_ky_ten;

    // $thongtinphuctra = ..... 
    $this->thongtin_phuctra = $this->get_thongtinphuctra();
  }

  private function get_thongtinphuctra()
  {
    // var_dump($this->array_of_nhankhau);
    $thongtinphuctra_ca_ho = "";
    foreach ($this->array_of_nhankhau as $k => $nhankhau) {
      $thongtinphuctra_canhan = $this->get_thongtinphuctra_1_nguoi($nhankhau);
      $thongtinphuctra_ca_ho .= $thongtinphuctra_canhan;
      // var_dump($thongtinphuctra_canhan);
    }
    // var_dump($thongtinphuctra_ca_ho);
    return $thongtinphuctra_ca_ho;
  }

  private function get_nam_sinh(stdClass $nhankhau)
  {
    global $ngay_sinh;
    $ngaysinh = $nhankhau->$ngay_sinh;
    $birthday_array = preg_split('/(\/|\.|\-)/', $ngaysinh);
    return $birthday_array[count($birthday_array) - 1];
  }

  private function get_thongtinphuctra_1_nguoi(stdClass $nhankhau)
  {
    global $ho_ten, $cmnd_ban_than, $cmnd_cha, $cmnd_chu_ho, $so_seri,
      $cmnd_me, $cmnd_vo_chong, $noi_dkks, $gioi_tinh, $ngay_sinh;

    // Nếu nhân khẩu sinh sau năm 2015 thì không cần bổ sung thêm gì
    $nam_sinh = intval($this->get_nam_sinh($nhankhau));
    if ($nam_sinh > 2015) {
      return "";
    }

    //Nếu nhân khẩu sinh từ năm 2000 đến 2015, thêm mục "Thiếu số CMND bản thân"
    if ($nam_sinh <= 2015 && $nam_sinh >= 2000) {
      $thongtinphuctra_canhan = "{$nhankhau->$ho_ten}: Thiếu nơi đăng ký khai sinh; thiếu số sổ hộ khẩu; thiếu số CMND của cha, mẹ, bản thân,"
        . ((strtolower($nhankhau->$gioi_tinh) == "nam") ? "vợ" : "chồng") . ", chủ hộ. ";
    } else {
      $thongtinphuctra_canhan = "{$nhankhau->$ho_ten}: Thiếu nơi đăng ký khai sinh; thiếu số sổ hộ khẩu; thiếu số CMND của cha, mẹ,"
        . ((strtolower($nhankhau->$gioi_tinh) == "nam") ? "vợ" : "chồng") . ", chủ hộ. ";
    }

    // validation values
    $co_thongtin_bosung = true;
    $co_thongtin_chua_bosung_duoc = true;
    $first_of_chuabosung = true;
    $end_of_dabosung = false;

    // holding values
    $thongtinphuctra_canhan_dabosung = "Đã bổ sung: ";
    $thongtinphuctra_canhan_chuacungcap = " Chưa cung cấp được: ";
    // var_dump($nhankhau);
    // var_dump(isset($nhankhau->cmnd_ban_than));
    if (isset($nhankhau->$so_seri)) {
      $thongtinphuctra_canhan_dabosung .= "số sổ hộ khẩu, ";
      $co_thongtin_bosung = false;
      // echo "block running";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .=  "số sổ hộ khẩu ";
      $first_of_chuabosung = false;
    }
    if (isset($nhankhau->$cmnd_ban_than)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= ($nam_sinh < 2000) ? "" : "số CMND bản thân, ";
      // echo "block running";
    } else {
      $co_thongtin_chua_bosung_duoc = ($nam_sinh < 2000) ? true : false;
      $thongtinphuctra_canhan_chuacungcap .= ($nam_sinh < 2000) ? "" : ((($first_of_chuabosung) ? "," : "") . " số CMND bản thân ");
      $first_of_chuabosung = ($nam_sinh < 2000) ? true : false;
      // var_dump($first_of_chuabosung);
      // echo $nhankhau->$ho_ten;
    }
    if (isset($nhankhau->$cmnd_chu_ho)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= "số CMND chủ hộ, ";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .= ((!$first_of_chuabosung) ? "," : "") . " số CMND chủ hộ ";
      $first_of_chuabosung = false;
    }
    if (isset($nhankhau->$cmnd_cha)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= "số CMND cha, ";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .= ((!$first_of_chuabosung) ? "," : "") . " số CMND cha ";
      $first_of_chuabosung = false;
    }
    if (isset($nhankhau->$cmnd_me)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= "số CMND mẹ, ";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .= ((!$first_of_chuabosung) ? "," : "") . " số CMND mẹ ";
      $first_of_chuabosung = false;
    }
    if (isset($nhankhau->$cmnd_vo_chong)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= "số CMND "
        . ((strtolower($nhankhau->$gioi_tinh) == "nam") ? "vợ" : "chồng") . ", ";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .= ((!$first_of_chuabosung) ? "," : "")
        . " số CMND " . ((strtolower($nhankhau->$gioi_tinh) == "nam") ? "vợ" : "chồng");
      $first_of_chuabosung = false;
    }
    if (isset($nhankhau->$noi_dkks)) {
      $co_thongtin_bosung = false;
      $thongtinphuctra_canhan_dabosung .= "nơi đăng ký khai sinh.";
      $thongtinphuctra_canhan_chuacungcap .= ".";
    } else {
      $co_thongtin_chua_bosung_duoc = false;
      $thongtinphuctra_canhan_chuacungcap .= ((!$first_of_chuabosung) ? "," : "") . " nơi đăng ký khai sinh. ";
      $thongtinphuctra_canhan_dabosung .= ".";
      $first_of_chuabosung = false;
    }

    $thongtinphuctra_canhan_dabosung = ($co_thongtin_bosung)
      ? "" : $thongtinphuctra_canhan_dabosung;
    $thongtinphuctra_canhan_chuacungcap = ($co_thongtin_chua_bosung_duoc)
      ? "" : $thongtinphuctra_canhan_chuacungcap;

    // var_dump($thongtinphuctra_canhan);
    return $thongtinphuctra_canhan . $thongtinphuctra_canhan_dabosung . $thongtinphuctra_canhan_chuacungcap . "\n";
  }
}