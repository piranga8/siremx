<?php

namespace App\Http\Controllers\Exam;

use App\Exam;
use App\Patient;
use App\Load;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function getExamById(Request $request)
    {
       if(!$request->ajax()) return redirect('/');
    
       $idExam  = $request->idExam;
       $idExam  = ($idExam == NULL) ? ($idExam = '') : $idExam;

       //dd($idExam );
       
       $exam = Exam::Where('id','=',$idExam)->get();

       return $exam;
    }

    public function setEditExam(Request $request)
    {
       if(!$request->ajax()) return redirect('/');

       $idExam                = $request->idExam;
       $servicioSalud         = $request->servicioSalud;
       $commune               = $request->commune; 
       $establishmentRequest  = $request->establishmentRequest;
       $date_exam_order       = $request->date_exam_order;
       $establishmentExam     = $request->establishmentExam;
       $doctor                = $request->doctor;
       $date_exam             = $request->date_exam;
       $derivation            = $request->derivation;
       $examType              = $request->examType;
       $birards               = $request->birards;
       $professional          = $request->professional;
       $listBIRADSEcoMam      = $request->listBIRADSEcoMam;
       $date_exam_reception   = $request->date_exam_reception;
       $diagnostic            = $request->diagnostic;

       
       

       if($examType == 'mam'){
            $birards_mam = $birards;
       }
       else {
            $birards_mam = NULL;
       }
       if($examType == 'eco'){
            $birards_eco = $birards;
       } 
       else {
            $birards_eco = NULL;
       } 
       if($examType == 'pro'){
            $birards_pro = $birards;
       }
       else {
            $birards_pro = NULL;
       }

       $idExam               = ($idExam == NULL) ? ($idExam = 0) : $idExam;
       $servicioSalud        = ($servicioSalud == NULL) ? ($servicioSalud = '') : $servicioSalud;
       $commune              = ($commune == NULL) ? ($commune = '') : $commune;
       $establishmentRequest = ($establishmentRequest == NULL) ? ($establishmentRequest = '') : $establishmentRequest;
       $date_exam_order      = ($date_exam_order == NULL) ? ($date_exam_order = NULL) : $date_exam_order; // SE QUITO NULL
       $establishmentExam    = ($establishmentExam == NULL) ? ($establishmentExam = '') : $establishmentExam;
       $doctor               = ($doctor == NULL) ? ($doctor = '') : $doctor;
       $date_exam            = ($date_exam == NULL) ? ($date_exam = NULL) : $date_exam;
       $derivation           = ($derivation == NULL) ? ($derivation = '') : $derivation;
       $date_exam_reception  = ($date_exam_reception ==  NULL) ? ($date_exam_reception = NULL) : $date_exam_reception;
       $diagnostic           = ($diagnostic == NULL) ? ($diagnostic = '') : $diagnostic;
       $professional         = ($professional == NULL) ? ($professional = '') : $professional;

       $exam = Exam::find($idExam);
       $exam->servicio_salud       = $servicioSalud;
       $exam->comuna               = $commune;
       $exam->cesfam               = $establishmentRequest;
       $exam->date_exam_order      = $date_exam_order;
       $exam->establecimiento_realiza_examen   = $establishmentExam;
       $exam->profesional_solicita               = $professional;
       $exam->medico               = $doctor;
       $exam->date_exam            = $date_exam;
       $exam->derivation_reason    = $derivation;
       $exam->birards_mamografia   = $birards_mam;
       $exam->birards_ecografia    = $birards_eco;
       $exam->birards_proyeccion   = $birards_pro;
       $exam->date_exam_reception  = $date_exam_reception;
       $exam->diagnostico          = $diagnostic;
       //dd($exam->filename);
       if($request->hasFile('file')){
        $file                  = $request->file;
        $flag                  = $idExam;
        $filename              = $file->getClientOriginalName();
        $fileserver            = $flag.'_'.$filename;
        Storage::delete('public/reports/'.$flag.'_'.$exam->filename);
        $exam->path                 = asset('storage/reports/'.$fileserver);
        $exam->filename             = $filename;
        Storage::putFileAs('public/reports',$file, $fileserver);
       }
       else {
        $exam->path                 = $exam->path;
        $exam->filename             = $exam->filename;
       }

       //$exam->updated_at           = date("Y-m-d");
       $exam->save();


       return $exam;
    }

    public function getListExams(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
    
        $cName           = $request->cName;
        $cFathers_family = $request->cFathers_family;
        $nRun            = $request->nRun;
        $code_deis_request  = $request->codeDeisRequest;


        $cName           = ($cName == NULL) ? ($cName = '') : $cName;
        $cFathers_family = ($cFathers_family == NULL) ? ($cFathers_family = '') : $cFathers_family;
        $nRun            = ($nRun == NULL) ? ($nRun = '') : $nRun;

        if($code_deis_request == NULL) {
            if(Auth::user()->establishment_id){
              $code_deis_request = Auth::user()->establishment_id;//"AND T0.cesfam = ".Auth::user()->establishment_id;
            }
            else {
              $code_deis_request = '';
            }
         }


       
        if($cName || $cFathers_family || $nRun ){

            $patients_list = Patient::Where('run','LIKE','%'.$nRun.'%')
                                ->Where('name','LIKE','%'.$cName.'%')
                                ->Where('fathers_family','LIKE','%'.$cFathers_family.'%')
                                ->get('id');
            

            $exams = Exam::select(
                                  'exams.id'
                                 ,'exams.date_exam'
                                 ,'exams.date_exam'
                                 ,'exams.date_exam_reception'
                                 ,'exams.exam_type'
                                 ,'exams.birards_mamografia'
                                 ,'exams.birards_ecografia'
                                 ,'exams.birards_proyeccion'
                                 ,'exams.user_id'
                                 ,'exams.path'
                                 ,'T1.run'
                                 ,'T1.dv'
                                 ,'T1.run'
                                 ,'T1.name'
                                 ,'T1.fathers_family'
                                 ,'T1.mothers_family'
                                 ,'T1.birthday'
                                 ,'T2.alias AS establishment_origin'
                                 ,'T3.name AS commune'
                                 ,'T4.firstname'
                                 ,'T4.secondname'
                                 ,'T4.lastname')
                    ->leftjoin('patients AS T1', 'exams.patient_id', '=', 'T1.id')
                    ->leftjoin('establishments AS T2', 'exams.cesfam', '=', 'T2.new_code_deis')
                    ->leftjoin('communes AS T3', 'exams.comuna', '=', 'T3.code_deis')
                    ->leftjoin('users AS T4', 'exams.user_id', '=', 'T4.id')
                    ->whereIn('patient_id',$patients_list)
                    ->get();
        }
        else {
            $exams = Exam::select(
                                 'exams.id'
                                ,'exams.date_exam'
                                ,'exams.date_exam'
                                ,'exams.date_exam_reception'
                                ,'exams.exam_type'
                                ,'exams.user_id'
                                ,'T1.run'
                                ,'T1.dv'
                                ,'T1.run'
                                ,'T1.name'
                                ,'T1.fathers_family'
                                ,'T1.mothers_family'
                                ,'T1.birthday'
                                ,'T2.alias AS establishment_origin'
                                ,'T3.name AS commune'
                                ,'T4.firstname'
                                ,'T4.secondname'
                                ,'T4.lastname')
                    ->leftjoin('patients AS T1', 'exams.patient_id', '=', 'T1.id')
                    ->leftjoin('establishments AS T2', 'exams.cesfam', '=', 'T2.new_code_deis')
                    ->leftjoin('communes AS T3', 'exams.comuna', '=', 'T3.code_deis')
                    ->leftjoin('users AS T4', 'exams.user_id', '=', 'T4.id')
                    ->whereNull('date_exam_reception')
                    ->Where('load_source','app')
                    ->Where('cesfam','LIKE','%'.$code_deis_request.'%')
                    ->orderBy('id','DESC')
                    ->take(1200)->get();
        }

       // Se obtiene el listado de Id de pacientes conforme el request.
        

       return $exams;
    }

    public function setStoreExam(Request $request)
    {
       if(!$request->ajax()) return redirect('/');
       //dd($request);

       $idPatient             = $request->idPatient;
       $run                   = $request->run;
       $name                  = $request->name;
       $fathers_family        = $request->fathers_family;
       $mothers_family        = $request->mothers_family;
       $gender                = $request->gender;
       $birthday              = $request->birthday;
       $telephone             = $request->telephone;
       $servicioSalud         = $request->servicioSalud;
       $commune               = $request->commune;
       $establishmentRequest  = $request->establishmentRequest;
       $professional          = $request->professional;
       $date_exam_order       = $request->date_exam_order;
       $establishmentExam     = $request->establishmentExam;
       $doctor                = $request->doctor;
       $date_exam             = $request->date_exam;
       $derivation            = $request->derivation;
       $examType              = $request->examType;
        
        
       //dd($request);

       // Se obtiene el listado de Id de pacientes conforme el request.
       /*$patients_list = Patient::Where('run','LIKE','%'.$run.'%')
                               ->Where('name','LIKE','%'.$name.'%')
                               ->Where('fathers_family','LIKE','%'.$fathers_family.'%')
                               ->get('id');*/

        
        $exams = new Exam();
        $exams->servicio_salud       = $servicioSalud;
        $exams->profesional_solicita = $professional;
        $exams->comuna               = $commune;
        $exams->establecimiento_realiza_examen   = $establishmentExam;
        $exams->cesfam               = $establishmentRequest;
        $exams->medico               = $doctor;

        $exams->date_exam_order      = $date_exam_order;
        $exams->date_exam            = $date_exam;
        $exams->derivation_reason    = $derivation;
        $exams->exam_type            = $examType;
        $exams->load_source          = 'app';
        $exams->load_id              = 0;
        $exams->user_id              = Auth::id(); //Cambiar por usuario de sesión
        $exams->patient_id           = $idPatient;
        $exams->save();

       return $exams->toArray();
    }

    public function setLoadExams(Request $request)
    {

        if(!$request->ajax()) return redirect('/');

        $title        = $request->title;
        $description  = $request->description;
        $exams        = json_decode($request->getContent(), true);

        $load = new Load();
        $load->title       = $title;
        $load->description = $description;
        $load->save();
        

        foreach($exams['exams'] as $exam) {
           
            //list($run,$dv) = explode('-',str_replace(".", "", $exam['RUN']));

            list($run,$dv) = array_pad(explode('-',str_replace(".", "",$exam['RUN'])),2,null);
            //dd($dv);
            $patient_id = Patient::Where('run','LIKE','%'.$run.'%')->first('id');
            //dd($patient_id);
            
            
            if($patient_id == null)
            {

                /* separar el nombre completo en espacios */
                $tokens = explode(' ', trim($exam['NOMBRE']));
                /* arreglo donde se guardan las "palabras" del nombre */
                $names = array();
                /* palabras de apellidos (y nombres) compuetos */
                $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');
                
                $prev = "";
                foreach($tokens as $token) {
                    $_token = strtolower($token);
                    if(in_array($_token, $special_tokens)) {
                        $prev .= "$token ";
                    } else {
                        $names[] = $prev. $token;
                        $prev = "";
                    }
                }
                
                $num_nombres = count($names);
                $nombres = $apellidos = "";
                switch ($num_nombres) {
                    case 0:
                        $nombres = '';
                        break;
                    case 1: 
                        $nombres = $names[0];
                        break;
                    case 2:
                        $nombres    = $names[0];
                        $apellidos  = $names[1];
                        break;
                    case 3:
                        $apellidos = $names[1] . ' ' . $names[2];
                        $nombres   = $names[0];
                    default:
                        $apellidos = $names[1] . ' '. $names[2];
                        $nombres   = $names[0];
                        /*unset($names[0]);
                        unset($names[1]);
                        
                        $nombres = implode(' ', $names);*/
                        break;
                }
                
                $nombres    = mb_convert_case($nombres, MB_CASE_TITLE, 'UTF-8');
                $apellidos  = mb_convert_case($apellidos, MB_CASE_TITLE, 'UTF-8');

                $apellidos  = ($apellidos == NULL) ? ($apellidos = '') : $apellidos;

                list($run,$dv) = array_pad(explode('-',str_replace(".", "",$exam['RUN'])),2,null);

                //list($run,$dv) = explode('-',str_replace(".", "", trim($exam['RUN'])));

                
                $date_birthday = date('Y-m-d',strtotime(str_replace('/', '-',$exam['FECHA NAC'])));
                $date_birthday = ($date_birthday == NULL) ? ($date_birthday = '') : $date_birthday;

                $gender = $exam['GENERO'];
                if($gender == 'F'){
                    $gender = 'female';
                }
                else if($gender == 'M'){
                    $gender = 'male';
                }
                else{
                    $gender = 'unknown';
                }
               
                
                $newPatient = new Patient();
                $newPatient->run = $run;
                $newPatient->dv = $dv;
                $newPatient->name = $nombres;
                $newPatient->fathers_family = $apellidos;
                $newPatient->mothers_family = '';
                $newPatient->birthday = $date_birthday;
                $newPatient->address = $exam['DIRECCION'];
                $newPatient->telephone = $exam['FONO'];
                $newPatient->gender    = $gender;
                $newPatient->save();

                $idInsertPatient = $newPatient->id;
             
                
            }
            else {
                $idInsertPatient = $patient_id->id;
            }
            

            $date_exam_order = date('Y-m-d',strtotime(str_replace('/', '-',$exam['FECHA SOLICITUD'])));
            $date_exam_order  = ($exam['FECHA SOLICITUD'] == NULL) ? ($date_exam_order = NULL) : $date_exam_order;

            $date_exam = date('Y-m-d',strtotime(str_replace('/', '-',$exam['FECHA TOMA'])));
            $date_exam  = ($exam['FECHA TOMA'] == NULL) ? ($date_exam = NULL) : $date_exam;

            $date_exam_reception = date('Y-m-d',strtotime(str_replace('/', '-',$exam['FECHA RECEPCION'])));
            $date_exam_reception  = ($exam['FECHA RECEPCION'] == NULL) ? ($date_exam_reception = NULL) : $date_exam_reception;

            $commune = $exam['COMUNA'];
            $commune  = ($commune == NULL) ? ($commune = NULL) : $commune;

            $birardsMam = $exam['BIRADS MAM'];
            $birardsMam  = ($birardsMam == NULL) ? ($birardsMam = '') : $birardsMam;
            $birardsEco = $exam['BIRADS ECO'];
            $birardsEco  = ($birardsEco == NULL) ? ($birardsEco = '') : $birardsEco;
            $birardsPro = $exam['BIRADS PRO'];
            $birardsPro  = ($birardsPro == NULL) ? ($birardsPro = '') : $birardsPro;

            $mDerivation = $exam['MDERIVACION'];
            $mDerivation  = ($mDerivation == NULL) ? ($mDerivation = '') : $mDerivation;

            $diagnostic = $exam['DIAGNOSTICO'];
            $diagnostic  = ($diagnostic == NULL) ? ($diagnostic = '') : $diagnostic;
            
            

            $examDet = new Exam();
            $examDet->servicio_salud   = $exam['SERVICIO SALUD'];
            $examDet->comuna   = $commune;
            $examDet->profesional_solicita   = $exam['PROFESIONAL'];
            $examDet->establecimiento_realiza_examen   = $exam['ESTABLECIMIENTO EXAMEN'];
            $examDet->cesfam   = $exam['CESFAM'];
            $examDet->medico   = $exam['MEDICO'];
            $examDet->fonasa   = $exam['FONASA'];

            $examDet->date_exam_order      = $date_exam_order;
            $examDet->date_exam            = $date_exam;
            $examDet->diagnostico          = $diagnostic;
            $examDet->date_exam_reception  = $date_exam_reception;
            $examDet->birards_mamografia   = $birardsMam;
            $examDet->birards_ecografia    = $birardsEco;
            $examDet->birards_proyeccion   = $birardsPro;
            $examDet->derivation_reason    = $mDerivation;
            $examDet->load_source          = 'excel';
            $examDet->load_id              = $load->id;
            $examDet->user_id              = Auth::id();
            $examDet->patient_id           = $idInsertPatient;
            $examDet->save();

        }
        
        
        return $exams;
    }

    public function setDeleteExam(Request $request)
    {
       if(!$request->ajax()) return redirect('/');
       $idExam   = $request->idExam;

       $idExam = ($idExam == NULL) ? ($idExam = 0) : $idExam;

       $exam = Exam::find($idExam);
       $exam ->delete(); 

       return $exam;
    }
}
