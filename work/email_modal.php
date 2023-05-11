<?php
include_once('session.php');
include_once('functions.php');

function formHtml($arrExport)
{
    $html = '';
    foreach($arrExport as $item) {
        $number = $item['menuIdx'] + 1;
        $idx = $item['menuIdx'];
        $produto = $item['product'];
        $thumb = $item['export']['thumb'];

        $html .= <<<HTML
            <div class="col-md-4 dv-thumb-export">
                <div class="custom-control custom-checkbox image-checkbox">
                    <input
                        type="checkbox"
                        class="custom-control-input"
                        data-idx="$idx"
                        id="ck$idx"
                    />
                    <label class="custom-control-label" for="ck$idx">
                        <img src="$thumb" alt="#" class="img-fluid" title="$produto">
                    </label>
                </div>
            </div>
        HTML;
    }

    return $html;
}

$config = $_SESSION['config'];
$arrExport = array_filter($config['pages'], function($val) {
    return is_array($val['export']);
});

if (isset($_GET['form']) && $_GET['form'] == 1) {
    echo formHtml($arrExport);
    return;
}
if (isset($_POST['send']) && $_POST['send'] == 1) {
    $ids = $_POST['ids'];
    $email = $_POST['email'];

    $arrSend = array_filter($arrExport, function($val) use ($ids) {
        return in_array($val['menuIdx'], $ids);
    });
    $arrFiles = [];
    foreach($arrSend as $item) {
        $arrFiles[] = $item['export']['file'];
    }

    echo sendEmail($email, 'Catálogo Seara', 'Oi<br />Segue anexo o documento requisitado. Obg!', $arrFiles);
    return;
}
?>

<div class="modal" tabindex="-1" role="dialog" id="emailModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar material</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div id="sending-body" class="modal-body" style="display:none;">
                <div style="text-align:center">
                    <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                    Enviando material. Aguarde, por favor ...
                </div>
            </div>

            <div id="main-body" class="modal-body">
                <div
                    class="alert alert-warning fade show"
                    role="alert"
                    style="display:none;"
                    id="alert-msg"
                >
                    <span id="message">
                        Erro!
                    </span>
                    <button type="button" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row">
                    <div class="col">
                        <input
                            type="email"
                            class="form-control mb-3"
                            name="inputEmail"
                            id="inputEmail"
                            placeholder="E-mail ..."
                            value=""
                        />
                    </div>
                </div>
                <div class="row export-items">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary submit">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    Enviar
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAlert(msg)
    {
        $('#alert-msg #message').html(`<b>Atenção</b>: ${msg}`);
        $('#alert-msg').fadeIn(350);

        setTimeout(() => {
            closeAlert();
        }, 4000);
    }

    function closeAlert()
    {
        $('#alert-msg').fadeOut(350);
    }

    $(document).on('click', '#alert-msg button.close', function(e) {
        closeAlert();
    });

    function validateEmail(email)
    {
        let validRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return validRegex.test(email);
    }

    // show.bs.modal, shown.bs.modal, hide.bs.modal, hidden.bs.modal
    $('#emailModal').on('shown.bs.modal', function (e) {
        // vars
        let currentPage = $('#dv-container').find('ul.menu-toc li.clickable.menu-toc-current').data('idx');
        let $exportItens = $('div.export-items');

        setTimeout(() => {
            $.get("email_modal.php?form=1", function(data, status) {
                $exportItens.html('<p style="text-align:center; width:100%;">Carregando ...</p>');
                $exportItens.html(data);

                setTimeout(() => {
                    let $imageCheckbox = $exportItens.find('div.image-checkbox');
                    $imageCheckbox.find(`input[data-idx='${currentPage}']`).parent().find('label').trigger('click');
                }, 175);
            });
        }, 200);
    });

    $('#emailModal').on('hide.bs.modal', function (e) {
        // re-display items
        $('#emailModal .modal-footer .submit').show();
        $('#emailModal #sending-body').hide();
        $('#emailModal #main-body').show();
    });

    $(document).on('click', '#emailModal .modal-footer button.submit', function(e) {
        let email = $(this).closest('div.modal-content').find('input#inputEmail').val();
        if (email == '') {
            showAlert('Preencha o e-mail!');
            return;
        }

        if (false === validateEmail(email)) {
            showAlert('Informe um e-mail válido!');
            return;
        }

        let items = $(this).closest('div.modal-content').find('input:checked');
        if (items.length <= 0) {
            showAlert('Nenhum produto selecionado!');
            return;
        }

        closeAlert();

        let idxs = [];
        items.each(function(idx, item){
            idxs.push($(item).data('idx'));
        });

        // send
        $.ajaxSetup({
            beforeSend: function(){
                $('#emailModal #main-body').hide();
                $('#emailModal #sending-body').show();
                $('#emailModal .modal-footer .submit').hide();
            },
            complete: function(data) {
                // clean setup
                $.ajaxSetup({
                    beforeSend: null,
                    complete: true
                });

                let response = data.responseText;
                let statusCode = data.status;
                let errorMsg = '';
                
                if (statusCode != 200) {
                    errorMsg = 'O servidor registrou um erro ao enviar o e-mail. Tente novamente mais tarde!';
                } else if (response == 0) {
                    errorMsg = 'Erro ao enviar o e-mail. Tente novamente mais tarde!';
                }

                if (errorMsg != '') {
                    $('#emailModal .modal-footer .submit').show();
                    $('#emailModal #sending-body').hide();
                    $('#emailModal #main-body').show();

                    showAlert(errorMsg);
                    return;
                }

                // all good
                $('#emailModal #modal-footer #submit').hide();
                $('#emailModal #main-body').hide();
                $('#emailModal #sending-body div').html('<i style="color:#4BB543" class="fa fa-check" aria-hidden="true"></i> Email enviado com sucesso!');
                $('#emailModal #sending-body').show();
            }
        });

        $.post("email_modal.php", {'send': 1, email: email, 'ids': idxs}, function(result) { });
    });
</script>