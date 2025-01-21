<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo 'Tiendas'; ?></h4>
        <span><a href="<?php echo getUrl('new', $myController->getUrls()) ?>"><?php echo "Nueva"; ?></a></span>
        <?php update_icon(getUrl('show', $myController->getUrls()));?>
    </div>
    <div class="card-block">
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th>CIF</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>CP.</th>
                    <th>Localidad</th>
                    <th>Provincia</th>
                    <th>Email</th>
                    <th>Web</th>
                    <th>Teléfono</th>
                    <th>Fax</th>
                    <th>Móvil</th>
                    <td>Editar</td>
                </tr>
            </thead>
            <tbody id="dynamic-cus">
            <?php
            foreach ($data as $store) {?>
                <tr>
                    <td><?php echo $store->getCif();?></td>
                    <td><?php echo $store->getStorename();?></td>
                    <td><?php echo $store->getAddress();?></td>
                    <td><?php echo $store->getCp();?></td>
                    <td><?php echo $store->getCity();?></td>
                    <td><?php echo $store->getProvince();?></td>
                    <td><?php echo $store->getEmail();?></td>
                    <td><?php echo $store->getWeb();?></td>
                    <td><?php echo $store->getTelephone();?></td>
                    <td><?php echo $store->getFax();?></td>
                    <td><?php echo $store->getCel();?></td>
                    <td><a href="<?php echo getUrl('edit', $myController->getUrls(), $store->getId());?>"><?php edit_icon(); ?></a></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>