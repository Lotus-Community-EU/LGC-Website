<?php
if(!$user->hasPermission('admin_translation_edit')) {
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
$language_name = Functions::GetLanguageName($language);
if($language != 'English') {
    $english_translations = Functions::GetLanguageTranslations('English');
}
$csrf_token = Functions::CreateCSRFToken('admin_translation_edit');
?>

<div class="row justify-content-end mb-3">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-6">
                <a href="/admin/translation/list" class="btn btn-primary btn-sm mb-2"><?= Functions::Translation('text.back_to_overview');?></a>
                <?php
                if($user->hasPermission('admin_translation_add')) {
                    ?>
                        <form class="row row-cols-lg-auto g-3 align-items-center" id="0" action="/admin/translation/edit" method="POST">
                            <div class="col-12">
                                <input type="text" name="new_language_name" class="form-control" value="<?= $language_name;?>" maxlength="32" required>
                            </div>
                            <div class="col-12">
                                <?php Functions::AddCSRFCheck('admin_translation_edit', $csrf_token); $_SESSION['language_name'] = $language;?>
                                <input type="hidden" name="language_name" value="<?= $language;?>">
                                <button type="button" id="submit_language" onclick="SubmitForm(0)" key="<?= $res['id'];?>" class="btn btn-success w-100"><?= Functions::Translation('text.translation.language.language_name.button');?></button>
                            </div>
                        </form>
                    <?php
                }
                else {
                    echo Functions::Translation('text.translation.language.edit', ['language'], [$language_name]);
                }
                ?>
            </div>
            <div class="col-12 col-lg-6 mt-3 mt-lg-0">
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
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter" id="filter_none" value="filter_none">
                        <label class="form-check-label" for="filter_none">"None"</label>
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-end">
                    <input type="text" name="filter" id="filter" placeholder="Filter" onkeyup="FilterTable()" class="form-control w-50">
                </div>
                <?php if($user->hasPermission('admin_translation_delete') && $language != 'English') { ?>
                    <div class="w-100 d-flex justify-content-end mt-3">
                        <a href="" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_language"><?= Functions::Translation('text.translation.language.delete.button');?></a>
                    </div>
                <?php } ?>
            </div>
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
                <th>Save</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $prepare = Functions::$mysqli->prepare("SELECT id,path,isBot,isGame,isWeb,".$language." FROM core_translations WHERE path != 'dev.control' AND path != 'mcinternal.language'");
                $prepare->execute();
                $result = $prepare->get_result();
                $result = $result->fetch_all(MYSQLI_ASSOC);
                foreach($result as $res) {
                    ?>
                        <tr>
                            <td <?= ($res[$language] == 'none' || strlen($res[$language]) < 1) ? 'style="color: red;"' : '';?>>
                                <?= $res['path']. (($res[$language] == 'none' || strlen($res[$language]) < 1) ? ' (NONE!)' : '');?>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" disabled <?= $res['isBot'] == 1 ? 'checked' : '';?>>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" disabled <?= $res['isGame'] == 1 ? 'checked' : '';?>>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" disabled <?= $res['isWeb'] == 1 ? 'checked' : '';?>>
                                </div>
                            </td>
                            <td>
                                
                                <div class="modal" id="edit_<?= $res['id'];?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="/admin/translation/edit" id="Form<?= $res['id'];?>" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Translation: <?= $res['path'];?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="ergebnis_<?= $res['id'];?>" class="alert text-dark mb-3" style="display: none;"></div>
                                                    <?php
                                                    if($language != 'English') {
                                                        ?>
                                                        <div class="form-group">
                                                            <label>Original English</label>
                                                            <textarea class="form-control" cols="30" rows="2" disabled><?= $english_translations[$res['path']];?></textarea>
                                                        </div>
                                                        <hr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="new_language_<?= $res['id'];?>">Translation - <?= $language;?></label>
                                                        <textarea name="new_language" id="new_language_<?= $res['id'];?>" cols="30" rows="3" class="form-control"><?= $res[$language];?></textarea>
                                                    </div>
                                                    <hr>
                                                    <label>Availability</label>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="isBot_<?= $res['id'];?>" name="isBot" <?= $res['isBot'] == 1 ? 'checked' : '';?>>
                                                        <label for="isBot_<?= $res['id'];?>" class="form-check-label">isBot</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="isGame_<?= $res['id'];?>" name="isGame" <?= $res['isGame'] == 1 ? 'checked' : '';?>>
                                                        <label for="isGame_<?= $res['id'];?>" class="form-check-label">isGame</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="isWeb_<?= $res['id'];?>" name="isWeb" <?= $res['isWeb'] == 1 ? 'checked' : '';?>>
                                                        <label for="isWeb_<?= $res['id'];?>" class="form-check-label">isBot</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php Functions::AddCSRFCheck('admin_translation_edit', $csrf_token); $_SESSION['language_name'] = $language;?>
                                                    <input type="hidden" name="path" value="<?= $res['path'];?>">
                                                    <input type="hidden" name="language_name" value="<?= $language;?>">
                                                    <input type="hidden" name="id" value="<?= $res['id'];?>">
                                                    <button type="button" id="submit_language_<?= $res['id'];?>" onclick="SubmitForm(<?= $res['id'];?>)" key="<?= $res['id'];?>" class="btn btn-success"><?= Functions::Translation('global.edit');?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#edit_<?= $res['id'];?>"><?= Functions::Translation('global.edit');?></button>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>

<?php if($user->hasPermission('admin_translation_delete') && $language != 'English') { ?>
<div class="modal" id="delete_language" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Functions::Translation('text.translation.language.delete.title', ['language_name'], [$language_name]); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p><?= Functions::Translation('text.translation.language.delete.text', ['language_name'], [$language_name]);?></p>
            </div>
            <div class="modal-footer">
                <form action="/admin/translation/delete>" method="POST" class="">
                    <?php Functions::AddCSRFCheck('admin_translation_edit', $csrf_token); $_SESSION['language_name'] = $language?>
                    <input type="hidden" name="language_name" value="<?= $language;?>">
                    <input type="submit" name="reset_password" class="btn btn-success" value="<?= Functions::Translation('text.translation.language.delete.button');?>">
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
    function SubmitForm(id) {
        var FormularData = new FormData(document.getElementById("Form"+id));

        $.ajax({
            type: 'POST',
            url: '/admin/translation/edit',
            data: FormularData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status == 'success') {
                    $("#ergebnis_"+response.id).css("display","block");
                    $("#ergebnis_"+response.id).removeClass("alert-danger");
                    $("#ergebnis_"+response.id).addClass("alert-success");
                    $("#ergebnis_"+response.id).html(response.message);
                }
                else {
                    $("#ergebnis_"+response.id).css("display","block");
                    $("#ergebnis_"+response.id).removeClass("alert-success");
                    $("#ergebnis_"+response.id).addClass("alert-danger");
                    $("#ergebnis_"+response.id).html(response);
                }
                console.log(response);
            }
        });
    }

    function FilterTable() {
        var input, filter, table, tr, td, i, txtValue, bot, game, web;
        var test, test2;
        bot = document.getElementById("filter_bot");
        game = document.getElementById("filter_game");
        web = document.getElementById("filter_web");
        none = document.getElementById("filter_none")
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
                    if(none.checked) {
                        txtValue = tr[i].getElementsByTagName("td")[0];
                        txtValue = txtValue.textContent || txtValue.innerText;
                        var text = td.getElementsByTagName('td')[0];
                        if(!(txtValue.toUpperCase().indexOf('NONE!') > -1)) {
                            tr[i].style.display = "none";
                        }
                    }
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    };

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
    });

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
    });

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
    });

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
    });

    $("#filter_none").on('click', function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("filter");
        filter = input.value = '';
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = tr[i].getElementsByTagName("td")[0];
                txtValue = txtValue.textContent || txtValue.innerText;
                var text = td.getElementsByTagName('td')[0];
                if(txtValue.toUpperCase().indexOf('NONE!') > -1) {
                    tr[i].style.display = "";
                }
                else {
                    tr[i].style.display = "none";
                }
            }      
        }
    });
</script>