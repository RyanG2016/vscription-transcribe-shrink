<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['files'])) {
        $errors = [];
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
                $errors[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
            }

            //Max file upload size is 15MB. PHP is configured for max size of 128MB
            if ($file_size > 134217728) {
                $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
            }

            if (empty($errors)) {
                move_uploaded_file($file_tmp, $file);
            }

            $nextFileID++;
            $nextJobNum++;
        }

        if ($errors) print_r($errors);
    }
}