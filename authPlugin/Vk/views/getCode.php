<? if ($error) : ?>
    <div  class="alert alert-error">Sorry we can't authorize you via VK</div>
<? else : ?>
    <script type="text/javascript">
        window.opener.commentBlock.applyAuth()
        window.close();
    </script>
<? endif; ?>
