<?php
/**
 * Created by PhpStorm.
 * User: Nerijus
 * Date: 14.3.23
 * Time: 10.34
 */
namespace Plugin\RepositoryManager; //Replace "YourPluginName" with actual plugin name

class AdminController extends \Ip\Internal\Repository\AdminController{

    public function index(){
        ipAddJs('assets/jquery.lazyload.min.js');
        ipAddJs('assets/script.js');
        ipAddCss('assets/style.css');
        return ipView('view/default.php', array());
    }

    public function whoUsesFile(){
        if (!isset($_GET['file'])) return;
        return new \Ip\Response\Json(Model::whoUsesFile($_GET['file']));
    }


    public function getAll()
    {
        $seek = isset($_POST['seek']) ? (int) $_POST['seek'] : 0;
        $limit = 10000;
        $filter = isset($_POST['filter']) ? $_POST['filter'] : null;

        $browserModel = \Ip\Internal\Repository\BrowserModel::instance();
        $files = $browserModel->getAvailableFiles($seek, $limit, $filter);

        usort ($files , array($this, 'sortFiles') );

        $fileGroups = array();
        foreach($files as $file) {
            $fileGroups[date("Y-m-d", $file['modified'])][] = $file;
        }


        $answer = array(
            'fileGroups' => $fileGroups
        );

        return new \Ip\Response\Json($answer);
    }

    public function deleteFile(){
        if(!isset($_GET['file'])) return false;

        $file = $_GET['file'];


        if (basename($file) == '.htaccess') {
            //for security reasons we don't allow to remove .htaccess files
            return false;
        }

        $realFile = realpath(ipFile('file/repository/' . $file));

        if (strpos($realFile, realpath(ipFile('file/repository/'))) !== 0) {
            return false;
        }

        $model = Model::instance();
        $usages = $model->whoUsesFile($file);
        if ( !isset($_GET['confirm'])) {
            if (empty($usages)){
                $answer = array(
                    'success' => false,
                    'confirm' => true,
                    'uses'    => false
                );
            } else {
                $answer = array(
                    'success' => false,
                    'confirm' => true,
                    'uses' =>  Model::whoUsesFile($_GET['file'])
                );
            }

            return new \Ip\Response\Json($answer);

        }

        $reflections = ipDb()->selectAll('repositoryReflection', 'reflection', array('original' => $file));

        foreach ($reflections as $reflection) {
            $absoluteFilename = ipFile('file/' . $reflection['reflection']);
            if (file_exists($absoluteFilename)) {
                unlink($absoluteFilename);
            }
        }

        if (file_exists($realFile) && is_file($realFile) && is_writable($realFile)) {
            unlink($realFile);
        }
        $answer = array(
            'success' => true
        );

        return new \Ip\Response\Json($answer);
    }

    public function replaceFile(){
        $form = new \Ip\Form();
        $form->addField(new \Ip\Form\Field\Hidden(array(
            'name' => 'lalala',
            'value' => 'lol'
        )));
        $form->addField(new \Ip\Form\Field\File(array(
            'name' => 'file',
            'label' => 'file'
        )));
        $view = ipView('view/form.php', array('form' => $form))->render();
        return new \Ip\Response\Json(array('modal' => $view));

    }

    private function sortFiles($a, $b)
    {
        if ($a['modified'] == $b['modified']) {
            return 0;
        }
        return ($a['modified'] > $b['modified']) ? -1 : 1;
    }
}