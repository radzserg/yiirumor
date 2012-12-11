<?php

class DefaultController extends Rm\components\Controller
{

	public function actionIndex()
	{
        $authPluginsConfig = require_once Yii::getPathOfAlias('trumor.config')
            . '/authPlugins.php';

        $authPlugins = array();
        foreach (array_keys($authPluginsConfig) as $code) {
            $authPlugins[$code] = \Rm\authPlugin\Factory::factory($code);
        }

		$this->render('index', array(
		    'user' => Rm\components\AuthUser::getAuthorizedUser(),
            'authPlugins' => $authPlugins,
        ));
	}

    public function actionComments()
    {
        $comments = Rm\models\Comment::model()
            ->published()
            ->with('user')
            ->findAll();

        $result = array();
        foreach ($comments as $comment) {
            /* @var $comment Rm\models\Comment */
            $result[] = array(
                'comment' => $comment->comment,
                'author_name' => $comment->user->username,
                'author_photo' => $comment->user->photo,
            );
        }

        $this->_returnJson($result);
    }

    /**
     * load comments
     */
    public function actionComment()
    {
        $authorizedUser = Rm\components\AuthUser::getAuthorizedUser();
        if (!$authorizedUser) {
            throw new CException("You have to be authorized");
        }
        if (Yii::app()->getRequest()->isAjaxRequest) {
            $requestType = Yii::app()->request->getRequestType();

            $data = CJSON::decode(file_get_contents('php://input'));

            if ($requestType == "POST") {
                $data = CJSON::decode(file_get_contents('php://input'));
                $comment = new Rm\models\Comment();
                $comment->setAttributes($data);
                $comment->user_id = $authorizedUser->id;
                $comment->save();
                $this->_returnJson($comment->getAttributes());
            } elseif ($requestType == "DELETE") {
                $id = Yii::app()->getRequest()->getParam('id');
                $comment = Rm\models\Comment::model()->deleteByPk($id);
            }
        }
    }

}