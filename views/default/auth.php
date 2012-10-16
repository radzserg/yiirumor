<!-- http://vk.com/developers.php?oid=-1&p=Open_API -->

<div id="vk_api_transport"></div>
<script type="text/javascript">
    window.vkAsyncInit = function() {
        VK.init({
            apiId: "YOUR_API_ID"
        });


        function authInfo(response) {
            if (response.session) {
                alert('user: '+response.session.mid);
            } else {
                alert('not auth');
            }
        }
        VK.Auth.getLoginStatus(authInfo);
        VK.UI.button('login_button');

        $("login_button").click(function() {
            VK.Auth.login(authInfo)
        })
    };

    setTimeout(function() {
        var el = document.createElement("script");
        el.type = "text/javascript";
        el.src = "http://vkontakte.ru/js/api/openapi.js";
        el.async = true;
        document.getElementById("vk_api_transport").appendChild(el);
    }, 0);

</script>

<div id="login_button"></div>

<a href="#">FB</a>
<a href="#">TW</a>


