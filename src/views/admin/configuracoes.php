<?php
/**
 * Configurações da conta (GET /admin/configuracoes).
 * Editar: envia PUT /admin/configuracoes (MembroController::updateConta).
 * Excluir: envia DELETE /admin/configuracoes (MembroController::deleteConta), após confirmação.
 * Sempre atua na conta do membro logado.
 *
 * @var models\Membro $membro
 * @var array         $funcoes models\Funcao[]
 * @var array         $valores preenchidos com os dados atuais
 * @var array         $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <div>
                <span class="adm-eyebrow"><i></i> minha conta</span>
                <h2 class="adm-section-title">Seus dados de acesso.</h2>
            </div>
        </div>

        <?php
        $formAcao = '/admin/configuracoes';
        $formEdicao = true;
        require VIEWS . 'admin/membros/_form.php';
        ?>

        <section class="adm-card adm-danger-zone">
            <div>
                <h2>Zona de perigo</h2>
                <p>Excluir a sua conta remove o seu acesso ao painel e apaga a sua foto. Esta ação não pode ser desfeita.</p>
            </div>
            <button type="button" class="adm-btn adm-btn-danger" data-delete data-action="/admin/configuracoes" data-nome="a sua conta"><?= iconAdmin('trash-2', 15) ?> Excluir minha conta</button>
        </section>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
