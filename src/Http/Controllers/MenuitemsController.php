<?php

namespace bishopm\base\Http\Controllers;

use bishopm\base\Repositories\MenuitemsRepository;
use bishopm\base\Repositories\PagesRepository;
use bishopm\base\Models\Menuitem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use bishopm\base\Http\Requests\CreateMenuitemRequest;
use bishopm\base\Http\Requests\UpdateMenuitemRequest;
use Illuminate\Support\Facades\DB;

class MenuitemsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	private $menuitem, $pages;

	public function __construct(MenuitemsRepository $menuitem, PagesRepository $pages)
    {
        $this->menuitem = $menuitem;
        $this->pages = $pages;
    }

	public function index($menu)
	{
        $data['menuitems'] = $this->menuitem->all();
        $data['menu'];
   		return view('base::menuitems.index',$data);
	}

	public function edit($menu,Menuitem $menuitem)
    {
        $data['pages']=$this->pages->all();
        $data['items']=$this->menuitem->all();
        $data['menuitem']=$menuitem;
        return view('base::menuitems.edit', $data);
    }

    public function create($menu)
    {
        $data['pages']=$this->pages->all();
        $data['items']=$this->menuitem->all();
        $data['menu']=$menu;
        return view('base::menuitems.create',$data);
    }

    public function store(CreateMenuitemRequest $request)
    {
        $this->menuitem->create($request->all());

        return redirect()->route('admin.menus.edit',$request->menu_id)
            ->withSuccess('New menu item added');
    }

    public function update(Menuitem $menuitem, UpdateMenuitemRequest $request)
    {
        $this->menuitem->update($menuitem, $request->all());
        return redirect()->route('admin.menuitems.index')->withSuccess('Menuitem has been updated');
    }

    public function reorder(Request $request)
    {
        $items=json_decode($request->menu);
        foreach ($items as $key=>$item){
            $item1=$this->menuitem->find($item->id);
            $item1->parent_id=0;
            $item1->position=$key;
            $item1->save();
            if (isset($item->children)){
                foreach ($item->children as $key2=>$child){
                    $item2=$this->menuitem->find($child->id);
                    $item2->parent_id=$item->id;
                    $item2->position=$key2;
                    $item2->save();
                    if (isset($child->children)){
                        foreach ($child->children as $key3=>$grandchild){
                            $item3=$this->menuitem->find($grandchild->id);
                            $item3->parent_id=$child->id;
                            $item3->position=$key3;
                            $item3->save();
                        }
                    }
                }
            }
        }
        print "Done!";
    }

}
