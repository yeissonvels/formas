<script>
    function delete_url(id, url) {
        if (confirm('¿Esta seguro que desea borrar esta url "' + url + '"?')) {
            document.location.href = '<?php echo getUrl('delete', $data['urls']); ?>' + id;
        }
    }
</script>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Friendly Urls</h4>
        <span><a href="<?php echo getUrl('new', $data['urls']); ?>"><?php icon('save', true); ?></a></span>
        <?php update_icon(getUrl('show', $data['urls'])); ?>
    </div>
    <div class="card-block">
        <table class="table table-responsive">
            <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Url friendly</th>
                <th>Url friendly Edit</th>
                <th>Controlador</th>
                <th>Método</th>
                <th>Tipo</th>
                <th><?php echo trans('edit') ?></th>
                <th><?php echo trans('delete') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data['dataurls'] as $url) {
                $id = $url->getId();
            ?>
                <tr>
                    <td><?php echo $id ?></td>
                    <td><?php echo $url->getUrlname() ?></td>
                    <td><?php echo $url->getDescription() ?></td>
                    <td><?php echo $url->getUrlfriendly() ?></td>
                    <td><?php echo $url->getUrlfriendlyedit() ?></td>
                    <td><?php echo $url->getControllername() ?></td>
                    <td><?php echo $url->getMethod() ?></td>
                    <td><?php echo getUrlType($url->getType()); ?></td>
                    <td>
                        <a href="<?php echo getUrl('edit', $data['urls'], $id) ?>"><?php icon('edit', true); ?></a>
                    </td>
                    <td>
                        <a href="#"
                           onclick="delete_url(<?php echo $id ?>, '<?php echo $url->getUrlname() ?>')" style="color: red;"><?php icon('delete', true); ?></a>

                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>