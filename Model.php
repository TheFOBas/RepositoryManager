<?php
/**
 * Created by PhpStorm.
 * User: Nerijus
 * Date: 14.3.23
 * Time: 10.34
 */
namespace Plugin\RepositoryManager;

class Model extends \Ip\Internal\Repository\Model{

    public static function whoUsesFile($file){
        $usages = parent::whoUsesFile($file);
        //print_r($usages);
        foreach ($usages as $key=>$usage){
            if($usage['plugin'] == 'Content'){
                $results = ipDb()->selectAll('widget' ,'*', array('id' => $usage['instanceId']));
                foreach ($results as $result){
                    $pageId = ipDb()->fetchValue("SELECT `pageId` FROM ".ipTable('widgetInstance')." instance, ". ipTable('revision') ." revision WHERE widgetId = :id AND instance.revisionId = revision.revisionId ORDER BY instance.createdAt DESC", array('id' => $result['id']));
                    if ($pageId){
                        $page = new \Ip\Page($pageId);
                        $usages[$key]['title'] = $page->getTitle();
                        $usages[$key]['pageUrl'] = $page->getLink();
                    } else {
                        unset($usages[$key]);
                    }
                }
            }
        }
        return $usages;
    }

    public static function removeFile(){

    }

    public static function forceUnbindFile($file){
        $condition = array(
            'fileName' => $file
        );

        $sql= 'DELETE FROM ' . ipTable('repositoryFile') . '
                WHERE filename = :fileName';

        ipDb()->execute($sql, $condition);

        $usages = self::whoUsesFile($file);
        if (empty($usages)) {
            $reflectionModel = \Ip\Internal\Repository\ReflectionModel::instance();
            $reflectionModel->removeReflections($file);
        }
    }


}