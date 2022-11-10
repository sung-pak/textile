<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use \DB;
use Illuminate\Database\Eloquent\Builder;

use TeamTNT\TNTSearch\TNTSearch;

// php artisan make:model app\Product
class Product extends Model {
  //protected $table = 'product_master'; // override plural

  private $discontinuedArr = array(
      array('item_name', 'NOT LIKE', '%(%'),
      array('item_name', 'NOT LIKE', '%)%'),
      array('item_name', 'NOT LIKE', '%-1%'),
      array('item_name', 'NOT LIKE', '%-'),
      array('vendor', 'NOT LIKE', '%Unknown%'),
      array('color_name', 'NOT LIKE', 'do not use%'),
      array('color_name', 'NOT LIKE', 'custom color%'),
      array('mill_description', 'NOT LIKE', 'custom%'),
      array('color_name', 'NOT LIKE', 'custom%'),
      array('item_number', 'NOT LIKE', 'sko%'),
      array('item_name', 'NOT LIKE', 'PROMO%'),
      array('color_name', 'NOT LIKE', '' ),
      array('content', 'NOT LIKE', '' ),
      array('collection', 'NOT LIKE', '' ),
      array('product_type', 'NOT LIKE', '' ),
      array('product_category', 'NOT LIKE', '' ),
      array('internal_comment', 'NOT LIKE', '%Discontinued%'),
      array('custom_item', 'NOT LIKE', 1),
      array('discontinue_code', 'NOT LIKE', 1),
      array('style_additional_description', 'NOT LIKE', '%Discontinued%'),
      //array('product_master.collection', 'NOT LIKE', '%2021 Summer%'),
      //array('', '', ''),
  );

  public function index($columnArr, $id, $page){
    $query = DB::table('product_master')
             ->leftJoin('product_list', 'product_master.item_name', '=', 'product_list.fabricName')
             ->select('product_master.item_name', 'product_master.date_introduced', 'product_list.main_img', DB::raw('count(*) as total'), DB::raw('SUBSTRING(product_master.collection, 1, 4) as col_year'), DB::raw('SUBSTRING(product_master.collection, 6, 1) as col_first'),DB::raw('SUBSTRING(product_master.collection, 7, 1) as col_second'))
             //->where('product_master.product_type', 'NOT LIKE', '%Sheers/Drapery%')
             ->where('product_master.product_type', 'NOT LIKE', '%Faux Leather%')
             ->where('product_master.product_type', 'NOT LIKE', '')
             ->where($this->discontinuedArr)
             ->groupBy('product_master.item_name');

    //dd($query->toSql());

    if ( $id === 'all-wallcovering' ) {
      $query3 = clone $query;
      $query1 = $query->where('product_list.feature_display', '1')->orderBy('product_master.item_name')->get();
      $query2 = $query3->where('product_list.feature_display', '0')->orderBy('col_year', 'DESC')->orderBy('col_first')->orderBy('col_second', 'DESC')->orderBy('item_name')->get();
      $query = $query1->merge($query2);
      return $query->slice(($page-1)*30, 30);
    }

    $query = $query->simplePaginate(30); // ->get()

    return $query; // ->get()
  }

  public function getProductList($id, $columnArr, $wcArr = array()){

    $query = DB::table('product_master')
                ->leftJoin('product_list', 'product_master.item_name', '=', 'product_list.fabricName');

    if( $id=='wallcoverings' || $id=='all wallcoverings' ){
      $query = $query->select($columnArr)
                ->where('product_master.product_type', '=', $wcArr[0])
                ->orWhere('product_master.product_type', '=', $wcArr[1])
                ->orWhere('product_master.product_type', '=', $wcArr[2])
                ->orWhere('product_master.product_type', '=', $wcArr[3]);
    }
    else{
      $query = $query->select($columnArr)->where('product_master.product_type', '=', $id);
    }

    $query = $query->where($this->discontinuedArr)
              ->groupBy('product_master.item_name') // issue with description
              ->orderBy('product_master.item_name', 'asc')
              ->simplePaginate(30); // ->get()

    return  $query;
  }

