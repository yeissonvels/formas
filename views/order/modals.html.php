<!-- Modal de comentarios clientes -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="customerComments">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLiveLabel" class="modal-title">Conversaciones con el cliente <?php icon('phone', true); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12" style="height: 260px; overflow: auto;">
                            <table class="table table-striped" id="dynamic-cus-comments">
                                <?php
                                include (VIEWS_PATH_CONTROLLER . 'customer_comments' . VIEW_EXT);
                                ?>
                            </table>
                        </div>
                        <div class="col-lg-12">
                            <?php if ($canEdit) { ?>
                                <textarea class="form-control" id="customercomment"></textarea>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ($canEdit) { ?>
                        <button class="btn btn-primary" type="button" onclick="saveComment(1);">Nuevo comentario</button>
                    <?php } ?>
                    <?php spinner_icon('spinner', 'sp-cus-comment', true); ?>
                    <button data-bs-dismiss="modal" class="btn btn-secondary" type="button">Salir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de comentarios internos -->

<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="ourComments">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLiveLabel" class="modal-title">Comentarios internos <?php icon('comments', true); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="checkChangeStatusNoComment();"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12" style="height: 260px; overflow: auto;">
                            <table class="table table-striped" id="dynamic-in-comments">
                                <?php
                                    include (VIEWS_PATH_CONTROLLER . 'order_comments' . VIEW_EXT);
                                ?>
                            </table>
                        </div>
                        <div class="col-lg-12" id="div-deliverydate" style="display: none;">
                            <div class="form-group row">
                                <label for="deliverydate" class="col-sm-4 col-form-label">Fecha de entrega</label>
                                <div class="col-sm-8">
                                    <input type="text" name="deliverydate" id="deliverydate" class="form-control" value="">
                                    <input type="hidden" id="controlDeliveryDate" value="0">
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
                <button data-bs-dismiss="modal" class="btn btn-secondary" type="button" onclick="checkChangeStatusNoComment();">Salir</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal de incidencias -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="incidences">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLiveLabel" class="modal-title">Incidencias <?php icon('incidence', true); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#tab-incidences" role="tab" data-bs-toggle="tab" id="inci-lk">Incidencias</a>
                                </li>
                                <li class="nav-item" style="display: none;">
                                    <a class="nav-link" href="#tab-newincidence" role="tab" data-bs-toggle="tab" id="editinci-lk">Editar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab-newincidence" role="tab" data-bs-toggle="tab" id="newinci-lk" onclick="editIncidence(0, idnew)">Nueva</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tab-incidences">
                                    <table class="table table-striped" id="dynamic-incidences">
                                        <?php
                                            include (VIEWS_PATH_CONTROLLER . 'incidences' . VIEW_EXT);
                                            echo $htmlincidences;
                                        ?>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab-newincidence">
                                    <?php include (VIEWS_PATH_CONTROLLER . 'new_incidence' . VIEW_EXT); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php spinner_icon('spinner', 'sp-in-comment', true); ?>
                <button data-bs-dismiss="modal" class="btn btn-secondary" type="button">Salir</button>
            </div>
        </div>
    </div>
</div>