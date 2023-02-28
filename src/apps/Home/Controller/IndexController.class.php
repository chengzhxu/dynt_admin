<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
	$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ } body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal;  } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style=""><img /src="/Public/Home/images/index.png" style="width:100%;height:100%;"></div><thinkad id="ad_55e75dfae343f5a1"></thinkad>','utf-8');
    }
}