<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Specs;
use App\PostView;
use App\ProductMaster;
use App\Http\Utils\Namefix;
use Image;

use SEOMeta;
use OpenGraph;
use JsonLd;
use Twitter;

use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Gate;

class SpecsController extends Controller
{
    public function index($request, $postView, $specs, $id){



        $nameFix = new Namefix();

        $id = $nameFix->dbName($id);

        //$productTitle = 'ALL PRODUCTS';
        $productTitle = $nameFix->productTitle($id);

        $columnArr = array('item_name', 'date_introduced');

        // $page = isset($request->page) ? $request->page : 0;

        $prodObj = $specs->iconsResult($id,$columnArr);

        $post = $specs;
        $post->post_id = 'product';
        $post->titleslug = $id;
        $postView->createViewLog($post);

        //print_r($id); die();

        $type = 'product'; // important for Plp.js, line 427

        $productArr = array();
        foreach ($prodObj as $key => $value) {

            $main_img = "";
            $imgUrl = config('constants.value.imgUrl');
            $ver = config('constants.value.VER');

            if (isset($value->productList->main_img)) {
            $main_img = $value->productList->main_img;
            }

            $Agent = new Agent();
            if ($Agent->isMobile()) {
            // // you're a mobile device
            $jpgName1 = $imgUrl . '/storage/product/150x150/' .$nameFix->jpgName($value->item_name). '.jpg?v=' . $ver;
            $mainImage = $nameFix->thumbImageName($value->main_img, 'medium');

            }
            else {
            // you're a desktop device, or something similar
            $jpgName1 = $imgUrl . '/storage/product/350x350/' .$nameFix->jpgName($value->item_name). '.jpg?v=' . $ver;
            $mainImage = $value->main_img;
            }

            $productArr[]= array(
              'type' => $type,
              'jpgName1' => $jpgName1,
              'urlName1' => $nameFix->urlName($value->item_name),
              'displayName1' => $nameFix->displayName($value->item_name),
              'dbName' => $nameFix->dbName($value->item_name),
              'mainImage' => $mainImage,
              'mainImageThumb' => $nameFix->thumbImageName($value->main_img, 'medium')
            );
        }

        $agent = new Agent();
        $mobile = $agent->isMobile();

        $returnArr = [
            'pageId' => $id,
            'productTitle'=>$productTitle,
            'mainArr'=>$productArr,
            'mobile'=>$mobile,
        ];

        $description = "Search Innovations Wallcovering or Textile Products with ".$id;
        $seoKeywords = "$id"." Innovations in Wallcoverings";

        $lazyLoad = '';
        $obj = json_decode( $request->getContent() );

        if($obj)
          $lazyLoad = $obj->lazyload;

        if($lazyLoad=='true'){
          return $returnArr;
        } else{
          SEOMeta::setTitle("Shop Our Wallcoverings by ".ucfirst($id));
          SEOMeta::setDescription($description);
          SEOMeta::addKeyword($seoKeywords);

          OpenGraph::setDescription($description);
          OpenGraph::setTitle($id);
          OpenGraph::setUrl(url()->current());
          OpenGraph::addProperty('type', 'product');
          OpenGraph::addProperty('locale', 'en-US');

          Twitter::setTitle($id);
          Twitter::setSite('@InnovationsUSA');

          JsonLd::setTitle($id);
          JsonLd::setDescription($description);
          JsonLd::setType('Product');
          return view('specs-page', $returnArr);

        }

    }


    public function iconsList(Request $request, PostView $postView, Specs $product,$type, $id){
        $nameFix = new Namefix();

        $id = $nameFix->dbName($id);
        return $this->index($request, $postView, $product, $id);
    }
}
