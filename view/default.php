<div class="row">
    <div class="viewer list"></div>
</div>

<div class="templates hide">
    <li class="ipsFileTemplate">
        <div class="buttons">
            <i class="fa fa-trash-o delete"></i>
            <i class="fa fa-edit replace"></i>
        </div>
        <div class="image"><img  data-original="" alt="" title="" class="lazy" /></div>
        <div class="data"><i class="icon"></i><h4 class="title"></h4>Used: <span class="count"></span> times.
            <hr>
            <p class="used_in"></p>
        </div>
    </li>
</div>
<div id="deleteModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure?</h4>
            </div>
            <div class="modal-body">
                File is used <span class="count"></span> times in page.
                <p class="used_in"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="confirm btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
<div id="replaceModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure?</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="confirm btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
