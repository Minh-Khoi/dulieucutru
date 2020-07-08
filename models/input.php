<?php

require_once dirname(__FILE__, 2) . "/controllers/global.php";
/** 
 * this class stand for the object which is json_decoded from the file input.json
 * @property raw_datas array<stdClass> is the raw data which is loaded by the function file_get_contents();
 * @property good_datas array<hogiadinh> is the data after handled (). At first good_datas is empty, and it will be computed when the input instance invoked
 */
class input
{
  private $raw_datas;
  public $list_hogiadinh;

  /**
   * Class input constructor.
   */
  public function __construct()
  {
    // $this->raw_datas = json_decode(file_get_contents(dirname(__FILE__, 2) . '/input/input.json'))->Sheet1;
    // var_dump(json_decode(file_get_contents(dirname(__FILE__, 2) . "/input/input.json")));
    $this->raw_datas = json_decode($_POST['json_input'])->Sheet1;
    $this->tap_hop_ho();
  }

  /** split raw_datas into the array of object "hogiadinh"  then add to the @property $this->list_hogiadinh*/
  private function tap_hop_ho()
  {
    global $SHK;
    $array_of_ho = [];
    $array_nhankhau_total = $this->raw_datas;
    // echo count($array_nhankhau_total);
    $array_nguoi_trong_tung_ho_1 = [];
    for ($i = 0; $i < count($array_nhankhau_total); $i++) {
      // echo $array_nhankhau_total[$i]->SHK;
      // echo $i . "\n";
      if ($i == 0 || $array_nhankhau_total[$i]->$SHK == $array_nhankhau_total[$i - 1]->$SHK) {
        array_push($array_nguoi_trong_tung_ho_1, $array_nhankhau_total[$i]);
        if (!isset($array_nhankhau_total[$i]->$SHK)) {
          echo " stop at variable i = $i";
          break;
        }
        // echo "done $i \t";
      } else {
        // echo ;
        $ho = new hogiadinh($array_nguoi_trong_tung_ho_1);
        // var_dump($ho);
        // array_push($array_nhankhau_total, $array_nguoi_trong_tung_ho_1);
        array_push($array_of_ho, $ho);
        $array_nguoi_trong_tung_ho_1 = [];
        array_push($array_nguoi_trong_tung_ho_1, $array_nhankhau_total[$i]);
      }
    }
    $this->list_hogiadinh = $array_of_ho;
  }

  // /** 
  //  * Convert hogiadinh from array to hogiadinh instance object
  //  * @param array $array_nguoi_trong_tung_ho_1 
  //  */
  // private function convertToInstance(array $array_nguoi_trong_tung_ho_1)
  // { 
  //   $ho = new hogiadinh($array_nguoi_trong_tung_ho_1);
  //   return $ho;
  // }
}
