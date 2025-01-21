<?php
foreach ($data as $customer) {
    ?>
    <tr>
        <td><?php echo $customer->getId();?></td>
        <td><?php echo $customer->getNif();?></td>
        <td><?php echo $customer->getName();?></td>
        <td><?php echo $customer->getAddress();?></td>
        <td><?php echo $customer->getTelephone();?></td>
        <td><?php echo $customer->getTelephone2();?></td>
        <td><?php echo $customer->getEmail();?></td>
        <!-- <td>< ?php echo $customer->getContactPerson();?></td> -->
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