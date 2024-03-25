<?php
namespace app\admin\model\permissions\traits;

use app\admin\model\permissions\Jobs;
use think\model\relation\BelongsToMany;

trait HasJobsTrait
{
    /**
     * @return BelongsToMany
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Jobs::class, 'admin_has_jobs', 'job_id', 'admin_id');
    }

    /**
     * @return array|\think\Collection|BelongsToMany[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getJobs()
    {
        return $this->jobs()->select();
    }

    /**
     * @param array $jobs
     * @return array|\think\model\Pivot|true
     * @throws \think\db\exception\DbException
     */
    public function attachJobs(array $jobs)
    {
        if (empty($jobs)) {
            return true;
        }

        sort($jobs);

        return $this->jobs()->attach($jobs);
    }

    /**
     * @param array $jobs
     * @return int
     */
    public function detachJobs(array $jobs = [])
    {
        if (empty($jobs)) {
            return $this->jobs()->detach();
        }

        return $this->jobs()->detach($jobs);
    }
}
