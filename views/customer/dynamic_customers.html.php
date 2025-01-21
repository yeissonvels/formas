<?php
if ($data) { ?>
<thead>
<tr>
    <th>Id</th>
    <th>NIF/ CIF</th>
    <th>Nombre</th>
    <th>Dirección</th>
    <th>CP.</th>
    <th>Localidad</th>
    <th>Provincia</th>
    <th>Teléfono</th>
    <th>Fax</th>
    <th>Móvil</th>
    <th>Email</th>
    <th>Persona de contacto</th>
    <th>Active</th>
    <td>Editar</td>
</tr>
</thead>
<tbody>
<?php
foreach ($data as $customer) {
    ?>
    <tr>
        <td><?php echo $customer->getId();?></td>
        <td><?php echo $customer->getNif();?></td>
        <td><?php echo $customer->getName();?></td>
        <td><?php echo $customer->getAddress();?></td>
        <td><?php echo $customer->getCp();?></td>
        <td><?php echo $customer->getCity();?></td>
        <td><?php echo $customer->getProvince();?></td>
        <td><?php echo $customer->getTelephone();?></td>
        <td><?php echo $customer->getFax();?></td>
        <td><?php echo $customer->getCel();?></td>
        <td><?php echo $customer->getEmail();?></td>
        <td><?php echo $customer->getContactPerson();?></td>
        <td>
            <?php
            echo $customer->getActive() == 1 ? "Si" : "No";
            ?>
        </td>
        <td><a href="<?php echo getUrl('edit', $myController->getUrls(), $customer->getId());?>"><?php edit_icon(); ?></a></td>
    </tr>
    <?php
}
?>
</tbody>
<?php
} else {
    errorMsg('Sin resultados');
}
