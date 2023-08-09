<?php

namespace kaabar\sso\actions;

use Yii;

/**
 * This class provides action for initiating login process using Saml.
 */
class LoginAction extends BaseAction
{

    /**
     * @var string An url which user will be redirected to after logout.
     */
    public $returnTo;
    
    /**
     * Initiate login process using Saml.
     * @return void
     */
    public function run()
    {
        $this->samlInstance->login();
    }

}
