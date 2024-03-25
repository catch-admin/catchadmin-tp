<?php
namespace app\admin\support\controller;

use think\response\Json;

trait Resource
{
    public function index(): Json
    {
        return $this->paginate($this->model->getList());
    }


    public function save(): Json
    {
        return $this->success($this->model->storeBy($this->request->all()));
    }


    public function read($id): Json
    {
        return $this->success($this->model->firstBy($id));
    }

    public function update($id): Json
    {
        return $this->success($this->model->updateBy($id, $this->request->all()));
    }


    public function delete($id): Json
    {
        return $this->success($this->model->deleteBy($id));
    }
}
