<?php

namespace App\Repositories\Group;

use App\Repositories\Group\groupInterface as GroupInterface;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupRepository implements GroupInterface
{
    public $group;


    function __construct(Group $group) {
	    $this->group = $group;
    }


    public function getAll()
    {
        return $this->group->getAll();
    }


    public function find($id)
    {
        return $this->group->findGroup($id);
    }


    public function delete($id)
    {
        return $this->group->deleteGroup($id);
    }

    public function create($name){
        
        $group = new Group();
        $group->name = $name;
        $group->save();
        
        return $group;
    }

    
}