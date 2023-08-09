Kaabar SSO Login
================
Kaabar SSO Login

Installation
------------

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kaabar-sso/yii2-sso "*"
```

or add

```
"kaabar-sso/yii2-sso": "*"
```

to the require section of your `composer.json` file.


About SAML
-------------

Integrate your Yii2 application with a SAML Identity Provider for seamless Single Sign-On authentication.

SAML, or Security Assertion Markup Language, is a widely used standard for enabling Single Sign-On (SSO) and identity federation across different applications and services. It allows secure authentication and authorization exchanges between parties, typically a service provider (SP) and an identity provider (IdP). Here's an overview of SAML:

1. Authentication and Authorization:
SAML facilitates the sharing of authentication and authorization data between different systems. It enables a user to log in once (Single Sign-On) and access multiple applications without needing to re-enter credentials.

2. Components:
SAML involves three main components: the user (principal), the service provider (SP), and the identity provider (IdP). The IdP is responsible for authenticating the user, while the SP relies on the IdP's assertions to grant access.

3. SAML Assertions:
Assertions are the core building blocks of SAML. They contain statements about a user's authentication and attributes. There are three main types: Authentication Assertions, Attribute Assertions, and Authorization Decision Assertions.

4. SAML Profiles:
SAML profiles define how assertions are packaged and exchanged in specific use cases. Common profiles include Web Browser SSO, Single Logout, and Enhanced Client or Proxy (ECP) profiles.

5. SAML Workflow:
When a user accesses an SP, the SP generates a SAML authentication request and redirects the user to the IdP. The IdP authenticates the user and generates a SAML response containing assertions. The user is then redirected back to the SP with the SAML response.

6. Metadata:
SAML uses metadata to share information about entities (IdPs and SPs) involved in the SSO process. Metadata includes details such as endpoints, public keys, and supported bindings.

7. Security:
SAML relies on XML digital signatures and optionally encryption to ensure the integrity and confidentiality of exchanged data. It's crucial to securely manage private keys and certificates.

8. Use Cases:
SAML is commonly used in enterprise environments, educational institutions, and federated systems to enable seamless access to various applications. It's also utilized for cross-domain single sign-on in web applications.

9. SAML Implementations:
Frameworks and libraries, such as the onelogin/php-saml library for PHP applications, provide tools to implement SAML-based SSO easily.

10. Advantages:
SAML reduces the need for users to manage multiple credentials, simplifies user provisioning and deprovisioning, and enhances security by centralizing authentication.

SAML plays a significant role in simplifying user access to multiple systems while maintaining security and privacy standards. Its widespread adoption and compatibility make it a fundamental component of modern identity and access management solutions.


Configuration
-------------

Register ``kaabar\sso\Saml`` to your components in ``config/web.php``.

```php
'components' => [
    'saml' => [
        'class' => 'kaabar\sso\Saml',
        'configFileName' => '@app/config/saml.php', // OneLogin_Saml config file (Optional)
    ]
]
```

To enable this component, a ``OneLogin_Saml`` configuration should be stored in a PHP file. By default, ``configFileName`` is set as ``@app/config/saml.php``, so ensure you create this file beforehand. The file should return the ``OneLogin_Saml`` configuration. For a sample configuration, refer to the [link](https://github.com/onelogin/php-saml/blob/master/settings_example.php) provided.

```php
<?php

$urlManager = Yii::$app->urlManager;
$spBaseUrl = $urlManager->getHostInfo() . $urlManager->getBaseUrl();

