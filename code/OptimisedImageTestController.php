<?php

class OptimisedImageTestController extends Controller
{

    public function init()
    {

        parent::init();

        if (!(Director::is_cli() || Permission::check("ADMIN"))) {
            return Security::permissionFailure($this, 'You need to be logged in to run this controller');

        }

    }

    public function test()
    {

        increase_memory_limit_to('128M');

        $images = DataObject::get('OptimisedImage', null, null, null, $this->urlParams['ID'] ? (int) $this->urlParams['ID'] : null);
        $viewer = new SSViewer_FromString('<% control Images %>$SetWidth(100,100)<% end_control %>');

        return $viewer->process(new ArrayData(array('Images' => $images)));

    }

}