  public function getProductFilter($id, $filters, $type, $wcArr = array()){

    if ($id=='material'){

      $filters = str_replace("-", " ", $filters);
      // specialty -> inspired material
      $filters = str_replace("specialty", "Inspired Material", $filters);
    }else if ($id=='pattern'){
      $arr = explode("+", $filters);
      //print_r($arr); die();
      $str = '';
      foreach ($arr as $key => $value) {
        if(strtolower($value)=='large-scale-mural'){
          $val_1 = 'large-scale/mural';
        }
        else if(strtolower($value)=='animal-print'){
          $val_1 = 'animal print';
        }
        else{
          $val_1 = str_replace("-", "/", $value);
        }

        $str .= $val_1 . '+';
      }

      $filters = rtrim($str, "+");
    }

    $tnt = new TNTSearch;

    $filters = str_replace("+", ") or (", $filters);
    $filters = str_replace("/", " ", $filters);
    $filters = str_replace("-", " ", $filters);
    $filters = "(".$filters.")";

    $tnt->loadConfig(config("scout.tntsearch"));
    $tnt->selectIndex('individual-filter.index');
    //dd($filters);

    $res = $tnt->searchBoolean($filters, 1000);

    $query = DB::table('product_master')
             ->leftJoin('product_list', 'product_master.item_name', '=', 'product_list.fabricName')
             ->where('product_master.product_type', 'NOT LIKE', '%Faux Leather%')
             ->where('product_master.product_type', 'NOT LIKE', '')
             ->where($this->discontinuedArr);


    //dd($query->toSql());

    $items = $query->whereIn('id_pdf', $res['ids']);
    if($id !== "color") {
      $items = $items->groupBy('item_name');
    }
    $result = $items->orderBy('item_name')->simplePaginate(30);
    // dd($items);
    return $result;
  }

  public function getAllFilter($filterArr, $page){

    // dd($filterArr);
    $tnt = new TNTSearch;
    $filter = array();
    foreach($filterArr as $key => $filterStr) {
      if($filterStr == 'specialty') { $filterStr = "Inspired Material"; }
      $filterStr = str_replace(" ", ") or (", $filterStr);

      $filterStr = $this->getSearchString($key, $filterStr);
      $filterStr = str_replace("/", " ", $filterStr);
      $filterStr = str_replace("-", " ", $filterStr);

      if(strpos($filterStr, ") or (") !== false) {
        $filterStr = "(".$filterStr.")";
      }

      $filter[] = "(".$filterStr.")";
    }
    // dd($filter);
    $filterString = implode(' ', $filter);


    $tnt->loadConfig(config("scout.tntsearch"));
    $tnt->selectIndex("individual-filter.index");
    $res = $tnt->searchBoolean($filterString, 1000);

    $items = DB::table('product_master')
    ->leftJoin('product_list', 'product_master.item_name', '=', 'product_list.fabricName');

    $query = $items->whereIn('id_pdf', $res['ids'])->where('product_type', 'NOT LIKE', '%Faux Leather%')->where($this->discontinuedArr);

    if(count($filterArr) == 1 && (array_key_exists('collection', $filterArr) || array_key_exists('environmental', $filterArr) || array_key_exists('material', $filterArr) || array_key_exists('pattern', $filterArr) || array_key_exists('texture', $filterArr))) {
      $query = $items->whereIn('id_pdf', $res['ids'])->where('product_type', 'NOT LIKE', '%Faux Leather%')->where($this->discontinuedArr)->orderBy('item_name')->orderBy('item_number')->groupBy('item_name');
    }

    $query3 = clone $query;
    $query1 = $query->where('product_list.feature_display', '1')->orderBy('product_master.item_name')->get();
    $query2 = $query3->where('product_list.feature_display', '0')->orderBy('product_master.collection', 'DESC')->orderBy('product_master.item_name', 'DESC')->get();
    $query = $query1->merge($query2);
    return $query->slice(($page-1)*30, 30);

  }

  protected function getSearchString($id, $filter) {

    if ($id=='product_type'){

      $filter = str_replace("-", " ", $filter);
      // specialty -> inspired material
      $filter = str_replace("specialty", "Inspired Material", $filter);
    }else if ($id=='product_design'){
      $arr = explode("+", $filter);
      //print_r($arr); die();
      $str = '';
      foreach ($arr as $key => $value) {
        if(strtolower($value)=='large-scale-mural'){
          $val_1 = 'mural';
        }
        else if(strtolower($value)=='animal-print'){
          $val_1 = 'animal print';
        }
        else{
          $val_1 = str_replace("-", "/", $value);
        }

        $str .= $val_1 . '+';
      }

      $filter = rtrim($str, "+");
    }

    return $filter;
  }
}

/*
  $posts = Posts::join("post_views", "post_views.id_post", "=", "posts.id")
            ->where("created_at", ">=", date("Y-m-d H:i:s", strtotime('-24 hours', time())))
            ->groupBy("posts.id")
            ->orderBy(DB::raw('COUNT(posts.id)'), 'desc')//here its very minute mistake of a paranthesis in Jean Marcos' answer, which results ASC ordering instead of DESC so be careful with this line
            ->get([DB::raw('COUNT(posts.id) as total_views'), 'posts.*']);
*/
