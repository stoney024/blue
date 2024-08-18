<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\Sync;
use App\Jobs\fetchApi;
use App\Models\Directory;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * Method to return the data for the current node level, recoursively
     *
     * @return array
     */
    public function addLevel($directory, $data){

        $children = $directory->children;

        $levelData = [];

        if ($children->count() > 0){
            // recoursive call for every child node
            foreach ($children as $i => $child){
                $levelData[] = $this->addLevel($child,$data);
            }
        }
        
        $files = File::where('directory_id',$directory->id)->pluck('name')->toArray();

        if (count($files) >0){
            $levelData = array_merge($levelData,$files);
        }
        $data[$directory->name] = $levelData;
        return $data;
    }

      /**
     * Method to return the full files and directories data
     *
     * @return \Illuminate\Http\Response
     */
    public function files_and_directories(){

        $data = [];
        $rootNodes = Directory::whereIsRoot()->get();    
  
        // add every root nodes data:
        foreach ($rootNodes as $i => $directory){
            $data = $this->addLevel($directory, $data);
        }
        
        if (count($data) > 0){
            return response()->json($data, 200);
        } else {
            return response()->json([
                'status'        => 'error',
                'status_msg'    => 'root directory not exists'
            ], 404);
        }
    }
   
    /**
     * Method to return the directories data
     *
     * @return \Illuminate\Http\Response
     */
    public function directories(){
        
        $directories = Directory::select('name')->paginate(100);

        // add the path info to the paginated collection data
        $directories->getCollection()->transform(function ($directory) {

            //get the model and apply the ancestors path
            $dir = Directory::where('name',$directory->name)->first();
            return implode('/', $dir->ancestors->pluck('name')->toArray()).'/'.$directory->name;
        });

        return response()->json($directories, 200);
    }

    /**
     * Method to return the files data
     *
     * @return \Illuminate\Http\Response
     */
    public function files(){
        $data = File::select('name')->paginate(100);
        return response()->json($data, 200);
    }


    /**
     * Method to return fetched data status
     *
     * @return \Illuminate\Http\Response
     */
    public function info(){

        $sync = Sync::latest()->get()->first();

        if ($sync){
            $data['processed_at'] = $sync->processed_at;
        } else{
            $data['status'] = ' Database empty, please fetch data with /api/update';
        }
       
        return response()->json($data, 200);
    }

     /**
     * Method to return fetched data status
     *
     * @return \Illuminate\Http\Response
     */
    public function update(){

        fetchApi::dispatch();

        $data['status'] = 'Remote update started...  ';
        return response()->json($data, 200);
    }

}
