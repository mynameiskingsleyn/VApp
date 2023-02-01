<?php

// If you choose to use ENV vars to define these values, give this IdP its own env var names
// so you can define different values for each IdP, all starting with 'SAML2_'.$this_idp_env_id
$this_idp_env_id = 'sso2';

//This is variable is for simplesaml example only.
// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = env('SAML2_'.$this_idp_env_id.'_IDP_HOST', 'https://dealeradmindev.v2soft.com/sso');

return $settings = array(

    /*****
     * One Login Settings
     */

    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', true),

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_SP_x509',''),
        'privateKey' => env('SAML2_'.$this_idp_env_id.'_SP_PRIVATEKEY',''),

        // Identifier (URI) of the SP entity.
        // Leave blank to use the '{idpName}_metadata' route, e.g. 'test_metadata'.
        'entityId' => env('SAML2_'.$this_idp_env_id.'_SP_ENTITYID',''),

        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the '{idpName}_acs' route, e.g. 'test_acs'
            'url' => '',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the '{idpName}_sls' route, e.g. 'test_sls'
            'url' => '',
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
         // Identifier of the IdP entity  (must be a URI)
        'entityId' => env('SAML2_'.$this_idp_env_id.'_IDP_ENTITYID', $idp_host . '/metadata.php'),
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SSO_URL', $idp_host . '/SSOService.php'),
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SL_URL', $idp_host . '/SingleLogoutService.php'),
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_IDP_x509', 'MIID2DCCAsCgAwIBAgIUSF90RSMgTFabZGCUsTjx7e7jYQ8wDQYJKoZIhvcNAQEF
BQAwRDEPMA0GA1UECgwGVjJTT0ZUMRUwEwYDVQQLDAxPbmVMb2dpbiBJZFAxGjAY
BgNVBAMMEU9uZUxvZ2luIEFjY291bnQgMB4XDTE5MTExNTA3MjYwOVoXDTI0MTEx
NTA3MjYwOVowRDEPMA0GA1UECgwGVjJTT0ZUMRUwEwYDVQQLDAxPbmVMb2dpbiBJ
ZFAxGjAYBgNVBAMMEU9uZUxvZ2luIEFjY291bnQgMIIBIjANBgkqhkiG9w0BAQEF
AAOCAQ8AMIIBCgKCAQEA02U4YvMv4xLGGEwl3iICwIvGS7Mi/5HQhMDbJ3v5+xM3
LKUn/+0ZgpotHXXesdSPXIdDylDmaI0xBB63VPa8K4cVDFEN3OnqupPHOdjGOzH4
dADr3N0RuthW4FgFFsgwBun1Lmu74w1dmEvV7LlguNbk8XwAaxyB/fqIuhBZMoOn
B6SaYf7CFNlgug2ImsY7T6H8aZ4US9aRRlc/jueuZYCAlrQpOb494InxlyFA/NFz
pg5OQj31Yk2wj5t+qloPVOVs6Fb9zfr+PTwQPvyNgefCWlK027lyIps9/mIVZXfa
wgKHhXLMOKLQrHQRxbKkUiYb2ece3/SYTwCz7s0yhwIDAQABo4HBMIG+MAwGA1Ud
EwEB/wQCMAAwHQYDVR0OBBYEFJANX2B1hjPczVvKJdUTPZa9+C1HMH8GA1UdIwR4
MHaAFJANX2B1hjPczVvKJdUTPZa9+C1HoUikRjBEMQ8wDQYDVQQKDAZWMlNPRlQx
FTATBgNVBAsMDE9uZUxvZ2luIElkUDEaMBgGA1UEAwwRT25lTG9naW4gQWNjb3Vu
dCCCFEhfdEUjIExWm2RglLE48e3u42EPMA4GA1UdDwEB/wQEAwIHgDANBgkqhkiG
9w0BAQUFAAOCAQEAWZ6JEIKAmUPsxV6Cv61oTpXqj3xG6f8E5r7HkHLdxVRtfoR+
s/XYOJeujFS6r3w/pdMzip1AQMFuvfUEiIWSbAx30PE2DYIcfrWBxxwlu3MXnFdw
tAJY/S5ySRFagOkV97DE5GL3ixpXuxBvT/y4p7ogZHqqo9di8ky/JLAbsAQ51oXj
qwTqYv2BCEWYvkdiaGCghdnmlMRgh60kvWmJ6SgTtWmHJaNtuVhhNTQUulkch8Qi
q0TgfedzNaEHdR4DSsbDEf2GI+SOUVpMx9mqPjKNzs2yQWJLTtX4XQpohxvRGcXC
Ttcsk+qvfn2ZLulML5S1ESda85bl3P0UvP0Mkw=='),
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),



    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'SATHISH KUMAR',
            'emailAddress' => 'sathisha@v2soft.com'
        ),
        'support' => array(
            'givenName' => 'Support Ore Team',
            'emailAddress' => 'sathisha@v2soft.com'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'V2Soft',
            'displayname' => 'V2Soft',
            'url' => 'https://www.v2soft.com/'
        ),
    ),

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
