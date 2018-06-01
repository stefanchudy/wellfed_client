<?php

class Controller extends System\MainController
{

    public function init()
    {
        if (!isset($this->input->get['id'])) {
            $this->redirect('admin/users');
        }
        $id = $this->input->get['id'];
        $user = $this->user->getUserById($id);
        if ($user === NULL) {
            $this->redirect('admin/users');
        }

        $this->user->upgradeUser($id);
        $this->_sendNotification($user);

        $this->redirect('admin/users/edit?id=' . $id);
    }

    private function _sendNotification($user)
    {
        if ($this->mailer) {
            $user_name = $user['screen_name'];
            $message = $this->renderWidget('templates/mail_body', array('body' => $this->renderWidget('templates/notifications/upgrade_user_granted', array('user_name' => $user_name))));

            $send_user = clone $this->mailer;
            $send_user->Subject = 'Well Fed Foundation : Account upgrade granted';
            $send_user->msgHTML($message);
            $send_user->clearAllRecipients();
            $send_user->addAddress($user['email']);
            $send_user->send();
        }

    }
}
