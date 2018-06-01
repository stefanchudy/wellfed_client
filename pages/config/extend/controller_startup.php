<?php

if (($this->input->is_secure==0) && ($this->input->is_local==0)) {
    $https_redirect = $this->input->https_url . '/' . $this->input->url;

    if (count($this->input->get)) {
        $https_redirect .= '?' . http_build_query($this->input->get);
    }
    header('Location: ' . $https_redirect);
}

date_default_timezone_set('UTC');
$folder = str_replace('/', DIRECTORY_SEPARATOR, $this->input->url);

$fullPath = $this->path->www . $folder;


if (file_exists($fullPath) && is_file($fullPath)) {
    return;
}
$url = explode(DIRECTORY_SEPARATOR, $folder);
if ($url[0] == 'admin') {
    $this->html->setCommonPath('admin/common');

    if (count($url) > 1) {
        if (!$this->user->isAdmin()) {
            $this->user->logOff();
        }
        $this->loadModel('common');
        $this->pageData['dashboard'] = $this->model_common->getDashboardData();
        $this->html->setHeaderTags($this->config->header_admin);
    } else {
        if ($this->user->isAdmin()) {
            $this->html->setUser($this->user->logged);
            $this->redirect('admin/dashboard');
        } else {
           $this->redirect('');
        }
    }
} else {
    if ($this->user->logged) {
        if ($this->user->logged['data']['ban'] || isset($this->input->post['logout'])) {
            $this->user->logOff(false);
        }
    } else {
        if (isset($this->input->post['login_form'])) {
            $form_data = $this->input->post['login_form'];
            $this->user->login($form_data['username'], $form_data['password']);
        }
        if (isset($this->input->post['register'])) {
            $register = $this->user->add($this->input->post['register']);
            if (count($register['errors'])) {
                $this->errors = $register['errors'];
                $this->errors['register'] = 1;
            } else {
                $this->user->login($this->input->post['register']['email'], $this->input->post['register']['password']);
            }
        }
    }
}