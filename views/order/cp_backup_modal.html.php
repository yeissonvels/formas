<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade show" id="incidences">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLiveLabel" class="modal-title">Incidencias <?php icon('incidence', true); ?></h5>
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#profile" role="tab" data-toggle="tab">profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#buzz" role="tab" data-toggle="tab">buzz</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#references" role="tab" data-toggle="tab">references</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="profile">
                                    <table class="table table-striped" id="dynamic-in-comments">
                                        <?php
                                        include (VIEWS_PATH_CONTROLLER . 'incidences' . VIEW_EXT);
                                        ?>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="buzz">
                                    Nueva
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="references">
                                    asdf
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <?php if ($canEdit) { ?>
                                <textarea class="form-control" id="intercomment"></textarea>
                                <input type="hidden" value="0" id="statuscomment">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($canEdit) { ?>
                    <button class="btn btn-primary" type="button" onclick="saveComment(0);">Nuevo comentario</button>
                <?php } ?>
                <?php spinner_icon('spinner', 'sp-in-comment', true); ?>
                <button data-dismiss="modal" class="btn btn-secondary" type="button">Salir</button>
            </div>
        </div>
    </div>
</div>