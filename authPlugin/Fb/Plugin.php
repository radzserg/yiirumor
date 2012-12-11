<?php

namespace Rm\authPlugin\Fb;

/**
 *
 * // @todo http://developers.facebook.com/docs/howtos/login/login-as-app/
 *
 */
class Plugin extends \Rm\authPlugin\AbstractClass
{
//  extends \CActiveRecord
    const CODE = 'fb';

    /**
     * Return facebook SDK
     * @return \Facebook
     */
    public static function getSdk()
    {
        require \Yii::getPathOfAlias('trumor.vendors.fb') . '/facebook.php' ;
        return new \Facebook(array(
            'appId' => \Yii::app()->params['fb']['app_id'],
            'secret' => \Yii::app()->params['fb']['app_secret']
        ));
    }

    public static function setJsHandler()
    {
        $fb = self::getSdk();
        $url = $fb->getLoginUrl(array(
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('/trumor/auth/fbgettoken'),
        ));

        $js = <<<JS
$('#auth_block .fb').click(function() {
    var authUrl = "{$url}"
    window.open(authUrl, 'Authorize via FB', 'width=800,height=200,toolbar=0,menubar=0,location=0,resizable=0,scrollbars=0,left=300,top=200')
})
JS;

        $cs = \Yii::app()->getComponent('clientScript');
        /* @var $cs \CClientScript */
        $cs->registerScript('fb_handler', $js, \CClientScript::POS_READY);
    }

}