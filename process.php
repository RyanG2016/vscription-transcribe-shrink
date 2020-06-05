<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['files'])) {
        //The uploadResult and uploadMsg elements are added at same time so they can be reference by same index
        // example: uploadResult[0] and uploadMsg[0] refer to the first file in $_FILES
        $uploadResult = [];
        $uploadMsg = [];
        $path = '../uploads/';
        $extensions = ['wav', 'dss', 'ds2', 'mp3', 'ogg'];

        $nextFileID = $_POST["nextFileID"];
        $nextJobNum = $_POST["nextJobNum"];

        $all_files = count($_FILES['files']['tmp_name']);

        for ($i = 0; $i < $all_files; $i++) {
            $file_name = $_FILES['files']['name'][$i];
            $file_tmp = $_FILES['files']['tmp_name'][$i];
            $file_type = $_FILES['files']['type'][$i];
            $file_size = $_FILES['files']['size'][$i];
            $array = explode('.', $_FILES['files']['name'][$i]);
            $file_ext = strtolower(end($array));

            // enumerating file names
            $enumName = "F".$nextFileID."_UM".$nextJobNum."_".str_replace(" ","_", $file_name);
            $file_name = $enumName;

            $file = $path . $file_name;

            if (!in_array($file_ext, $extensions)) {
                $uploadMsg[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
                $uploadResult[] = "0";
                //unset($_FILES[$i]); //Remove from files array so it doesn't get inserted into DB
                continue;
            }

            //Max file upload size is 128MB. PHP is configured for max size of 128MB
            //if ($file_size > 134217728) {
            if ($file_size > 1048576) {
                $uploadMsg[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
                $uploadResult[] = "0";
                //unset($_FILES[$i]); //Remove from files array so it doesn't get inserted into DB
                continue;
            }

            //if (empty($errors)) {
                $uplSuccess = move_uploaded_file($file_tmp, $file);
                if ($uplSuccess) {
                    $uploadResult[] = "1";
                    $uploadMsg[] = "Upload Successful!";
                } else {
                    $uploadResult[] = "0";
                    $uploadMsg[] =  'An error occurred during upload of ' . $file_name . ' ' . $file_type . '.';
                }
            //}

            $nextFileID++;
            $nextJobNum++;
        }

        if ($uploadMsg) print_r($uploadMsg);
        if ($uploadResult) print_r($uploadResult);

    }
}
