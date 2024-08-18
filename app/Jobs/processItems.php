<?php

namespace App\Jobs;

use Exception;
use App\Models\File;
use App\Models\Sync;
use App\Models\SyncItem;
use App\Models\Directory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class processItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


     /**
     * The json response data
     *
     * @var $data jonObject
     */
    protected $data;


      /**
     * The sync id
     *
     * @var $sync_id integer
     */
    protected $sync_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$sync_id)
    {
        $this->data = $data;
        $this->sync_id = $sync_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("Sync: $this->sync_id. - processItems");

        foreach ($this->data['items'] as $row){
            
            try {
                //$items[] = ['sync_id' => $this->sync_id, 'fileUrl' => mb_convert_encoding($row['fileUrl'],'UTF-8','BIG5')];    
                $url = iconv('BIG5','UTF-8',$row['fileUrl']);
                $item = ['sync_id' => $this->sync_id, 'file_url' => $url];    

                $syncItem = SyncItem::updateOrCreate(
                    ['file_url'=> $url],
                    $item                    
                );

                if ($syncItem->created_at == $syncItem->updated_at){
                    //newly inserted item
                    $this->processUrl($url, $syncItem->id);
                }

            } catch (Exception $e) {
                \Log::error('Caught exception: '. $e->getMessage().' '.$row['fileUrl']);
            }
            
        }

        // Delete the missing ones, the fetched data didn't updated the syncItem
        // By foreign key, it deletes the linked files and folders - on delete cascade
        SyncItem::where('sync_id','!=',$this->sync_id)->delete();

        $sync = Sync::where('id',$this->sync_id)->first();

        $sync->processed_at = \Carbon\Carbon::now();
        $sync->save();
    }


      /**
     * process url
     *
     * @return void
     */
    public function processUrl($url, $syncItemId){

        $isDirectory = substr($url, -1) == '/';

        //trim / from the end of the url
        $url = rtrim($url,'/');

        $urlArr = explode("://",$url);

        $url = $urlArr[1]; //ommit the http:// or https:// tags
        $urlArr = explode('/',$url);
      
        $rootSlug = array_shift($urlArr);

        $rootSlugArr = explode(':',$rootSlug);

        if (isset($rootSlugArr[0])){
            $rootSlugIP = $rootSlugArr[0];
        } else {
            $rootSlugIP = $rootSlug;
        }

        //create root category if needed
        $rootDirectory = Directory::where('name',$rootSlugIP)->first();

        if (!$rootDirectory){
            //create the root directory
            $rootDirectory = Directory::create([
                'name' => $rootSlugIP,
            ]);  
        }

        if ($isDirectory){
            //process directory
            $directoryName = array_pop($urlArr);
            $directory = Directory::create([
                'name'          => $directoryName,
                'syncItem_id'   => $syncItemId
            ]);  

            $i = count($urlArr);

            if ($i >= 1){
                $parentDirectoryName = array_pop($urlArr);
                $parent = Directory::where('name',$parentDirectoryName)->first();
                $directory->appendToNode($parent)->save();
            } else {
                $directory->appendToNode($rootDirectory)->save();
            }

        } else {
            //process file
            $fileName = array_pop($urlArr);

            $directoryName = array_pop($urlArr);
            $directory = Directory::where('name',$directoryName)->first();

            $file = File::create([
                'name'          => $fileName,
                'syncItem_id'   => $syncItemId,
                'directory_id'  => $directory->id 
            ]);  
        }
    }
}
