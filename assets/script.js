/**
 * Created by Nerijus on 14.3.20.
 */
(function($){
    "use strict";
    function setupItem($file) {
        // icon
        var data = $file.data('fileData');
        var iconClass = 'fa fa-file-o';
        var type = '';
        switch (data.ext) {
            case 'gif':
            case 'jpeg':
            case 'jpg':
            case 'png':
                type = 'img';
                iconClass = 'fa fa-picture-o';
                break;
            case 'pdf':
                iconClass = 'fa fa-print';
                break;
            case 'txt':
                iconClass = 'fa fa-file-text-o';
                break;
            case 'exe':
                iconClass = 'fa fa-windows';
                break;
            case '7z':
            case 'apk':
            case 'arc':
            case 'arj':
            case 'cab':
            case 'gz':
            case 'iso':
            case 'rar':
            case 'tar':
            case 'tar.gz':
            case 'tgz':
            case 'zip':
                iconClass = 'fa fa-archive';
                break;
            case 'aac':
            case 'cda':
            case 'm4a':
            case 'mp3':
            case 'mp4':
            case 'ogg':
            case 'wav':
            case 'wma':
                iconClass = 'fa fa-music';
                break;
            case 'aaf':
            case 'avi':
            case 'flv':
            case 'm4v':
            case 'mkv':
            case 'mpeg':
            case 'mpg':
            case 'mov':
            case 'wmv':
                iconClass = 'fa fa-film';
                break;
        }
        $file.addClass(type);
        $file.find('.icon').addClass(iconClass);
        // thumbnail
        $file.find('img')
            .attr('data-original', data.previewUrl)
            .attr('alt', data.fileName)
            .attr('title', data.fileName);
        $file.find('img').lazyload({
            effect : "fadeIn",
            placeholder: 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs='
        });

        // filename
        $file.find('.title').html('<a href="'+data.originalUrl+'" target="_blank"> '+data.fileName+' </a>');
        // file data
        $file.attr('data-file', data.fileName); // unique attribute to recognize required element
        $file.data('fileData', data);

        $file.find('.delete').on('click', function(){
            deleteFile(data.fileName);
        });

        $file.find('.replace').on('click', function(){
            replaceFile(data.fileName);
        });

        $.getJSON(ip.baseUrl,
            {
                aa : "RepositoryManager.whoUsesFile",
                file : data.fileName
            }).done(function(responce){
                $file.find('.count').html(responce.length);
                $.each(responce, function(key, usage){
                    if (usage.plugin == 'Content'){
                        $file.find('.used_in').append('Used in page: <a href="'+usage.pageUrl+'" target="_blank">' + usage.title + '</a><br>');
                    } else {
                        $file.find('.used_in').append('Used in table: ' + usage.plugin + '<br>');
                    }
                });
            });
    }

    function replaceFile(fileName){
        $.getJSON(ip.baseUrl,
            {
                aa : "RepositoryManager.replaceFile",
                file : fileName
            }).done(function(data){
                console.log(data);
                var $modal = $('#replaceModal');
                $modal.modal();
                $modal.find('.modal-body').html(data.modal);
                ipInitForms();
            });
    }


    function deleteFile(name){
        $.getJSON(ip.baseUrl,{
            aa: "RepositoryManager.deleteFile",
            file : name
        }).done(function(data){
                console.log(data);
                var $modal = $('#deleteModal');
                $modal.modal();
                if (data.uses){
                    $modal.find('.count').html(data.uses.length);
                    $modal.find('.used_in').html('');
                    $.each(data.uses, function(key, usage){
                        if (usage.plugin == 'Content'){
                            $modal.find('.used_in').append('Used in page: <a href="'+usage.pageUrl+'" target="_blank">' + usage.title + '</a><br>');
                        } else {
                            $modal.find('.used_in').append('Used in table: ' + usage.plugin + '<br>');
                        }
                    });
                    $modal.find('.confirm').on('click', function(){
                        deleteResponce(name, $modal);
                    });
                } else {
                    $modal.find('.confirm').on('click', function(){
                        deleteResponce(name, $modal);
                    });
                }
            });
    }

    function deleteResponce(name, $modal){
        $.getJSON(ip.baseUrl,{
            aa: "RepositoryManager.deleteFile",
            file : name,
            confirm: true
        }).done(function(data){
            if (data.success){
                $modal.modal('hide');
            }
        });
    }

    function printGroup(key, group){
        var $group_wrapper = $('<div/>');
        $group_wrapper.addClass('group');
        $group_wrapper.addClass(key);
        $group_wrapper.append('<h2>'+key+'</h2>')
        var $group_container = $('<ul/>');
        var $template = $('.ipsFileTemplate');
        $.each(group, function(key, file){
            var $newItem = $template.clone().removeClass('ipsFileTemplate');
            $newItem.data('fileData', file);
            $group_container.append($newItem);
        });
        $('.viewer').append($group_wrapper.append($group_container));
        $group_wrapper.find('h2').on('click', function(){
            openGroup($(this).next());
        });
    }
    function openGroup($group){
        $group.slideToggle(500, function () {});

        //setup items only on opening of group
        $group.children().each(function(){
            setupItem($(this));
        });
    }

    $(document).ready(function(){
        $("img.lazy").show().lazyload();
        $.getJSON(ip.baseUrl,
            {
                aa: "RepositoryManager.getAll"
            }).done(function(data){
                $.each(data.fileGroups, function(key, group){
                    printGroup(key, group);
                });
                openGroup($('.viewer .group').first().find('ul'));
            });
    });
})($);








