<div class="container">
    <h2>&nbsp;</h2>
    <div class="alert alert-danger">
        <p>
        ##########################################<br>
        ############### APP DEBUG #################<br>
        ##########################################
        </p>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Errors</th>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach ($errors as $error) {
        ?>
            <tr>
                <td><?php echo $error; ?></td>
            </tr>
        <?php
            }
        ?>
        </tbody>
    </table>
</div>
