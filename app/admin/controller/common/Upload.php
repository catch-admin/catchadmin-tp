<?php
namespace app\admin\controller\common;

use app\admin\controller\CatchController;
use app\Request;
use app\admin\support\Upload as UploadSupport;

class Upload extends CatchController
{
    public function file()
    {

    }

    public function image(Request $request, UploadSupport $upload)
    {
        return $this->success(
            $upload->setUploadedFile($request->file('image'))->upload()
        );
    }
}
