<?php
/**
 * Modal de confirmação de EXCLUSÃO (um por página).
 *
 * Os botões [data-delete] das telas abrem este modal (assets/js/dashboard/dashboard.js),
 * que coloca a URL do registro no action do formulário. A exclusão só acontece quando o
 * usuário confirma: o formulário envia POST + _method=DELETE + token CSRF.
 * Nunca se exclui por link GET.
 */
?>
<div class="adm-backdrop" data-confirm hidden>
    <section class="adm-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-text">
        <header>
            <div>
                <span class="adm-eyebrow"><i></i> confirmação</span>
                <h2 id="confirm-title">Excluir registro?</h2>
            </div>
            <button type="button" class="adm-modal-close" data-confirm-cancel aria-label="Fechar"><?= iconAdmin('x', 18) ?></button>
        </header>

        <form class="adm-modal-body" action="" method="post" data-confirm-form>
            <?= createCsrf() ?>
            <input type="hidden" name="_method" value="DELETE">

            <p id="confirm-text" data-confirm-text>Esta ação não pode ser desfeita.</p>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn adm-btn-secondary" data-confirm-cancel>Cancelar</button>
                <button type="submit" class="adm-btn adm-btn-danger"><?= iconAdmin('trash-2', 15) ?> Sim, excluir</button>
            </div>
        </form>
    </section>
</div>
