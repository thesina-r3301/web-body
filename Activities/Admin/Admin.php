<?php

namespace Admin;

class Admin
{
    protected $currentDomain;
    protected $basePath;

    function __construct()
    {
        $this->currentDomain = CURRENT_DOMAIN;
        $this->basePath = BASE_PATH;
    }

    protected function redirect($url)
    {
        header('Location: ' . trim($this->currentDomain, '/ ') . '/' . trim($url, '/ '));
        exit;
    }

    protected function redirectBack()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    protected function saveImage($image, $imagePath, $imageName = NULL)
    {
        $extension = explode('/', $image['type'][1]);

        if ($imageName) {
            $imageName = $imageName . '.' . $extension;
        } else {
            $imageName = date("Y-m-d-H-i-s") . '.' . $extension;
        }

        $imageTmp = $image['tmp_name'];
        $imagePath = 'Public' . $imagePath . '/';

        if (is_uploaded_file($imageTmp)) {
            if (move_uploaded_file($imageTmp, $imagePath . $imageName)) {
                return $imagePath . $imageName;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    protected function removeImage($path)
    {
        $path = trim($this->basePath, '/ ') . '/' . trim($path, '/ ');

        if (file_exists($path)) {
            unlink($path);
        }
    }
}