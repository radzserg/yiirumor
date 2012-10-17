<!--

http://vk.com/developers.php?oid=-1&p=Open_API
https://dev.twitter.com/docs/auth/implementing-sign-twitter

-->

<?= Rm\authPlugin\Vk::initVkApi() ?>

<script type="text/javascript">

    VK.Auth.getLoginStatus(authInfo);

    function authInfo(response) {
        if (response.session) {
            sendSucceed(response.session)
        } else {
            VK.Auth.login(function(response) {
                if (response.session) {
                    sendSucceed(response.session);
                }
            })
        }
        window.close();
    }

    function sendSucceed(data) {
        if (window.opener) {
            window.opener.commentBlock.applyAuth("<?= Rm\models\AuthProvider::VK ?>", data)
        }
    }
</script>

