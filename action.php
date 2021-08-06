<?php
session_start();
include("api.php");
include("conn.php");
$db = new DataAccess($conn);
$validate = new Validator($conn);
function format_folder_size($size)
{
 if ($size >= 1073741824)
 {
  $size = number_format($size / 1073741824, 2) . ' GB';
 }
    elseif ($size >= 1048576)
    {
        $size = number_format($size / 1048576, 2) . ' MB';
    }
    elseif ($size >= 1024)
    {
        $size = number_format($size / 1024, 2) . ' KB';
    }
    elseif ($size > 1)
    {
        $size = $size . ' bytes';
    }
    elseif ($size == 1)
    {
        $size = $size . ' byte';
    }
    else
    {
        $size = '0 bytes';
    }
 return $size;
}

function get_folder_size($folder_name)
{
 $total_size = 0;
 $file_data = scandir($folder_name);
 foreach($file_data as $file)
 {
  if($file === '.' or $file === '..')
  {
   continue;
  }
  else
  {
   $path = $folder_name . '/' . $file;
   $total_size = $total_size + filesize($path);
  }
 }
 return format_folder_size($total_size);
}

if(isset($_POST["action"]))
{
 if($_POST["action"] == "fetch")
 {
  $folder = array_filter(glob('*'), 'is_dir');
  
  $output = '
  <table class="table table-bordered table-striped">
   <tr>
    <th>Folder Name</th>
    <th>Total File</th>
    <th>Size</th>
    <th>Update</th>
    <th>Delete</th>
    <th>Upload File</th>
    <th>View Uploaded File</th>
   </tr>
   ';
  if(count($folder) > 0)
  {
   foreach($folder as $name)
   {
    $output .= '
     <tr>
      <td>'.$name.'</td>
      <td>'.(count(scandir($name)) - 2).'</td>
      <td>'.get_folder_size($name).'</td>
      <td><button type="button" name="update" data-name="'.$name.'" class="update btn btn-warning btn-xs">Update</button></td>
      <td><button type="button" name="delete" data-name="'.$name.'" class="delete btn btn-danger btn-xs">Delete</button></td>
      <td><button type="button" name="upload" data-name="'.$name.'" class="upload btn btn-info btn-xs">Upload File</button></td>
      <td><button type="button" name="view_files" data-name="'.$name.'" class="view_files btn btn-default btn-xs">View Files</button></td>
     </tr>';
   }
  }
  else
  {
   $output .= '
    <tr>
     <td colspan="6">No Folder Found</td>
    </tr>
   ';
  }
  $output .= '</table>';
  echo $output;
 }
 
 if($_POST["action"] == "create")
 {
  if(!file_exists($_POST["folder_name"])) 
  {
   mkdir($_POST["folder_name"], 0777, true);
   echo 'Folder Created';
  }
  else
  {
   echo 'Folder Already Created';
  }
 }
 if($_POST["action"] == "change")
 {
  if(!file_exists($_POST["folder_name"]))
  {
   rename($_POST["old_name"], $_POST["folder_name"]);
   echo 'Folder Name Change';
  }
  else
  {
   echo 'Folder Already Created';
  }
 }
 
 if($_POST["action"] == "delete")
 {
  $files = scandir($_POST["folder_name"]);
  foreach($files as $file)
  {
   if($file === '.' or $file === '..')
   {
    continue;
   }
   else
   {
    unlink($_POST["folder_name"] . '/' . $file);
   }
  }
  if(rmdir($_POST["folder_name"]))
  {
   echo 'Folder Deleted';
  }
 }
 
 if($_POST["action"] == "fetch_files")
 {
  $file_data = scandir($_POST["folder_name"]);
  $output = '
  <table class="table table-bordered table-striped">
   <tr>
    <th>Download</th>
    <th>File Name</th>
    <th>Delete</th>
   </tr>
  ';
  
  foreach($file_data as $file)
  {
   if($file === '.' or $file === '..')
   {
    continue;
   }
   else
   {
    $path = $_POST["folder_name"] . '/' . $file;
    $output .= '
    <tr>
    <td><a href="'.$path.'">Download</a></td>
     <td contenteditable="true" data-folder_name="'.$_POST["folder_name"].'"  data-file_name = "'.$file.'" class="change_file_name">'.$file.'</td>
     <td><button name="remove_file" class="remove_file btn btn-danger btn-xs" id="'.$path.'">Remove</button></td>
    </tr>
    ';
   }
  }
  $output .='</table>';
  echo $output;
 }
 
 if($_POST["action"] == "remove_file")
 {
  if(file_exists($_POST["path"]))
  {
   unlink($_POST["path"]);
   echo 'File Deleted';
  }
 }
 
 if($_POST["action"] == "change_file_name")
 {
  $old_name = $_POST["folder_name"] . '/' . $_POST["old_file_name"];
  $new_name = $_POST["folder_name"] . '/' . $_POST["new_file_name"];
  if(rename($old_name, $new_name))
  {
   echo 'File name change successfully';
  }
  else
  {
   echo 'There is an error';
  }
 }




 if($_POST["action"] == "login"){
     $username = filter_var($_POST["username"],FILTER_SANITIZE_STRING);
     $password = filter_var($_POST["password"],FILTER_SANITIZE_STRING);
     $condition = "username= '$username' and password = '$password'";
    $userdetails = $db->fetch_single("registration",$condition);
    if(empty($userdetails)){
        echo "Wrong Username or Password";
    }else{
        $_SESSION["user"] = $userdetails["username"];
        $_SESSION["pass"] = $userdetails["password"];
        echo 1;
    }
 }


 if($_POST["action"] == "logout"){
     if(session_unset()){
        echo 1;
     }else{
         echo "Something Went Wrong";
     }

    
 }

 if($_POST["action"] == "register"){
    $full_name = strtoupper(filter_var($_POST["first_name"],FILTER_SANITIZE_STRING));
    $User_name = strtoupper(filter_var($_POST["last_name"], FILTER_SANITIZE_STRING));
    $email = filter_var($_POST["your_email"],FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    $comfirm_password = filter_var($_POST["comfirm_password"], FILTER_SANITIZE_STRING);

    $condition = "username = '$User_name'";
    $field = array("username");
    $isEmpty = $validate->isEmpty($_POST);
    $itExist = $validate->itExit($field,"registration",$condition);
    $isEmail = $validate->isItEmail($email);
    $Password_check = 0;

    if($password == $comfirm_password){
        $Password_check = 1;
    }

    if($isEmpty == 1 and $itExist == 1 and $isEmpty == 1 and $Password_check == 1 ){
        $fields = array('fullname','username','email','password');
        $data = array($full_name,$User_name,$email,$password);
        $result = $db->insertter($fields, "registration", $data);
        echo $result;
    }
    if($itExist == 0 ){
            echo "User Alread Exit Please Login";
    }if($isEmail == 0){
        echo "Invalid Email Address...";
    }if($isEmpty == 0){

        echo "All Fields is Required...";
    }if($Password_check == 0){

        echo "password Misstyped..";
    }
   
}
}
?>