return [
    'sp' => [
        'entityId' => $spBaseUrl.'/saml/metadata',
        'assertionConsumerService' => [
            'url' => $spBaseUrl.'/saml/acs',
        ],
        'singleLogoutService' => [
            'url' => $spBaseUrl.'/saml/sls',
        ],
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
    ],
    'idp' => [
        'entityId' => '<URL>', // https://login.microsoftonline.com/51487458-d114-43b9-1234-4e6r75ee86e9
        'singleSignOnService' => [
            'url' => '<URL>', // https://login.microsoftonline.com/51487458-d114-43b9-1234-4e6r75ee86e9/saml2
        ],
        'singleLogoutService' => [
            'url' => '<URL>', // https://login.microsoftonline.com/51487458-d114-43b9-1234-4e6r75ee86e9/saml2
        ],
        'x509cert' => '<x509cert script>',
    ],
];
```

Usage
-----

This extension provides 5 actions:

1. LoginAction

    This action serves as the catalyst to commence the login process towards the Identity Provider, as outlined in the configuration file. By incorporating this action into your controller's actions, you seamlessly activate the Single Sign-On authentication procedure with the specified Identity Provider. This integration streamlines user access through a straightforward setup process. Simply configure and register this action within your controller to enable efficient interaction with the designated Identity Provider, enhancing your application's authentication capabilities.

    ```php
    <?php

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use yii\helpers\Url;


    class SamlController extends Controller {

        // Remove CSRF protection
        public $enableCsrfValidation = false;

        public function actions() {
            return [
                'login' => [
                    'class' => 'kaabar\sso\actions\LoginAction',
                    'returnTo' => Yii::app()->user->returnUrl
                ]
            ];
        }
    }
    ```

    The login method can receive seven optional parameters:

    * `$returnTo` - The target URL the user should be returned to after login..
    * `$parameters` - An array of parameters that will be added to the `GET` in the HTTP-Redirect.
    * `$forceAuthn` - When true the `AuthNRequest` will set the `ForceAuthn='true'`
    * `$isPassive` - When true the `AuthNRequest` will set the `Ispassive='true'`
    * `$strict` - True if we want to stay (returns the url string) False to redirect
    * `$setNameIdPolicy` - When true the AuthNRequest will set a nameIdPolicy element.
    * `$nameIdValueReq` - Indicates to the IdP the subject that should be authenticated.

    You can now access your Identity Provider's login by navigating to: ``saml/login``.

2. AcsAction

    This action is designed to handle the SAML response transmitted by the Identity Provider upon successful login. You have the option to register a callback function, enabling operations such as attribute extraction from the Identity Provider's response and user creation based on those attributes. To implement this functionality, simply add and register this action within your controller's list of actions. This allows you to seamlessly integrate SAML-based authentication processes while customizing user interactions according to your application's needs.

    ```php
    <?php

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use yii\helpers\Url;


    class SamlController extends Controller {

        // Remove CSRF protection
        public $enableCsrfValidation = false;

        public function actions() {
            return [
                ...
                'acs' => [
                    'class' => 'kaabar\sso\actions\AcsAction',
                    'successCallback' => [$this, 'callback'],
                    'successUrl' => Url::to('site/welcome'),
                ]
            ];
        }

        /**
         * @param array $param has 'attributes', 'nameId' , 'sessionIndex', 'nameIdNameQualifier' and 'nameIdSPNameQualifier' from response
         */
        public function callback($param) {
            // do something
            //
            // if (isset($_POST['RelayState'])) {
            // $_POST['RelayState'] - should be returnUrl from login action
            // }
        }
    }
    ```

    **NOTE: Make sure to register the acs action's url to ``AssertionConsumerService`` and the sls actions's url to ``SingleLogoutService`` (if supported) in the Identity Provider.**

3. MetadataAction

    This action will display your application's metadata in XML format. To utilize this feature, simply register the action within your controller's list of actions. This enables you to effortlessly access and present the metadata of your application in a structured XML representation.

    ```php
    <?php

        public function actions() {
            return [
                ...
                'metadata' => [
                    'class' => 'kaabar\sso\actions\MetadataAction'
                ]
            ];
        }
    ```

4. LogoutAction

    By invoking this action, you trigger the Single Logout process aimed at the Identity Provider. To integrate this functionality, effortlessly register the action within your controller's array of actions. This streamlined approach empowers you to initiate Single Logout procedures with the designated Identity Provider, enhancing the security and user management aspects of your application.

    ```php
    <?php
        $session = Yii::$app->session;
        public function actions() {
            return [
                ...
                'logout' => [
                    'class' => 'kaabar\sso\actions\LogoutAction',
                    'returnTo' => Url::to('site/bye'),
                    'parameters' => [],
                    'nameId' => $session->get('nameId'),
                    'sessionIndex' => $session->get('sessionIndex'),
                    'stay' => false,
                    'nameIdFormat' => null,
                    'nameIdNameQualifier' => $session->get('nameIdNameQualifier'),
                    'nameIdSPNameQualifier' => $session->get('nameIdSPNameQualifier'),
                    'logoutIdP' => false, // if you don't want to logout on idp
                ]
            ];
        }
    ```

5. SlsAction

    This action facilitates the processing of SAML logout requests/responses sent by the Identity Provider. To employ this feature, simply register the action within your controller's list of actions. By doing so, you enable your application to effectively handle SAML-based logout interactions, enhancing the overall security and user experience of your system.

    ```php
    <?php

        public function actions() {
            ...

            return [
                ...
                'sls' => [
                    'class' => 'kaabar\sso\actions\SlsAction',
                    'successUrl' => Url::to('site/bye'),
                    'logoutIdP' => false, // if you don't want to logout on idp
                ]
            ]
        }
    ```

Usage
-----

In case of SAMLResponse rejection, incorporate the parameter to the SAML settings:
```
'debug' => true,
```
and the reason will be displayed.


LICENCE
-------

MIT Licence
