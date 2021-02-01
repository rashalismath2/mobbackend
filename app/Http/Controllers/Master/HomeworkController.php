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


class HomeworkController extends Controller
{
    public function __construct(){
        $this->middleware('auth:masterapi');
    }

    public function createHomeWork(Request $request,MessageBag $message_bag){

        $validator=Validator::make($request->all(),[
            "title"=>"required|string|min:2",
            "note"=>"required|string|min:4",
            "onetime"=>"required|boolean",
            "startDate"=>"required|date",
            "endDate"=>"required",
            "startTime"=>"required",
            "endTime"=>"required",
            "fileCount"=>"required|numeric",
            "groupCount"=>"required|numeric",
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
        $homework->startDate=$request->startDate;
        $homework->endDate=$request->endDate;
        $homework->startTime=$request->startTime;
        $homework->endTime=$request->endTime;
        $homework->status="queued";

        $homework->save();

        //save files
        $this->saveFile($request,$homework->id,$message_bag);
  
        if(count($message_bag)!=0){
            return response()->json($message_bag,422);
        }
        
        
        return response()->json(["Message"=>"homework created"],200);
     
    }



    protected function saveFile($request,$homeworkId,$message_bag){

        $files=[];
        $storeHomeworkAttachemnts=[];

        for ($i=1; $i <=$request->fileCount; $i++) { 
            $file_name="file_".$i;

            try {
                if(!$request->hasFile($file_name)){
                    throw new \Exception("The file format does not support");
                }
               else{
                    $fileNameWithExtenstion=$request->file($file_name)->getClientOriginalName();
                    $fileName=pathinfo($fileNameWithExtenstion,PATHINFO_FILENAME);
                    $extension=$request->file($file_name)->getClientOriginalExtension();
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
            

            $allowedExtenstion = array("jpg", "jpeg", "pdf", "doc","docx","xls","pptx");

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
                $homeworkAttachemnts->file_path=$filePath."/".$fileNameToStore;
                
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
                $group->homework_id=$homework->id;
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


