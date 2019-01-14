<?php
class FrameController extends AdminController
{

    public function actionIndex()
    {
        $this->render('/common/index');
    }
}