<?php
if(!Functions::UserHasPermission('admin_translation_edit')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit Translation';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit translations!';
    header("Location: /admin/translation/list");
    exit;
}
if(!Functions::LanguageExists($GET[3])) {
    $_SESSION['error_title'] = Functions::Translation('global.language');
    $_SESSION['error_message'] = Functions::Translation('text.translation.language.not_exist');
    header("Location: /admin/translation/list");
    exit;
}

$language = $GET[3];
?>

<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2 w-100">
        <div class="d-flex justify-content-end">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filter" id="filter_all" value="filter_all" checked>
                <label class="form-check-label" for="filter_all">All</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filter" id="filter_bot" value="filter_bot">
                <label class="form-check-label" for="filter_bot">Bot</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filter" id="filter_game" value="filter_game">
                <label class="form-check-label" for="filter_game">Game</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="filter" id="filter_web" value="filter_web">
                <label class="form-check-label" for="filter_web">Web</label>
            </div>
        </div>
        <div class="w-100 d-flex justify-content-end">
            <input type="text" name="filter" id="filter" placeholder="Filter" onkeyup="FilterTable()" class="form-control w-25">
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="table" class="table table-dark table-borderless">
        <thead>
            <tr>
                <th>Code</th>
                <th>isBot</th>
                <th>isGame</th>
                <th>isWeb</th>
                <th>Text</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $prepare = Functions::$mysqli->prepare("SELECT id,path,isBot,isGame,isWeb,".$language." FROM core_translations WHERE path != 'dev.control'");
                $prepare->execute();
                $result = $prepare->get_result();
                $result = $result->fetch_all(MYSQLI_ASSOC);
                foreach($result as $res) {
                    ?>
                    <tr>
                        <td>
                            <?= $res['path'];?>
                        </td>
                        <td>
                            <input type="checkbox" name="<?= $res['id'];?>_isBot" class="form-check-input" <?= $res['isBot'] == 1 ? 'checked' : '';?>>
                        </td>
                        <td>
                            <input type="checkbox" name="<?= $res['id'];?>_isGame" class="form-check-input" <?= $res['isGame'] == 1 ? 'checked' : '';?>>
                        </td>
                        <td>
                            <input type="checkbox" name="<?= $res['id'];?>_isWeb" class="form-check-input" <?= $res['isWeb'] == 1 ? 'checked' : '';?>>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="<?= $res['path'];?>" class="form-control" value="<?= $res[$language];?>">
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>

<script>
    function FilterTable() {
        var input, filter, table, tr, td, i, txtValue, bot, game, web;
        var test, test2;
        bot = document.getElementById("filter_bot");
        game = document.getElementById("filter_game");
        web = document.getElementById("filter_web");
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    if(bot.checked) {
                        if(!(tr[i].getElementsByTagName("td")[1].getElementsByTagName("input")[0].checked)) {
                            tr[i].style.display = "none";
                        } 
                    }
                    if(game.checked) {
                        if(!(tr[i].getElementsByTagName("td")[2].getElementsByTagName("input")[0].checked)) {
                            tr[i].style.display = "none";
                        } 
                    }
                    if(web.checked) {
                        if(!(tr[i].getElementsByTagName("td")[3].getElementsByTagName("input")[0].checked)) {
                            tr[i].style.display = "none";
                        } 
                    }
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    }

    $("#filter_bot").on('click', function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = tr[i].getElementsByTagName("td")[0];
                txtValue = txtValue.textContent || txtValue.innerText;
                var check = td.getElementsByTagName('input')[0];
                if((check.checked && (txtValue.toUpperCase().indexOf(filter) > -1)) || check.checked) {
                    tr[i].style.display = "";
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    })

    $("#filter_game").on('click', function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2];
            if (td) {
                txtValue = tr[i].getElementsByTagName("td")[0];
                txtValue = txtValue.textContent || txtValue.innerText;
                var check = td.getElementsByTagName('input')[0];
                if((check.checked && (txtValue.toUpperCase().indexOf(filter) > -1)) || check.checked) {
                    tr[i].style.display = "";
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    })

    $("#filter_web").on('click', function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) {
                txtValue = tr[i].getElementsByTagName("td")[0];
                txtValue = txtValue.textContent || txtValue.innerText;
                var check = td.getElementsByTagName('input')[0];
                if((check.checked && (txtValue.toUpperCase().indexOf(filter) > -1)) || check.checked) {
                    tr[i].style.display = "";
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    })

    $("#filter_all").on('click', function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if(txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }
                else {
                    tr[i].style.display = "none";
                }
            }
        }
    })
</script>