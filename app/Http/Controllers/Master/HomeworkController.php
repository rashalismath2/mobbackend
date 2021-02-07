<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;
use Exception;
use Illuminate\Support\MessageBag;

use App\Models\Homework;
use App\Models\Group;
use App\Models\HomeworkAttachments;
use App\Models\HomeworksGroups;

use Illuminate\Support\Carbon;


class HomeworkController extends Controller
{
    public function __construct(){
        $this->middleware('auth:masterapi');
    }

    public function getAllHomeworks(Request $request,MessageBag $message_bag){

        $homeworks=Homework::whereHas("HomeworkGroups.group",function($query){
                            $query->where("master_id",auth()->user()->id);
                        })
                        ->with("HomeworkAttachments")
                        ->with("HomeworkGroups.group")
                        ->get();

        return response()->json($homeworks,200);
    }
    public function startHomework(Request $request,MessageBag $message_bag){
        //homework status shouldnt be on,or finished
        $validator=Validator::make($request->all(),[
            "homework_id"=>"required|numeric|exists:homeworks,id",
            "started_at"=>"required|date",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

 
        $homework=Homework::find($request->homework_id);
        //homework must be created by the same master
        try {
            if($homework->HomeworkGroups[0]->group->master_id!=auth()->user()->id){
                throw new \Exception("Auth user failed");
            }
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting homework does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }

            
        if($homework->status=="queued"){
            $homework->status="on";
            
            $homework->started_at=Carbon::parse($request->started_at);
            $homework->update();
            return response()->json(["Message"=>"Homework started"],200);
        }
        else{
            $message_bag->add("error","Homework is not on the proper status");
            return response()->json($message_bag, 422);
        }

    }
    public function endHomework(Request $request,MessageBag $message_bag){
        //homework status shouldnt be queued,or finished
        $validator=Validator::make($request->all(),[
            "homework_id"=>"required|numeric|exists:homeworks,id",
            "ended_at"=>"required|date",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $homework=Homework::find($request->homework_id);
        //homework must be created by the same master
        try {
            if($homework->HomeworkGroups[0]->group->master_id!=auth()->user()->id){
                throw new \Exception("Auth user failed");
            }
        } catch (Exception $e) {
            $message_bag->add("errors","Requesting homework does not belongs to authenticated user.!");
            return response()->json($message_bag,422);
        }

        if($homework->status=="on"){
            $homework->status="ended";
            
            $homework->ended_at=Carbon::parse($request->ended_at);
            $homework->update();
            return response()->json(["Message"=>"Homework ended"],200);
        }
        else{
            $message_bag->add("error","Homework is not on the proper status");
            return response()->json($message_bag, 422);
        }

    }

    public function createHomeWork(Request $request,MessageBag $message_bag){

        $validator=Validator::make($request->all(),[
            "title"=>"required|string|min:2",
            "note"=>"required|string|min:4",
            "onetime"=>"required|boolean",
            "allow_late"=>"required|boolean",
            "startDate"=>"required|date",
            "endDate"=>"required",
            "startTime"=>"required",
            "endTime"=>"required",
            "fileCount"=>"required|numeric",
            "groupCount"=>"required|numeric",
            "number_of_questions"=>"required|numeric",
        ]);
 
       
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //validate if atleast one file is present in the request
        try {
            if(!$request->hasFile("file_1")){
                throw new \Exception("Homeworks must include atleast one file");
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }


        //validate if the sent groups are belongs to authenticated user
        try { 

            for ($i=1; $i <=$request->groupCount; $i++) { 
                if ($request->has('group_'.$i)) {
                    $groupId=$request["group_".$i];
                    //if user doesnt have requesting group(auth user should have requesting group created by him)
                    $group=Group::where("id",$groupId)->where("master_id",auth()->user()->id)->firstOrFail();
                }
                else{
                    throw new \Exception("The group input are invalid");
                }
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }


        $homework=new Homework();
        $homework->title=$request->title;
        $homework->note=$request->note;
        $homework->onetime=$request->onetime;
        $homework->startDate=Carbon::parse($request->startDate);
        $homework->endDate=Carbon::parse($request->endDate);
        $homework->startTime=Carbon::parse($request->startTime);
        $homework->endTime=Carbon::parse($request->endTime);
        $homework->allow_late=$request->allow_late;
        $homework->number_of_questions=$request->number_of_questions;
        $homework->status="queued";
    
        try {
            $homework->save();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }

        //save files
        $this->saveFile($request,$homework->id,$message_bag);
  
        if(count($message_bag)!=0){
            return response()->json($message_bag,422);
        }

        $attachments=$homework->HomeworkAttachments;
        $homeworkgroups=$homework->HomeworkGroups()->with("group")->get();
      

        return response()->json(["Message"=>"homework created",
            "homework"=>$homework,
            "attachments"=>$attachments,
            "homeworkgroups"=>$homeworkgroups,
        ],200);
     
    }



    protected function saveFile($request,$homeworkId,$message_bag){

        $files=[];
        $storeHomeworkAttachemnts=[];
      
        for ($i=1; $i <=$request->fileCount; $i++) { 
            $file_name="file_".$i;
           
            try {
                if(!$request->hasFile($file_name)){
                    throw new \Exception("The file doesnt exists");
                }
               else{
                    $fileNameWithExtenstion=$request->file($file_name)->getClientOriginalName();
                    $fileName=pathinfo($fileNameWithExtenstion,PATHINFO_FILENAME);
                    
                    $nameArray=explode(".",$fileNameWithExtenstion);
                    $count=count(explode(".",$fileNameWithExtenstion));
                    $extension=$nameArray[$count-1];
               }

            } catch (Exception $e) {
                if(count($files)!=0){
                    Storage::delete($files);
                }
                if(count($storeHomeworkAttachemnts)!=0){
                    HomeworkAttachments::destroy($storeHomeworkAttachemnts);
                }
                $message_bag->add("errors",$e->getMessage());
                break; 
            }
            

            $allowedExtenstion = array("jpg","png","jpeg", "pdf", "doc","docx","xls","pptx");

            try {
                if(!in_array($extension,$allowedExtenstion)){
                    throw new \Exception("The file format does not support");
                }
            } catch (Exception $e) {
                if(count($files)!=0){
                    Storage::delete($files);
                }
                if(count($storeHomeworkAttachemnts)!=0){
                    HomeworkAttachments::destroy($storeHomeworkAttachemnts);
                }
                $message_bag->add("errors",$e->getMessage());
                break; 
            }

            $fileNameToStore=$fileName."_".time().".".$extension;

            $filePath="public/attachments/".date("Y-m-d");
            $filePathToDB="storage/attachments/".date("Y-m-d");

            try {
                $path=$request->file($file_name)->storeAs($filePath,$fileNameToStore);

                array_push($files,$filePath."/".$fileNameToStore);
            } catch (Exception $e) {
                if(count($files)!=0){
                    Storage::delete($files);
                }
                if(count($storeHomeworkAttachemnts)!=0){
                    HomeworkAttachments::destroy($storeHomeworkAttachemnts);
                }

                $message_bag->add("errors",$e->getMessage());
                break; 
            }

            try {
                $homeworkAttachemnts=new HomeworkAttachments();
                $homeworkAttachemnts->file_type=$extension;
                $homeworkAttachemnts->homework_id=$homeworkId;
                $homeworkAttachemnts->file_path=$filePathToDB."/".$fileNameToStore;
                
                $homeworkAttachemnts->save();
                array_push($storeHomeworkAttachemnts,$homeworkAttachemnts->id);
            } catch (Exception $e) {
                if(count($files)!=0){
                    Storage::delete($files);
                }
                if(count($storeHomeworkAttachemnts)!=0){
                    HomeworkAttachments::destroy($storeHomeworkAttachemnts);
                }

                $message_bag->add("errors",$e->getMessage());
                break; 
            }
        }

        if(count($message_bag)!=0){
            return response()->json($message_bag,422);
        }
        $this->storeHomeworksGroups($request,
               $homeworkId,
                $storeHomeworkAttachemnts,$message_bag);

    }

    protected function storeHomeworksGroups($request,$homeworkId,$storeHomeworkAttachemnts,$message_bag){
        //save groupshomeworks
        $savedGroups=[];
        for ($i=1; $i <=$request->groupCount; $i++) { 
            //if sabing groups went wrong delete saved groupshomeworks and files
            try {
                $groupId=$request["group_".$i];
                $group=new HomeworksGroups();
    
                $group->group_id=$groupId;
                $group->homework_id=$homeworkId;
                $group->save();
    
                array_push($savedGroups,$group->id);
            } catch (Exception $e) {
                if(count($files)!=0){
                    Storage::delete($files);
                }
                if(count($savedGroups)!=0){
                    HomeworksGroups::destroy($savedGroups);
                }
                if(count($storeHomeworkAttachemnts)!=0){
                    HomeworkAttachments::destroy($savedGroups);
                }
    
                $message_bag->add("errors",$e->getMessage());
                break; 
            }
        }
    }


}


