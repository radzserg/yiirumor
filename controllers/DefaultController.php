<?php

class DefaultController extends Rm\components\Controller
{

	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionComments()
    {
        $comments = Rm\models\Comment::model()->findAll();

        $this->_returnJson($comments);
    }

    /**
     * load comments
     */
    public function actionComment()
    {
        $authorizedUser = Rm\components\AuthUser::getAuthorizedUser();
        if ($authorizedUser) {
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