<?php

namespace Bishopm\Connexion\Http\Controllers;

use Bishopm\Connexion\Repositories\SettingsRepository;
use Bishopm\Connexion\Repositories\SocietiesRepository;
use Bishopm\Connexion\Repositories\GroupsRepository;
use Bishopm\Connexion\Models\Setting;
use Bishopm\Connexion\Models\User;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\Controller;
use Bishopm\Connexion\Http\Requests\CreateSettingRequest;
use Bishopm\Connexion\Http\Requests\UpdateSettingRequest;
use Spatie\Analytics\Period;
use Analytics;

class SettingsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $setting,$societies,$groups;

    public function __construct(SettingsRepository $setting, SocietiesRepository $societies, GroupsRepository $groups)
    {
        $this->setting = $setting;
        $this->societies = $societies;
        $this->groups = $groups;
    }

    public function index()
    {
        $allset = $this->setting->allsettings();
        foreach ($allset as $setting){
            $settings[$setting['category']][]=$setting;
        }
        ksort($settings);
        return view('connexion::settings.index',compact('settings'));
    }

    public function modulesindex()
    {
        $modules = $this->setting->allmodules();
        return view('connexion::settings.modules',compact('modules'));
    }

    public function modulestoggle($module)
    {
        $modules = $this->setting->allmodules();
        $module = $this->setting->find($module);
        if ($module->setting_value=="yes"){
            $module->setting_value="no";
        } else {
            $module->setting_value="yes";
        }
        $module->save();
        return redirect()->route('admin.modules.index');
    }    

    public function edit(Setting $setting)
    {
        if ($setting->setting_key=="society_name"){
            $vals=$this->societies->dropdown();
            $dropdown=array();
            foreach ($vals as $val){
                $dum[0]=$val->id;
                $dum[1]=$val->society;
                $dropdown[]=$dum;
            }
        } elseif ($setting->setting_key=="pastoral_group"){
            $vals=$this->groups->dropdown();
            $dropdown=array();
            foreach ($vals as $val){
                $dum[0]=$val->id;
                $dum[1]=$val->groupname;
                $dropdown[]=$dum;
            }
        } elseif ($setting->setting_key=="sms_provider"){
            $dropdown[0][0]="bulksms";
            $dropdown[0][1]="bulksms";
            $dropdown[1][0]="smsfactory";
            $dropdown[1][1]="smsfactory";
        } else {
            $dropdown='';
        }
        return view('connexion::settings.edit', compact('setting','dropdown'));
    }

    public function userlogs(){
        $activities=Activity::all();
        foreach ($activities as $activity){
            $user=User::find($activity->causer_id);
            if ($user){
                $name=$user->individual->firstname . " " . $user->individual->surname;
            } else {
                $name="System";
            }
            if ($activity->subject_type){
                $obj=$activity->subject_type::find($activity->subject_id);
                $object=substr($activity->subject_type,1+strrpos($activity->subject_type,'\\'));
                $details=$name . " " . $activity->description . " " . strtolower($object) . " (";
                if ($activity->description<>"deleted"){
                    if ($object=="Group") {
                        $details.=$obj->groupname . ")";
                    } elseif ($object=="Individual"){
                        $details.=$obj->firstname . " " . $obj->surname . ")";
                    } elseif ($object=="Household"){
                        $details.=$obj->addressee . ")";
                    } elseif ($object=="Song"){
                        $details.=$obj->title . ")";
                    }
                } else {
                    $details.=$activity->subject_id . ")";
                }
            } else {
                $details=$name . " " . $activity->description;
            }
            $activity->details=$details;
        }
        return view('connexion::settings.userlogs',compact('activities'));
    }

    public function create()
    {
        return view('connexion::settings.create');
    }

    public function store(CreateSettingRequest $request)
    {
        $this->setting->create($request->all());

        return redirect()->route('admin.settings.index')
            ->withSuccess('New setting added');
    }
    
    public function update(Setting $setting, UpdateSettingRequest $request)
    {
        $this->setting->update($setting, $request->all());
        return redirect()->route('admin.settings.index')->withSuccess('Setting has been updated');
    }

    public function analytics()
    {
        $anal=Analytics::fetchMostVisitedPages(Period::days(7),75);
        $analytics=array();
        foreach ($anal as $ana){
            $url=$ana['url'];
            if (array_key_exists($url, $analytics)){
                $analytics[$url]=$analytics[$url]+$ana['pageViews'];
            } else {
                $analytics[$url]=$ana['pageViews'];
            }
        }
        arsort($analytics);
        return view('connexion::settings.analytics', compact('analytics'));   
    }

}