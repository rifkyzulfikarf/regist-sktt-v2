<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\Mapmenu;
use App\Models\Menu;

class Unauthorized implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $map = new Mapmenu;
        $uri = explode('/', uri_string());
        // dd($uri);
        $list = $map->where('id_role',session()->get('id_role'))->get();
        $arr = [];
        foreach ($list->getResult() as $key => $value) {
            array_push($arr, $value->id_menu);
        }
        $menu = new Menu;
        $accessed= $menu->where('url',$uri[0])->get()->getRow();
        // dd(in_array($accessed->id, $arr));
        if (!in_array($accessed->id, $arr))
        {
            return redirect()
                ->to('/unauthorized');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}