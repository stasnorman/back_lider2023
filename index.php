<?php 
  header("Content-Type: application/json");
  header('Access-Control-Allow-Origin: http://localhost:5173');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Разрешить   

    require_once 'connect.php'; 
    require_once 'function_post.php';
    require_once 'function_get.php';
    require_once 'function_put.php';
    require_once 'function_responce.php';

    $actionMethod = $_SERVER['REQUEST_METHOD'];
    
    $typeUrl = isset($_GET['q']) ? $_GET['q'] : null;
    switch ($actionMethod) {
        case 'GET':
                switch ($typeUrl) {
                    case 'users':
                        viewallusers($connect);
                       break;
                    case 'user':
                        viewalluser($connect, $_GET['id']);
                        break;
                    case 'roles':
                        viewallroles($connect);
                        break;
                    case 'role':
                        viewallrole($connect, $_GET['id']);
                        break;
                    case 'all-level-worker':
                        viewalllevel($connect);
                        break;
                    case 'level-worker':
                        viewlevelworker($connect, $_GET['id']);
                        break;
                    case 'all-condition-tolmut':
                        viewAllCondition($connect);
                        break;
                    case 'condition-tolmut': 
                        viewTolmutData($connect, $_GET['id']);
                        break;
                    case 'all-tolmuts':
                        viewListTolmut($connect);
                        break;
                    case 'all-tasks-schedule':
                        viewallSchedule($connect);
                        break;
                    case 'task-schedule':
                        getTask($connect, $_GET['id']);
                        break;
                    case 'all-tasks':
                        getAllTask($connect);
                        break;
                    case 'select-task':
                        getOneTask($connect, $_GET['id']);
                        break;
                    case 'all-sub-tasks':
                        getAllSubTasks($connect);
                        break;
                    case 'sub-task':
                        getOneSubTask($connect, $_GET['id']);
                        break;
                    case 'notifications-managers':
                        getNotification($connect);
                        break;
           
                    default:
                        http_response_code(418);
                        echo "Такого типа запроса нет.";
                        break;
                }
        break;
        case 'POST':
                switch($typeUrl){
                    case 'auth':
                            $dataUser = file_get_contents('php://input');
                            $dataUser = json_decode($dataUser, true);
                            authUser($connect, $dataUser);
                        break;
                    case 'create-task':
                        createTask($connect, json_decode(file_get_contents('php://input'), true));
                        break;
                    case 'create-sub-task':
                        createSubTask($connect, json_decode(file_get_contents('php://input'), true));
                        break;
                    case '':
                        break;
                }
        break;
        case 'PUT':
            switch($typeUrl){
                case 'update-task':
                    updateTask($connect, $_GET['id'], json_decode(file_get_contents('php://input'), true));
                    break;
                case 'update-sub-task':
                    updateSubTask($connect, $_GET['id'], json_decode(file_get_contents('php://input'), true));
                    break;
            }
        break;
        default:
        
            break;
    }
?>