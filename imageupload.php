<?php

/*
if(!$_SERVER['HTTP_REFERER']) { header('Location: index.php'); die('Access denied'); }
 * 
 */

$actions = trim($_REQUEST['actions']);
$script_dir = trim($_REQUEST['script_dir']);

$upload_dir_path = "images/Avatar/";//Directory target path to save the uploaded image

$allowed_formats = array('image/pjpeg', 'image/jpeg', 'image/jpg', 'gif', 'image/x-png', 'image/png', 'image/gif');//Allowed formats for images

define("DISPLAY_ALLOWED_SIZE", 100);//allowed size for image specify in kb
define("ALLOWED_SIZE", 102400);//allowed size for image specify in bytes
define("ALLOWED_WIDTH", 325);//allowed width for image
define("ALLOWED_HEIGHT", 195);//allowed height for image

/*----- SWITCH CONSTANT ----- */
define('BROWSE_FILE','browse_file');
define('UPLOAD_FILE','upload_file');

switch($actions)
{
    case BROWSE_FILE:
        $new_action = "add_file";
        break;
    case UPLOAD_FILE:
        $files = $_FILES['txt_image_upload'];
        $image_size = getimagesize($_FILES['txt_image_upload']['tmp_name']);
        $file_name = $_FILES['txt_image_upload']['name'];
        $ext = strtolower(strrchr($file_name,'.'));
        $tmp_name = $_FILES["txt_image_upload"]["tmp_name"];
        $type = $_FILES['txt_image_upload']['type'];
        if(in_array($type, $allowed_formats))
        {
            if($_FILES["file"]["error"] > 0)
            {
                $result = "<div id='0' style='color:red;'>Error File Uploaded</div>";
            }
            else
            {
                $time = time();
                $newfilename = "A"."_".$time.$ext;
                $result = UploadImage($files, $tmp_name, $newfilename, $upload_dir_path);
            }
        }
        else
        {
            $result = "<div id='0' style='color:red;'>Invalid File Type</div>";
        }        
        echo $result;
        die;
        break;
    default:
        echo REQUEST_NOT_FOUND;
        header('Location:index.php');
        die;
        break;
}

function UploadImage($files, $tmp_name, $newfilename, $upload_dir_path)
{
    if(move_uploaded_file($tmp_name, "$upload_dir_path/$newfilename"))
    {
        $message = "<div id='1' style='color:green;' class='$newfilename'>Uploaded Successfully</div>";
    }
    else
    {
        $message = "<div id='0' style='color:red;'>Upload failed</div>";
    }
    return $message;
}

?>
<html>
    <head>
        <style type="text/css">
            
        </style>
        <script type="text/javascript" src="<?php echo $script_dir; ?>/custom/addons/ajaxupload/scripts/jquery-1.7.1.js"></script>
        <script type="text/javascript">
            function uploadfiles(obj)
            {
                var rand = new Date().getTime();
                $('#uploadform').attr("action", "imageupload.php?actions=upload_file&id="+rand);
                $('#uploadform').submit();
                $('#postframe').load(function(){
                    iframeContents = $('#postframe').contents().find('body').html();
                    $('#message').html(iframeContents);
                    uploadedxml();
                });
            }
            function uploadedxml()
            {
                var res = $('#message').find('div').attr('class');
                if(res)
                {
                    parent.$('#additional_values_1').val(res);
                    parent.$('#upload_area').html("<img src='images/Avatar/"+res+"' width='135px' height='135px'>");
                    parent.$('#imageupload').colorbox.close();
                }
            }
    </script>
    </head>
    <body class="registrar">
        <table align="center" border="0px">
            <tr>
                <td colspan="2" align="center">
                    <h2>Upload Profile Picture</h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <div id="message">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <form id="uploadform" name="uploadform" method="post" enctype="multipart/form-data" target="postframe">
                        <input type="file" name="txt_image_upload" onchange="uploadfiles(this);" />
                    </form>
                    <br/>
                    <small style="font-weight: bold; font-style:italic;">Supported File Types: gif, jpg, png</small>
                    <iframe id="postframe" name="postframe" style="display:none;"></iframe>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="hidden" name="actions" value="<?php echo $new_action; ?>" />
                </td>
            </tr>
        </table>
    </body>
</html>
