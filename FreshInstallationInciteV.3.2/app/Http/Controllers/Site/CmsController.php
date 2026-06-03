<?php

namespace App\Http\Controllers\Site;
use App\Http\Controllers\Controller;
use App\Models\CmsContent; 
use Illuminate\Http\Request;

class CmsController extends Controller
{
    /**
     * Cms page.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request,$page)
    {        
        try{       
            $data['row'] = CmsContent::where('page_title',$page)->first(); 
            return view('site.cms.index',$data); 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
}
