<?php


namespace kaabar\sso;

use Yii;
use yii\base\BaseObject;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Settings;


/**
 * This class encapsulates the functionality of the OneLogin_Saml2_Auth class by instantiating 
 * it with configurations derived from the configFileName variable located within the @app/config 
 * directory. By leveraging this approach, the class streamlines the process of configuring and 
 * utilizing the OneLogin_Saml2_Auth class, facilitating efficient integration of SAML-based 
 * authentication within the application.
 */
class Saml extends BaseObject
{
    /**
     * This file holds the configuration settings for the OneLogin_Saml2_Auth class. 
     * It serves as a repository for essential parameters and options required to set up 
     * and customize the SAML-based authentication process within an application. 
     * Storing these configurations in this file allows for easy management and modification 
     * of authentication behavior, enhancing the flexibility and security of the authentication workflow.
     */
    public $configFileName = '@app/config/saml.php';

    /**
     * Auth instance.
     * @var Auth
     */
    private $instance;

    /**
     * Configurations for OneLogin_Saml2_Auth encompass a set of parameters and settings that tailor 
     * the behavior of the SAML-based authentication process. These configurations define aspects such 
     * as the Identity Provider's metadata, the Service Provider's attributes, cryptographic keys, and 
     * binding protocols. By accurately configuring OneLogin_Saml2_Auth, organizations can ensure seamless 
     * Single Sign-On (SSO) interactions, secure data exchange, and efficient management of user authentication 
     * and attributes within their applications.
     * 
     * @var array
     */
    private $config;

    public function init()
    {
        parent::init();

        $configFile = Yii::getAlias($this->configFileName);

        $this->config = require($configFile);
        $this->instance = new Auth($this->config);
    }

    /**
     * Invoking the login method on the OneLogin_Saml2_Auth initiates the authentication process. 
     * This action triggers the communication with the Identity Provider, leading to the presentation 
     * of the IdP's login page to the user. By triggering this method, the application begins the Single 
     * Sign-On (SSO) flow, facilitating secure user authentication through the established SAML-based framework.
     */
    public function login($returnTo = null, $parameters = array(), $forceAuthn = false, $isPassive = false)
    {
        return $this->instance->login($returnTo, $parameters, $forceAuthn, $isPassive);
    }

    /**
     * Call the logout method on OneLogin_Saml2_Auth.
     */
    public function logout($returnTo = null, $parameters = array(), $nameId = null, $sessionIndex = null)
    {
        return $this->instance->logout($returnTo, $parameters, $nameId, $sessionIndex);
    }

    /**
     * Call the getAttributes method on OneLogin_Saml2_Auth.
     */
    public function getAttributes()
    {
        return $this->instance->getAttributes();
    }

    /**
     * Call the getAttribute method on OneLogin_Saml2_Auth.
     */
    public function getAttribute($name)
    {
        return $this->instance->getAttribute($name);
    }

    /**
     * Returns the metadata of this Service Provider in xml.
     * @return string Metadata in xml
     * @throws \Exception
     * @throws \OneLogin_Saml2_Error
     */
    public function getMetadata()
    {
        $samlSettings = new Settings($this->config, true);
        $metadata = $samlSettings->getSPMetadata();

        $errors = $samlSettings->validateMetadata($metadata);
        if (!empty($errors)) {
            throw new \Exception('Invalid Metadata Service Provider');
        }

        return $metadata;
    }

    /**
     * Call the processResponse method on OneLogin_Saml2_Auth.
     */
    public function processResponse()
    {
        $this->instance->processResponse();
    }

    public function processSLO()
    {
        $this->instance->processSLO();   
    }

    /**
     * Call the getErrors method on OneLogin_Saml2_Auth.
     */
    public function getErrors()
    {
        return $this->instance->getErrors();
    }

    /**
     * Call the getLastErrorReason method on OneLogin_Saml2_Auth.
     */
    public function getLastErrorReason()
    {
        return $this->instance->getLastErrorReason();
    }

    /**
     * Check if debug is enabled on OneLogin_Saml2_Auth.
     */
    public function isDebugActive()
    {
        $samlSettings = $this->instance->getSettings();
        return $samlSettings->isDebugActive();
    }
}
