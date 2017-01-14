<?php

namespace bishopm\base\Http\Controllers;

use Illuminate\Http\Request, bishopm\base\Models\Gchord;
use App\Http\Requests, bishopm\base\Models\User, bishopm\base\Models\Song, Auth, bishopm\base\Models\Set, bishopm\base\Models\Setitem, View, Redirect, Fpdf;
use App\Http\Controllers\Controller, bishopm\base\Http\Requests\SongsRequest;

class SongsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['lets']=array('1'=>'A','2'=>'B','3'=>'C','4'=>'D','5'=>'E','6'=>'F','7'=>'G','8'=>'H','9'=>'I','10'=>'J','11'=>'K','12'=>'L','13'=>'M','14'=>'N','15'=>'O','16'=>'P','17'=>'Q','18'=>'R','19'=>'S','20'=>'T','21'=>'U','22'=>'V','23'=>'W','24'=>'X','25'=>'Y','26'=>'Z');
        $data['songs']=Song::orderBy('title')->get();
        $data['songcount']=count($data['songs']);
        $lasthree=date("Y-m-d",strtotime("-3 month"));
        $recents=Set::with('setitems')->where('servicedate','>',$lasthree)->get();
        $arecents=array();
        $newest=array();
        $mostrecentset=0;
        foreach ($recents as $recent){
            if (!array_key_exists($recent->service->service,$arecents)){
                $arecents[$recent->service->service]=array();
            }
            foreach ($recent->setitems as $si){
                if (array_key_exists($si->song_id,$arecents[$recent->service->service])){
                    $arecents[$recent->service->service][$si->song_id]['count']++;
                } else {
                    $arecents[$recent->service->service][$si->song_id]['count']=1;
                    $arecents[$recent->service->service][$si->song_id]['title']=$si->song->title;
                    $arecents[$recent->service->service][$si->song_id]['musictype']=$si->song->musictype;
                    $arecents[$recent->service->service][$si->song_id]['id']=$si->song_id;
                }
                $newest[strtotime($recent->servicedate)][$recent->service->service][$si->song_id]['title']=$si->song->title;
                $newest[strtotime($recent->servicedate)][$recent->service->service][$si->song_id]['musictype']=$si->song->musictype;
                $newest[strtotime($recent->servicedate)][$recent->service->service][$si->song_id]['id']=$si->song_id;
                if (strtotime($recent->servicedate)>$mostrecentset){
                    $mostrecentset=strtotime($recent->servicedate);
                }
            }
        }
        krsort($newest);
        $newestsets=reset($newest);
        if ($newestsets){
            ksort($newestsets);
        } else {
            $newestsets=array();
        }
        $data['newestsets']=$newestsets;
        if ($arecents){
            ksort($arecents);
        }
        foreach ($arecents as $key=>$arecent){
            arsort($arecent);
            $data['recents'][$key][]=array_splice($arecent,0,20);
        }
        $data['mostrecentset']=date("d F Y",$mostrecentset);
        $data['newest']=Song::orderBy('created_at', 'DESC')->get()->take(9);
        $data['users']=User::orderBy('name','DESC')->get();
        return View::make('base::songs.index', $data);
    }

    public function search($q='')
	{
		$filtered=Song::where('title','like','%' . $q . '%')->orwhere('words','like','%' . $q . '%')->orwhere('author','like','%' . $q . '%')->whereNull('deleted_at')->select('title','id','musictype')->orderBy('title')->get();
		return $filtered;
	}

    public function convert($lyrics=""){
        $keys=array('A','B','C','D','E','F','G');
        $lines=explode("\r\n",$lyrics);
        $newlines=array();
        $finchord=array();
        foreach ($lines as $line){
            if (substr($line,0,1)=="["){
                $line=str_replace('[','{',$line);
                $line=str_replace(']','}',$line);
                $newlines[]=$line . "\r\n";;
                $chords=array();
            } elseif ((substr($line,0,1)==".") and (strlen($line)>1)){
                $prevpos=0;
                $chords=str_split(substr($line,1));
                $slash=false;
                $finchord=array();
                foreach ($chords as $pos=>$chord){
                    if ((!$slash) and (in_array($chord,$keys))){
                        $finchord[$pos]=$chord;
                        $prevpos=$pos;
                    } elseif ($chord<>" ") {
                        $finchord[$prevpos]=$finchord[$prevpos] . $chord;
                        if ($chord=="/"){
                            $slash=true;
                        } else {
                            $slash=false;
                        }
                    }
                }
                if (isset($finchord)){
                    $chords=$finchord;
                }
            } elseif (substr($line,0,1)==" ") {
                $line=substr($line,1);
                if ((isset($chords)) and (count($chords)<>0)){
                    $running=0;
                    foreach ($chords as $pos=>$chord){
                        $line=substr($line,0,$pos+$running) . "[" . $chord . "]" . substr($line,$pos+$running);
                        $running=$running+strlen($chord)+2;
                    }
                }
                $chords=array();
                $newlines[]=$line . "\r\n";
            } else {
                $newlines[]=$line . "\r\n";
            }
        }
        return implode($newlines);
    }

    public function pdf($dat)
    {
        $pdf = new Fpdf();
        $pdf->AddPage('P');
        $logopath=base_path() . '/public/images/chords/';
        $pdf->SetAutoPageBreak(true,0);
        $pdf->SetFont('Courier','B',14);
        $pdf->text(20,16,$dat['song']->title);
        $pdf->SetFont('Courier','I',10);
        $pdf->text(20,22,$dat['song']->author);
        $pdf->SetFont('Courier','',10);
        $pdf->text(185,16,'Key: ' . $dat['song']->key);
        $pdf->text(190,22,$dat['song']->tempo);
        $pdf->line(20,26,200,26);
        $x=20;
        $lines=explode(PHP_EOL,$dat['song']->lyrics);
        $y=34;
        foreach ($lines as $line){
            if (strpos($line,'}')){
                $line=str_replace('{','',$line);
                $line=str_replace('}','',$line);
                $pdf->SetFont('Courier','B',12);
                $pdf->SetTextColor(160,160,160);
                $y=$y+3.5;
                $pdf->text(13,$y,substr($line,0,2));
                $pdf->SetTextColor(0,0,0);
                $y=$y-3.5;
            } else {
                $pdf->SetFont('Courier','',12);
                if (strpos($line,']')){
                    $y=$y+3.5;
                }
                $x=20;
                $ch=0;
                for ($i=0; $i<strlen($line); $i++) {
                    if ($line[$i]=='['){
                        $y=$y-3.5;
                        $chordsub=substr($line,$i);
                        $chor=substr($chordsub,1,-1+strpos($chordsub,']'));
                        $pdf->SetFont('Courier','B',12);
                        $pdf->text($x,$y,$chor);
                        $ch=1;
                        $pdf->SetFont('Courier','',12);
                        $i=$i+strlen($chor)+1;
                        $y=$y+3.5;
                    } else {
                        $pdf->text($x,$y,$line[$i]);
                        $x=$x+$pdf->GetStringWidth($line[$i]);
                    }
                }
            }
            $y=$y+3.5;
        }
        if ($y<260){
            if (count($dat['chords'])>1){
                $pdf->line(20,$y+2,200,$y+2);
                $x=20;
                $pdf->SetFont('Courier','',8);
                foreach ($dat['chords'] as $nn=>$chord){
                    if (($nn<>0) and ($nn % 10 == 0)){
                        $x=20;
                        $y=$y+20;
                    }
                    if (isset($chord['id'])){
                        $pdf->Image($logopath . $chord['id'] . '.png',$x,$y+6,15);
                    } else {
                        $pdf->setxy($x,$y+6);
                        $pdf->cell(15,5,str_replace('_','/',$chord),0,0,'C');
                    }
                    $x=$x+17;
                }
            }
        }
        $pdf->Output();
        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['keys']=array('A','Bb','B','C','C#','D','Eb','E','F','F#','G','G#');
        $data['tempos']=array('4/4','3/4','6/8');
        return View::make('base::songs.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SongsRequest $request)
    {
        $song=Song::create($request->all());
        if (strpos($request->lyrics,']---')) {
            $song->lyrics=str_replace('---[','{',$request->lyrics);
            $song->lyrics=str_replace(']---','}',$song->lyrics);
            $song->lyrics=str_replace('{Verse:','{V',$song->lyrics);
            $song->lyrics=str_replace('{Chorus:','{C',$song->lyrics);
            $song->lyrics=str_replace('{Pre-Chorus:','{P',$song->lyrics);
            $song->lyrics=str_replace('{Bridge:','{B',$song->lyrics);
            $song->lyrics=str_replace('{Ending:1}','',$song->lyrics);
        } elseif (!strpos($request->lyrics,'}')){
            $song->lyrics=$this->convert($request->lyrics);
        }
        $song->lyrics=preg_replace("/[\r\n]+/", "\n", $song->lyrics);
        $song->audio=str_replace("dropbox.com","dl.dropboxusercontent.com",$song->audio);
        $song->music=str_replace("www.","",$song->music);
        $song->music=str_replace("dropbox.com","dl.dropboxusercontent.com",$song->music);
        $song->video=str_replace("www.youtube.com/watch?v=","www.youtube.com/embed/",$song->video);
        $song->user_id=auth()->user()->id;
        $song->save();
        return Redirect::route('base::songs.index')->with('okmessage','New song has been added');
    }

    private function _getChords($lyrics){
        preg_match_all("/\[([^\]]*)\]/", $lyrics, $matches);
        $chords=array_unique($matches[1]);
        asort($chords);
        if (count($chords)){
            return $chords;
        } else {
            return "";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$mode="view")
    {
        $history=Setitem::with('set')->where('song_id','=',$id)->get();
        foreach ($history as $hist){
            $dhistory[$hist->set->service->service][]=$hist->set->servicedate;
        }
        if (isset($dhistory)){
            ksort($dhistory);
            foreach($dhistory as $kh=>$kc){
                arsort($kc);
                $data['history'][$kh]=$kc;
            }
        }
        $data['song']=Song::find($id);
        $data['lyrics']=$data['song']->lyrics;
        $op=preg_replace('/\[[^\[\]]*\]/', '', $data['song']->lyrics);
        $op=str_replace('{','---[',$op);
        $op=str_replace('}',']---',$op);
        $op=str_replace('[V','[Verse:',$op);
        $op=str_replace('[Verse:]','[Verse:1]',$op);
        $op=str_replace('[C','[Chorus:',$op);
        $op=str_replace('[Chorus:]','[Chorus:1]',$op);
        $op=str_replace('[B','[Bridge:',$op);
        $op=str_replace('[Bridge:]','[Bridge:1]',$op);
        $op=str_replace('[P','[Pre-Chorus:',$op);
        $op=str_replace('[Pre-Chorus:]','[Pre-Chorus:1]',$op);
        $data['openlp']=$op;
        $chords=$this->_getChords($data['song']->lyrics);
        if ($chords<>""){
            foreach ($chords as $chord){
                $tc=Gchord::where('chordname','=',$chord)->first();
                if (count($tc)){
                    $data['chords'][]=$tc;
                } else {
                    $data['chords'][]=$chord;
                }
            }
        } else {
            $data['chords']=array();
        }
        $data['keys']=array('A','Bb','B','C','C#','D','Eb','E','F','F#','G','G#');
        $data['tempos']=array('4/4','3/4','6/8');
        if ($mode=="view"){
            return View::make('base::songs.show',$data);
        } elseif ($mode=="slim") {
            return $data['song'];
        } else {
            $this->pdf($data);
        }
    }

    public function showapi($id)
    {
        $data['song']=Song::find($id);
        $data['brlyrics']="";
        $lines=explode(PHP_EOL,$data['song']->lyrics);
        foreach ($lines as $line){
            $line=str_replace("\r",'',$line);
            $line=str_replace("\n",'',$line);
            if (strpos($line,']')){
                $line="<chordline>" . $line . "</chordline>";
                $line=str_replace('[','<chord>',$line);
                $line=str_replace(']','</chord>',$line);
            }
            $line = $line . "<br>";
            $line=str_replace('{','<strong>',$line);
            $line=str_replace('}','</strong>',$line);
            $data['brlyrics'] = $data['brlyrics'] . $line;
        }
        return $data;
    }

    private function _moveOne($keys,$chord,$direction)
    {
        if (($chord=="A") and ($direction=="down")){
            $ndx=11;
        } elseif (($chord=="A") and ($direction=="up")){
            $ndx=1;
        } elseif ($direction=="up") {
            $ndx=array_search($chord,$keys)+1;
        } elseif ($direction=="down") {
            $ndx=array_search($chord,$keys)-1;
        } else {
            $ndx=array_search($chord,$keys);
        }
        return $keys[$ndx];
    }

    private function _transpose($chords,$direction)
    {
        $keys=array('A','Bb','B','C','C#','D','Eb','E','F','F#','G','G#','A');
        $newchords=array();
        foreach ($chords as $chord){
            if ((strpos($chord,"#")==1) or (strpos($chord,'b'))==1){
                $chordpart=substr($chord,0,2);
            } else {
                $chordpart=substr($chord,0,1);
            }
            $newchordpart=$this->_moveOne($keys,$chordpart,$direction);
            if (strlen($chord)>strlen($chordpart)){
                $chordrem=substr($chord,strlen($chordpart));
            } else {
                $chordrem="";
            }
            if ((strpos($chordrem,'/')===0) or (strpos($chordrem,'/')>0)){
                $newbassnote=$this->_moveOne($keys,substr($chordrem,1),$direction);
                $chordrem=substr($chordrem,0,strpos($chordrem,'/')) . "/" . $newbassnote;
            }
            $newchords[]=$newchordpart . $chordrem;
        }
        return $newchords;
    }

    private function _transposeLyrics($lyrics,$direction)
    {
        $lyrics=str_replace('[A#','[Bb',$lyrics);
        $lyrics=str_replace('[Db','[C#',$lyrics);
        $lyrics=str_replace('[D#','[Eb',$lyrics);
        $lyrics=str_replace('[Gb','[F#',$lyrics);
        $lyrics=str_replace('[Ab','[G#',$lyrics);
        $chords=$this->_getChords($lyrics);
        $newchords=$this->_transpose($chords,$direction);
        foreach ($chords as $key=>$chord){
            $lyrics=str_replace('[' . $chord . ']', '$' . array_shift($newchords) . '%', $lyrics);
        }
        $lyrics=str_replace('$','[',$lyrics);
        $lyrics=str_replace('%',']',$lyrics);
        return $lyrics;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['song']=song::find($id);
        $data['keys']=array('A','Bb','B','C','C#','D','Eb','E','F','F#','G','G#');
        $data['tempos']=array('4/4','3/4','6/8');
        $data['chords']=$this->_getChords($data['song']->lyrics);
        return View::make('base::songs.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SongsRequest $request, $id)
    {
        $keys=array('A','Bb','B','C','C#','D','Eb','E','F','F#','G','G#','A');
        $song=Song::find($id);
        $song->fill($request->except('transpose'));
        $song->key=$this->_moveOne($keys,$request->key,strtolower($request->transpose));
        if (!strpos($request->lyrics,'}')){
            $song->lyrics=$this->convert($song->lyrics);
        }
        if (isset($request->transpose)){
            $song->lyrics=$this->_transposeLyrics($request->lyrics,strtolower($request->transpose));
        }
        $song->words=preg_replace('/\[[^\[\]]*\]/', '', $song->lyrics);
        $song->audio=str_replace("dropbox.com","dl.dropboxusercontent.com",$song->audio);
        $song->music=str_replace("www.","",$song->music);
        $song->music=str_replace("dropbox.com","dl.dropboxusercontent.com",$song->music);
        $song->video=str_replace("www.youtube.com/watch?v=","www.youtube.com/embed/",$song->video);
        $song->save();
        $data['lyrics']=$song->lyrics;
        $data['key']=$song->key;
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $song = song::find($id);
        $song->delete();
        return Redirect::route('songs.index')->with('okmessage','Song has been deleted');
    }
}
