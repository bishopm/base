<?php

namespace Bishopm\Connexion\Http\Controllers;

use App\Http\Controllers\Controller, Auth;
use Bishopm\Connexion\Mail\GenericMail;
use Bishopm\Connexion\Events\MessagePosted;
use Illuminate\Support\Facades\Mail;
use Bishopm\Connexion\Repositories\IndividualsRepository;
use Bishopm\Connexion\Repositories\GroupsRepository;
use Bishopm\Connexion\Repositories\MessagesRepository;
use Bishopm\Connexion\Http\Requests\MessageRequest;
use Bishopm\Connexion\Libraries\SMSfunctions, Bishopm\Connexion\Repositories\SettingsRepository;

class MessagesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    private $groups, $individuals, $settings, $messages;

    public function __construct(GroupsRepository $groups, IndividualsRepository $individuals, SettingsRepository $settings, MessagesRepository $messages)
    {
        $this->groups = $groups;
        $this->individuals = $individuals;
        $this->settings = $settings;
        $this->messages = $messages;
    }

	public function create()
	{
        $data['groups']=$this->groups->all();
        $data['individuals']=$this->individuals->all();
        $settings=$this->settings->makearray();
		if ($settings['sms_provider']=="bulksms"){
			$data['credits']=SMSfunctions::BS_get_credits($settings['sms_username'],$settings['sms_password']);
        }
   		return view('connexion::messages.create',$data);
	}

    public function store(MessageRequest $request)
    {
        $recipients=$this->getrecipients($request->groups,$request->individuals,$request->grouprec,$request->msgtype);
        if ($request->msgtype=="email"){
            //$results=$this->sendemail($request,$recipients);
            return view('connexion::messages.emailresults',compact('results'));
        } elseif ($request->msgtype=="sms"){
            //$results=$this->sendsms($request->smsmessage,$recipients);
            return view('connexion::messages.smsresults',compact('results'));
        } elseif ($request->msgtype=="app"){
            foreach ($recipients as $key=>$rec){
                $message = $this->messages->create(['user_id'=>Auth::user()->id, 'receiver_id'=>$key, 'message'=>$request->emailmessage, 'viewed'=>0]);
                event(new MessagePosted($message));
            }
        }
    }

    protected function getrecipients($groups,$individuals,$grouprec,$msgtype)
    {
        $recipients=array();
        if ($grouprec=="allchurchmembers"){
            $indivs=$this->individuals->allchurchmembers();
            foreach ($indivs as $indiv){
                if (((null !== $indiv->household->householdcell) and ($indiv->household->householdcell==$indiv->id)) or ($msgtype=="email")){
                    if ((($msgtype=="email") and ($indiv->email)) or (($msgtype=="sms") and ($indiv->cellphone))){
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    }
                }
            }
        } elseif ($grouprec=="everyone"){
            $indivs=$this->individuals->everyone();
            foreach ($indivs as $indiv){
                if (((null !== $indiv->household->householdcell) and ($indiv->household->householdcell==$indiv->id)) or ($msgtype=="email")){
                    if ((($msgtype=="email") and ($indiv->email)) or (($msgtype=="sms") and ($indiv->cellphone))){
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    }
                }
            }
        } else {
            if (null!==$groups){
                foreach ($groups as $group){
                    if ($grouprec=="leadersonly"){
                        $indiv=$this->individuals->find($this->groups->find($group)->leader);
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    } else {
                        $indivs=$this->groups->find($group)->individuals;
                        foreach ($indivs as $indiv){
                            $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                            $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                            $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                        }
                    }
                }
            }
            if (null!==$individuals){
                foreach ($individuals as $indiv){
                    $indi=$this->individuals->find($indiv);
                    $recipients[$indi->household_id][$indi->id]['name']=$indi->fullname;
                    $recipients[$indi->household_id][$indi->id]['email']=$indi->email;
                    $recipients[$indi->household_id][$indi->id]['cellphone']=$indi->cellphone;
                }
            }
        }
        return $recipients;
    }

    protected function sendemail($data,$recipients)
    {
        $results=array();
        foreach ($recipients as $household){
            foreach ($household as $indiv){
                $dum['name']=$indiv['name'];
                $dum['address']=$indiv['email'];
                if(filter_var($indiv['email'], FILTER_VALIDATE_EMAIL)) {
                    Mail::to($indiv['email'])->send(new GenericMail($data));
                    $dum['emailresult']="OK";
                }
                else {
                    $dum['emailresult']="Invalid";
                }
                $results[]=$dum;
            }
        }   
        return $results;
    }

    public function sendsms($message,$recipients)
	{
        $settings=$this->settings->makearray();
		if ($settings['sms_provider']=="bulksms"){
			$data['credits']=SMSfunctions::BS_get_credits($settings['sms_username'],$settings['sms_password']);
            $url = 'http://community.bulksms.com/eapi/submission/send_sms/2/2.0';
            $port = 80;
            if (count($recipients)>$data['credits']){
                return Redirect::back()->withInput()->withErrors("Insufficient Bulk SMS credits to send SMS");
            }
		} elseif ($settings['sms_provider']=="smsfactory"){
            // SMS Factory stuff
        }
        foreach ($recipients as $household){
            foreach ($household as $sms){
                $seven_bit_msg=$message . " (From " . $settings['site_abbreviation'] . ")";
                if ($settings['sms_provider']=="bulksms"){
                    $transient_errors = array(40 => 1);
                    $msisdn = "+27" . substr($sms['cellphone'],1);
                    $post_body = SMSfunctions::BS_seven_bit_sms($settings['sms_username'],$settings['sms_password'], $seven_bit_msg, $msisdn);
                }
                $dum2['name']=$sms['name'];
                if (SMSfunctions::checkcell($sms['cellphone'])){
                    if ($settings['sms_provider']=="bulksms"){
                        $dum2['smsresult'] = SMSfunctions::BS_send_message( $post_body, $url, $port );
                    } elseif ($settings['sms_provider']=="smsfactory"){
                        $dum2['smsresult'] = SMSfunctions::SF_sendSms($settings['sms_username'],$settings['sms_password'],$sms['cellphone'],$seven_bit_msg);
                    }
                    $dum2['address']=$sms['cellphone'];
                } else {
                    if ($sms['cellphone']==""){
                        $dum2['address']="No cell number provided.";
                    } else {
                        $dum2['address']="Invalid cell number: " . $sms['cellphone'] . ".";
                    }
                }
                $results[]=$dum2;
            }
            $data['results']=$results;
            $data['type']="SMS";
        }
        return $data['results'];
    }
}