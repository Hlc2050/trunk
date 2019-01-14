<?php
/**
 * User: fu
 * Date: 13-8-29
 * Time: 下午1:33
 */

class AdminAccessControlFilter extends CAccessControlFilter
{
    protected function preFilter($filterChain)
    {
        $app=Yii::app();
        $request=$app->getRequest();
        $user = Yii::app()->controller->module->getComponent('adminuser');
        $verb=$request->getRequestType();
        $ip=$request->getUserHostAddress();

        foreach($this->getRules() as $rule)
        {
            if(($allow=$rule->isUserAllowed($user,$filterChain->controller,$filterChain->action,$ip,$verb))>0) // allowed
                break;
            else if($allow<0) // denied
            {
                $this->accessDenied($user,$this->resolveErrorMessage($rule));
                return false;
            }
        }

        return true;
    }
}