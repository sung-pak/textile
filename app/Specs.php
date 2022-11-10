<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductMaster;

class Specs extends Model
{
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
    //
    public function iconsResult($searchStr, $columnArr)
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        $url = parse_url($referer, $component = -1);
        $path = $url['path'];
        $path = explode('/', $path);
        if(count($path) == 3&& ($path[1] == 'item' || $path[1] == 'product')) {
          $omit = $path[2];
        } else {
          $omit = '';
        }
        $items = [];

        if ($searchStr == "phthalate free") {
            $items = ProductMaster::select($columnArr)->where('env_phthalate_free_vinyl', 1)->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "seamless") {
            $items = ProductMaster::select($columnArr)->where('tech_seaming', 'Seamless')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
            
        } else if ($searchStr == "minimal seams") {
            $items = ProductMaster::select($columnArr)->where('tech_seaming', 'Minimal Seams')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "noticeable seams") {
            $items = ProductMaster::select($columnArr)->where('tech_seaming', 'Noticeable Seams')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "natural woven") {
            $items = ProductMaster::select($columnArr)->where('product_type', 'Natural Woven')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "textile wallcovering") {
            $items = ProductMaster::select($columnArr)->where('product_type', 'Textile Wallcovering')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "vinyl") {
            $items = ProductMaster::select($columnArr)->where('product_type', 'Vinyl')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "inspired material") {
            $items = ProductMaster::select($columnArr)->where('product_type', 'Inspired Material')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "low traffic") {
            $items = ProductMaster::select($columnArr)->where('usage', 'Low Traffic')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "medium traffic") {
            $items = ProductMaster::select($columnArr)->where('usage', 'Medium Traffic')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "high traffic") {
            $items = ProductMaster::select($columnArr)->where('usage', 'High Traffic')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "upholstery") {
            $items = ProductMaster::select($columnArr)->where('product_type', 'Faux Leather')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "tech typeii") {
            $items = ProductMaster::select($columnArr)->where('tech_type_ii', 1)->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "eco friendly") {
            $items = ProductMaster::select($columnArr)->where(function ($query) {
                $query->where('env_fsc_certified_paper', 1);
                $query->orWhere('env_rapidly_renewable', 1);
                $query->orWhere('env_recycled_backing', 1);
                $query->orWhere('env_recycled_content_by_weight', 1);
                $query->orWhere('env_natural_nonsynthetic', 1);
            })->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "strata") {
            $items = ProductMaster::select($columnArr)->where('item_name', 'Strata')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        } else if ($searchStr == "harlequin") {
            $items = ProductMaster::select($columnArr)->where('item_name', 'Harlequin')->where($this->discontinuedArr)->whereNotIn('item_name', [$omit])->groupBy('item_name')->orderBy('collection', 'DESC')->orderBy('item_name')->orderBy('item_number')->simplePaginate(30);
        }

// dd($searchStr);
        return $items;

    }
}
