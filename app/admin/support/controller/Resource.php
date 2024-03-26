<?php
namespace app\admin\support\controller;

trait Resource
{
    public function index()
    {
        return $this->success($this->model->getList());
    }


    public function save()
    {
        return $this->success($this->model->storeBy($this->request->all()));
    }


    public function read($id)
    {
        return $this->success($this->model->firstBy($id));
    }

    public function update($id)
    {
        return $this->success($this->model->updateBy($id, $this->request->all()));
    }


    public function delete($id)
    {
        return $this->success($this->model->deleteBy($id));
    }

    public function enable($id)
    {
        return $this->success($this->model->toggleBy($id));
    }
}
