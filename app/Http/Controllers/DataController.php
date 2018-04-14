<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;
use App\Base;
use App\Data;

class DataController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * 上传需要清洗的数据
     * 
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse| unknown
     */
    public function Upload(Request $request)
    {
        if ($request->isMethod('POST')) {
            $user = Auth::user();
            $datafile = $request->file('datafile');
            $referencefile = $request->file('referencefile');
            if ($datafile->isValid() && $referencefile->isValid()) {
                
                $dataOriginalName = $datafile->getClientOriginalName();
                $dataExt = $datafile->getClientOriginalExtension();
                $dataRealPath = $datafile->getRealPath();
                $dataFileName = 'data'.'-'.$user['id'].'-'.date('Y-m-d-H-i-S') . '-' . uniqid() . '.' . $dataExt;
                
                $referenceOriginalName = $referencefile->getClientOriginalName();
                $referenceExt = $referencefile->getClientOriginalExtension();
                $referenceRealPath = $referencefile->getRealPath();
                $referenceFileName = 'reference'.'-'.$user['id'].'-'.date('Y-m-d-H-i-S') . '-' . uniqid() . '.' . $referenceExt;
                
                if ($dataExt != 'arff' || $referenceExt != 'arff') {
                    echo Json::encode ( '格式非法' );
                    exit ();
                }
                
                $dataBool = Storage::disk('data')->put($dataFileName, file_get_contents($dataRealPath));
                $referenceBool = Storage::disk('data')->put($referenceFileName, file_get_contents($referenceRealPath));
                
                if ($dataBool == FALSE || $referenceBool == FALSE) {
                    echo Json::encode ( '文件保存失败' );
                    exit ();
                }
                $javaPath = '/usr/local/java/jdk-10/bin/java';
                $jarName = 'dtcleaner.jar';             
                $jarPath = base_path('storage/app/').$jarName;
                $dataPath = base_path('storage/app/data/').$dataFileName;
                $referencePath = base_path('storage/app/data/').$referenceFileName;
                $expPath = base_path('storage/app/exp/');
                $userPath = $user['id'].'-'.date('Y-m-d-H-i-S').uniqid();
                $outputPath = base_path('storage/app/exp/');
                exec('mkdir '.$outputPath.$userPath);
                exec('touch '.$outputPath.$userPath.'/output.txt');
                exec($javaPath.' -jar '.$jarPath.' '.$dataPath.' '.base_path('storage/app/data/').'CFDs '.$referencePath.' '.$userPath.' '.$expPath.' $s >> '.$outputPath.$userPath.'/output.txt &', $out, $ret);
                echo $javaPath.' -jar '.$jarPath.' '.$dataPath.' '.base_path('storage/app/data/').'CFDs '.$referencePath.' '.$userPath.' '.$expPath.' $s >> '.$outputPath.$userPath.'/output.txt &';
                $dataModel = new Data();
                $dataModel['userid'] = $user['id'];
                $dataModel['filename'] = $dataOriginalName;
                $dataModel['filepath'] = $userPath;
                $dataModel['report'] = '';
                $dataModel['status'] = FALSE;
                
                $dataModel->save();
                return redirect('/data/upload');
            } else {
                echo Json::encode ( '上传失败' );
                exit ();
            }
        } else if ($request->isMethod('GET')) {
            $user = Auth::user();
            $dataCollection = Data::where('userid', $user['id'])->get();
            $datas = Base::getOriginalArray ( $dataCollection );
            return view('data.upload')->with('datas', $datas);
        }
    }
    
    public function Down(Request $request) {
        $dataId =  $request->input('submit');
        $dataCollection = Data::where('id', $dataId)->get();
        $data = Base::getOriginalArray ( $dataCollection );
        return response()->download(base_path('storage/app/exp/'.$data[0]['filepath'].'.zip'));
    }
}
