<? /* @var $this DefaultController */ ?>
<div id="comment_block">


    <h4>Comment</h4>
    <div id="auth_block">
        <a href="javascript:void(0)" class="<?= Rm\models\AuthProvider::VK ?>">
            <img id="vk_provider" />
        </a>
    </div>

    <div id="comments">

    </div>

    <form id="comment_form" method="post" class="well">
        <textarea name="comment" rows="3" id="new_comment_text"></textarea>

        <div>
            <input type="button" class="btn btn-primary" value="Send" id="save_comment">
        </div>
    </form>
</div>

<script>

    var app = {
        Views: {},
        Models: {},
        Collections: {}
    }

    var commentBlock;

    $(document).ready(function() {

        app.Models.Comment = Backbone.Model.extend({
            url: function() {
                if (this.get('id')) {
                    return "<?= $this->createUrl('comment') ?>/id/" + this.get('id')
                } else {
                    return "<?= $this->createUrl('comment') ?>"
                }
            }
        })

        var comments = new (Backbone.Collection.extend({
            model: app.Models.Comment,
            url: "<?= $this->createUrl('comments') ?>"
        }))

        app.Views.CommentRow = Backbone.View.extend({
            tagName: "div",

            events: {
                "click #remove_comment": "removeComment"
            },

            template: _.template($("#comment_row").html()),

            removeComment: function() {
                this.model.destroy()
                this.remove()
            },

            render: function() {
                this.$el.html(this.template(this.model.toJSON()))
                return this
            }
        })

        commentBlock = new (Backbone.View.extend({
            el: $("#comment_block"),

            initialize: function() {
                comments.fetch()
                comments.bind('reset', this.addAll, this);
                comments.bind('add', this.addOne, this);
            },

            events: {
                "click #save_comment": "saveComment",
                "submit": "saveComment"
            },

            saveComment: function() {
                comments.create({
                    "comment": this.$("#new_comment_text").val()
                })
                $("#new_comment_text").val('')
            },

            applyAuth: function(authProviderCode, authData) {
                $.ajax({
                    type: 'post',
                    url: '<?= $this->createUrl('auth/applyauth')?>',
                    data: {'authProviderCode': authProviderCode, 'authData': authData},
                    success: function() {}
                })
            },

            /**
             * Add new comment
             */
            addOne: function(comment) {
                var commentRow = new app.Views.CommentRow({model: comment})
                $("#comments").append(commentRow.render().el)
            },

            /**
             * Load all comments
             */
            addAll: function() {
                comments.each(this.addOne)
            }

        }))

        // @todo move it authPlugin/*
        $('#auth_block .vk').click(function() {
            var authUrl = "<?= Rm\authPlugin\Vk::getAuthUrl() ?>"
            window.open(authUrl, 'Authorize via VK', 'width=500,height=200,toolbar=0,menubar=0,location=0,resizable=0,scrollbars=0,left=300,top=200')

            //window.open('<?= $this->createUrl('auth/vk') ?>', 'Authorize via VK', 'width=500,height=200,toolbar=0,menubar=0,location=0,resizable=0,scrollbars=0,left=300,top=200')
        })



    })

</script>


<script type="text/template" id="comment_row">
    <div class="remove_comment">
        <i class="icon-remove" id="remove_comment"></i>
    </div>
    <div class="comment">
        <%= comment %>
    </div>
</script>