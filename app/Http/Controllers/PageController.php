<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use File;
use App\Models\Team;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function welcome(Request $request)
    {
        if($request->has('code'))
        {
            $code = $request->get('code');
            $result = $this->auth_call('POST', $code);
            return $result;
        }
        else
        {
            if (session()->has('token')) {
                $status = 0;
            }
            else {
                $status = 1;
            }
            return view('welcome',[ 
                'status' => $status
                ]
                );
        }
    }

    public function index($type = "folders", $id = null)
    {

    $folder = $this->load_folder($type, $id);

    if (!empty($folder["children"]))
    {
        $children = $folder["children"];  
    }
    else
    {
        $children = [];
    }

    if (!empty($folder["parent_title"]))
    {
        $title = $folder["parent_title"];
    }
    else
    {
        $title = "";
    }
    $children_concat = "";

        if (count($children) > 0)
        {
            for ($i=0; $i < count($children); $i++)
            {
                $children_concat .= $children[$i].",";
            }
            $folders = $this->load_folder($type, $children_concat);

            $folders = array_values(array_sort($folders['folders'], function ($value) {
                return $value['title'];
            }));

            return view('folders',[ 
                'folders' => $folders,
                'title' => $title
                ]
                );
        }

        else
        {
            $tasks = $this->load_folders_tasks($type = "tasks", $id , $title);
            $stored_tasks = Task::with('team')->published()->get();
            return view('tasks',[ 
                'tasks' => $tasks['tasks'],
                'title' => $tasks['title'],
                'stored_tasks' => $stored_tasks,
                'folder_id' => $id,
                ]
                );
        }

    }


    public function auth_call ($method = 'POST', $token = null)
    {
        $client_id = env('CLIENT_ID');
        $client_secret = env('CLIENT_SECRET');

        $params = array();
        $params['client_id'] = $client_id;
        $params['client_secret'] = $client_secret;
        $params['grant_type'] = 'authorization_code';
        $params['code'] = $token;

        $api_url = "https://www.wrike.com/oauth2/token"; 
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL,$api_url);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$params);

        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

            if (empty($buffer))
            {
                return "ERROR!! Nothing returned from url. Please refresh the page!";
            }
            else
            {
                $buffer = json_decode($buffer);

                if (!empty($buffer->access_token)){
                    session()->flush();
                    session()->put('token', $buffer->access_token);
                    return redirect()->route('all-folders', ['type' => 'folders', 'id' => 'IEAAZ7U3I4CF77VV']);
                }
                else
                {
                    session()->flush();
                    header('Location: /welcome');
                    exit();
                }
            }
    }


    public function api_call ($method = 'GET', $url = null)
    {
        
        if (session()->has('token')) {

            $value = session('token');
            $myToken = 'Authorization: bearer '.$value;

            $header = array();
            $header[] = $myToken;
            $api_url = "https://www.wrike.com/api/v3/".$url; 
            
            $curl_handle=curl_init();
            curl_setopt($curl_handle,CURLOPT_URL,$api_url);
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curl_handle,CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl_handle,CURLOPT_HTTPHEADER, $header);

            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);

                if (empty($buffer))
                {
                    return "ERROR!! Nothing returned from url. Please refresh the page!";
                }
                else
                {
                    $buffer = json_decode($buffer, true);
                    return $buffer;
                }
        }
        else
        {   
            header('Location: /welcome');
            exit();
        }
    }

    public function load_folder($type = "folders", $id = null) 
    {

        $url = $type."/".$id;
        $folders = $this->api_call("GET" , $url); 
        $folder = array();

        if (!empty($folders['data']))
        {
            $parent_title = $folders['data'][0]['title'];

            $folders = $folders['data'];


            $folders_count = count($folders);

            for ($i=0; $i < $folders_count; $i++)
                {
                    $folders_array[] = ["id" => $folders[$i]["id"], "title" => $folders[$i]["title"]];
                }

            $children = $folders[0]['childIds'];
            $folder = array("children" => $children, "folders" => $folders_array, "parent_title" => $parent_title);
        }

        return $folder;
    }


    public function load_all_tasks ($type = "tasks", $id = null) 
    {
        $url = $type."?status=Active&createdDate={'start':'2017-03-23T04:28:01Z','end':'2017-03-23T04:37:01Z'}";
        $tasks = $this->api_call("GET" , $url); 
        if (!empty($tasks['data']))
        {
            $tasks = $tasks['data']; 
            $tasks_count = count($tasks);
            $tasks_concat = "";
            ini_set('max_execution_time', 600); //10 minutes
            for ($i=0; $i < $tasks_count; $i++)
            {
                echo "<a href='/api/type/tasks/".$tasks[$i]['id']."'>".$tasks[$i]['title']."</a><br/>";
                $this->alloc_balance_calculation($tasks[$i]['id']);
            }   
        }
    }

    public function all_tasks_dates ()
    {
        return view('report-dates',[ 
                'title' => 'Report Generator',
                ]
                );     
    }

    public function load_folders_tasks ($type = "tasks", $id = null, $title = null) 
    {
        $url = "folders/".$id."/".$type."?status=Active";
        $tasks = $this->api_call("GET" , $url); 
        $tasks_array = array();

        if (!empty($tasks['data']))
        {
            $folder_title = $tasks['data'][0]['title'];
            $tasks = $tasks['data'];
            $tasks_count = count($tasks);
            
            for ($i=0; $i < $tasks_count; $i++)
                {
                    $tasks_array[] = ["id" => $tasks[$i]['id'], "title" => $tasks[$i]['title']];
                }

            $tasks = array(
                'title' => $title, 
                'tasks' => $tasks_array
                );
            return $tasks;
        }
        else
        {
            $tasks = array(
                'title' => $title, 
                'tasks' => $tasks_array
                );
            return $tasks;
        }
    }



    public function load_single_task($id = null) 
    {

        $url = "tasks/".$id;
        $task_id = $id;
        $task = $this->api_call("GET" , $url); 

        if (!empty($task["data"][0]))
        {

        $title = $task['data'][0]['title'];
        $created_date = $task['data'][0]['createdDate'];
        $allocated = 0;

        // print_r($task['data'][0]);

        $custom_count = count($task['data'][0]['customFields']);



        if (!empty($task['data'][0]['customFields']))
            {

                for ($i=0; $i < $custom_count; $i++)
                {
                    $id = $task['data'][0]['customFields'][$i]['id'];
                    if ($id == "IEAAZ7U3JUAABVPM")
                    {
                        $value = $task['data'][0]['customFields'][$i]['value'];
                        $allocated = $value;
                        break;
                    } 
                }
            }
        else
            {
                $allocated = 0;
            }

        $logs = $this->task_logs_total($task_id);

        if (empty($logs))
        {
            $logs = 0;
        }
        $balance = $allocated - $logs;
        return view('task',[ 
                'title' => $title,
                'allocated' => $allocated,
                'id' => $task_id,
                'sum' => $logs,
                'balance' => $balance,
                'created_date' => $created_date
                ]
                );

        }
        else
        {
            return "Error!! Please refresh the page, or choose another range!";
        }
    }


    public function create_task ($folder_id = null, $task_id = null, $folder_code = null) 
    {
        $task = Task::whereId($task_id)->get(['title','hours','unique_code'])->first();
        // print_r($task);
        $title = "[".$folder_code."".$task->unique_code."] ".$task->title;
        $title = urlencode($title);
        $url = "folders/".$folder_id."/tasks?title=".$title."&customFields=[{'id':'IEAAZ7U3JUAABVPM','value':'".$task->hours."'}]&status=Active";
        $call = $this->api_call("POST" , $url); 
        return redirect()->route('all-folders', ['type' => 'folders', 'id' => $folder_id]);
    }

    public function modify_allocated_time ($id = null, $amount = null) 
    {
        echo "id: ".$id."<br/>";
        $url = "tasks/".$id."?customFields=[{'id':'IEAAZ7U3JUAABVPM','value':'".$amount."'}]";
        $call = $this->api_call("PUT" , $url);
        return redirect()->route('single-task', ['id' => $id]);
    }
 
    
    public function modify_balance ($id = null, $amount = null) 
    {

        $url = "tasks/".$id."?customFields=[{'id':'IEAAZ7U3JUAAHYIF','value':'".$amount."'}]";
        $call = $this->api_call("PUT" , $url); 
    }

    public function task_logs_total($id = null) 
    {
        $url = "tasks/".$id."/timelogs";
        $logs = $this->api_call("GET" , $url); 
        $sum = 0;
        if (!empty($logs['data']))
        {
            $logs_count = count($logs['data']);
            for ($i = 0; $i < $logs_count; $i++) 
            {
                $sum = $sum + $logs['data'][$i]['hours'];
            }
            return $sum;
        }
    }

    public function alloc_balance_calculation($id = null) 
    {
        $url = "tasks/".$id."/timelogs";
        $logs = $this->api_call("GET" , $url); 
        $sum = 0;
        if (!empty($logs['data']))
        {
            $logs_count = count($logs['data']);
            for ($i = 0; $i < $logs_count; $i++) 
            {
                $sum = $sum + $logs['data'][$i]['hours'];
            }
            $allocated_hour = $this->get_allocated_hour($id);
            $new_amount = $allocated_hour - $sum;
            $this->modify_balance($id, $new_amount);
        }
    }

    public function get_allocated_hour($id = null) 
    {
        $url = "tasks/".$id;
        $task = $this->api_call("GET" , $url); 
        $allocated = 0;
        if (!empty($task['data'][0]))
        {
            $custom_count = count($task['data'][0]['customFields']);
            if (!empty($task['data'][0]['customFields']))
                {
                    for ($i=0; $i < $custom_count; $i++)
                    {
                        $id = $task['data'][0]['customFields'][$i]['id'];
                        if ($id == "IEAAZ7U3JUAABVPM")
                        {
                            $value = $task['data'][0]['customFields'][$i]['value'];
                            $allocated = $value;
                            break;
                        } 
                    }
                }
        }
        return $allocated;
    }

    public function generate_report_init ($start = "2017-01-01", $end = "2017-01-30") 
    {
        $url = "tasks?createdDate={'start':'".$start."T00:00:01Z','end':'".$end."T00:00:01Z'}&pageSize=1000";

        $tasks = $this->api_call("GET" , $url);

        if (!empty($tasks['responseSize']))
        {
            echo $tasks['responseSize'];
        }
        else
        {
            $data = count($tasks['data']);
            echo $data;
        }

    }


    public function generate_report ($start = "2017-01-01", $end = "2017-01-30", $counter = 0, $nextPageToken = "") 
    {

        $url = "tasks?createdDate={'start':'".$start."T00:00:01Z','end':'".$end."T00:00:01Z'}&pageSize=1000&nextPageToken=".$nextPageToken;
        $tasks = $this->api_call("GET" , $url);
        $next_page = false;

        if (!empty($tasks['responseSize']))
        {
            $nextPageToken = $tasks['nextPageToken'];
            $next_page = true;
        }

        $values = array();
        if (!empty($tasks['data']))
        {
            $tasks = $tasks['data']; 
            $tasks_count = count($tasks);
            $tasks_concat = "";
            ini_set('max_execution_time', 6000); //100 minutes
            for ($i = $counter; $i < $tasks_count; $i+=3)
            {
                $number = $i;
                $title = $tasks[$i]['title'];
                $createdDate = $tasks[$i]['createdDate'];
                $allocated = $this->get_allocated_hour($tasks[$i]['id']);
                $logs = $this->task_logs_total($tasks[$i]['id']);
                if (empty($logs))
                {
                    $logs = 0;
                }
                $balance = $allocated - $logs;
                $values[] = ["number" => $number, "title" => $title, "allocated" => $allocated, "logs" => $logs, "balance" => $balance, "createdDate" => $createdDate];
            }

            if ($next_page)
                $this->generate_report($start,$end,$nextPageToken);

            return view('report',[ 
                'title' => 'Report',
                'values' => $values,
                ]
                );   
        }


    }

    public function report_dates() 
    {
        return view('report-dates',[ 
                'title' => 'Report',
                ]
                ); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

}
